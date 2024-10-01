<?php
session_start();

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'coin';

$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_SESSION['email']; // Get the logged-in user's email
    $amount = $_POST['amount']; // The amount of cryptocurrency the user wants to buy
    $crypto_name = $_POST['crypto-name']; 
    $crypto_price = $_POST['crypto-price']; // The price of the selected cryptocurrency

    if (empty($amount) || !is_numeric($amount)) {
        echo "Please enter a valid amount.";
    } else {
        // Retrieve all the user's deposited amounts from the `payments` table
        $query = "SELECT id, amount FROM payments WHERE email = ? ORDER BY id ASC";
        $stmt = $con->prepare($query);

        if (!$stmt) {
            die('Query preparation failed: ' . $con->error);
        }

        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        $total_amount = 0;
        $deposits = [];
        
        while ($row = $result->fetch_assoc()) {
            $total_amount += $row['amount'];
            $deposits[] = $row;
        }

        if ($total_amount === null || $total_amount == 0) {
            echo "No funds available.";
        } elseif ($amount > $total_amount) {
            echo "Insufficient funds. You only have $total_amount available.";
        } else {
            // Proceed with the transaction
            $purchase_value = $amount;
            $remaining_value = $purchase_value;

            // Loop through deposits and deduct from each until the full purchase is deducted
            foreach ($deposits as $deposit) {
                if ($remaining_value <= 0) break;

                $deposit_id = $deposit['id'];
                $deposit_amount = $deposit['amount'];

                if ($deposit_amount >= $remaining_value) {
                    // Deduct the remaining value from this deposit
                    $new_deposit_amount = $deposit_amount - $remaining_value;
                    $remaining_value = 0;
                } else {
                    // Use the full deposit amount and reduce the remaining value
                    $new_deposit_amount = 0;
                    $remaining_value -= $deposit_amount;
                }

                // Update the deposit in the database
                $update_query = "UPDATE payments SET amount = ? WHERE id = ?";
                $stmt = $con->prepare($update_query);

                if (!$stmt) {
                    die('Update query preparation failed: ' . $con->error);
                }

                $stmt->bind_param('di', $new_deposit_amount, $deposit_id);
                $stmt->execute();
            }

            // Insert the transaction into the `portfolio` table
            $insert_query = "INSERT INTO portfolio (email, cryptoname, cryptoprice, amount) VALUES (?, ?, ?, ?)";
            $stmt = $con->prepare($insert_query);

            if (!$stmt) {
                die('Insert query preparation failed: ' . $con->error);
            }

            $stmt->bind_param('ssdd', $email, $crypto_name, $crypto_price, $amount);

            if ($stmt->execute()) {
                echo "Transaction successful. You have bought $amount $crypto_name.";
            } else {
                echo "Failed to record the transaction. Please try again.";
            }
        }
        $stmt->close();

        // insert the transaction into the 'transaction history' table
        $insert_query = "INSERT INTO transactions (email, cryptoname, cryptoprice, amount) VALUES (?, ?, ?, ?)";
        $stmt = $con->prepare($insert_query);

        if (!$stmt) {
            die('Insert query peparation failed:'  . $con->error);   
        }

        $stmt->bind_param('ssdd', $email, $crypto_name, $crypto_price, $amount);

        if ($stmt->execute()) {
            echo "Transaction successful. You have bought $amount $crypto_name.";
        } else {
            echo "Failed to record the transaction. Please try again.";
        }

        


    }
}

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction</title>
    <link rel="stylesheet" href="styles5.css">
</head>
<body>
    <!-- Back Button -->
    <a href="dashboard.php" class="back-button">← Back</a>

    <div class="transaction-container">
        <h1>Buy Cryptocurrency</h1>
        <div class="crypto-details">
            <img id="crypto-logo" src="" alt="Crypto Logo" class="crypto-logo">
            <h2 id="display-crypto-name"></h2>
            <p id="display-crypto-symbol"></p>  
            <p id="display-crypto-price"></p>
        </div>

        <form id="buy-form" action="buycoin.php" method="post">
            <label for="amount">Amount to Buy:</label>
            <input type="number" id="amount" name="amount" min="1" required>
        
            <!-- Hidden fields to store crypto details for submission -->
            <input type="hidden" id="crypto-name-field" name="crypto-name">
            <input type="hidden" id="crypto-symbol-field" name="crypto-symbol">
            <input type="hidden" id="crypto-price-field" name="crypto-price">
        
            <button type="submit">Buy Now</button>
        </form>
        
    </div>

    <script>
        // Function to get query parameters from URL
        function getQueryParams() {
            const params = new URLSearchParams(window.location.search);
            return {
                symbol: params.get('symbol'),
                name: params.get('name'),
                price: parseFloat(params.get('price')).toFixed(2)
            };
        }

        // Function to display cryptocurrency details on the page
        function displayCryptoDetails() {
            const { symbol, name, price } = getQueryParams();

            document.getElementById('display-crypto-name').innerText = name;
            document.getElementById('display-crypto-symbol').innerText = `Symbol: ${symbol}`;
            document.getElementById('display-crypto-price').innerText = `Price: $${price}`;
            document.getElementById('crypto-logo').src = `https://cryptoicons.org/api/icon/${symbol.toLowerCase()}/200`;
            document.getElementById('crypto-logo').alt = `${name} logo`;

            // Set hidden fields for form submission
            document.getElementById('crypto-name-field').value = name;
            document.getElementById('crypto-symbol-field').value = symbol;
            document.getElementById('crypto-price-field').value = price;
        }

        // Initialize the page with the cryptocurrency details
        document.addEventListener('DOMContentLoaded', () => {
            displayCryptoDetails();
        });
    </script>

</body>
</html>
