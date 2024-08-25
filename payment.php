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
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_SESSION['email']; // Get the logged-in user's email
    $amount = $_POST['amount'];
    $currency = $_POST['currency'];

    // Validate form inputs
    if (empty($amount) || !is_numeric($amount)) {
        echo "Please enter a valid amount.";
    } elseif (empty($currency)) {
        echo "Please select a currency.";
    } else {
        // Prepare the SQL statement to insert the payment
        $sql = "INSERT INTO payments (email, amount, currency) VALUES (?, ?, ?)";
        
        if ($stmt = $con->prepare($sql)) {
            // Bind the parameters
            $stmt->bind_param('sds', $email, $amount, $currency); // Corrected types and variable
            
            // Execute the statement
            if ($stmt->execute()) {
                echo "Payment successful!";
            } else {
                echo "Error: " . $stmt->error;
            }
            
            // Close the statement
            $stmt->close();
        } else {
            echo "Error preparing the statement: " . $con->error; // Use $con instead of $conn
        }
    }
}

// Close the database connection
$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <link rel="stylesheet" href="style4.css">
</head>
<body>
    <div class="payment-container">
        <h2>Payment Page</h2>
        <form action="payment.php" method="POST">
            <label for="amount">Enter Amount</label>
            <input type="text" id="amount" name="amount" placeholder="Enter amount">

            <label for="currency">Select Currency</label>
            <select id="currency" name="currency">
                <option value="ZAR">Rand (ZAR)</option>
                <option value="USD">Dollar (USD)</option>
                <option value="GBP">Pound (GBP)</option>
                <option value="EUR">Euro (EUR)</option>
                <option value="JPY">Yen (JPY)</option>
                <option value="KRW">Won (KRW)</option>
                <option value="CNY">Yuan (CNY)</option>
            </select>

            <button type="submit">Make Payment</button>
        </form>
    </div>
</body>
</html>
