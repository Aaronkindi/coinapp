<?php
// Start the session
session_start();

// Database connection
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'coin';

$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Initialize variables
$response = [
    'balance' => 0.00,
    'currency' => 'USD',
    'portfolio' => []
];

// Check if the user email is stored in the session
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    // Query to fetch the total balance from the `payments` table
    $stmt = $con->prepare("SELECT SUM(amount) AS total_balance, currency FROM payments WHERE email = ? GROUP BY currency");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $response['balance'] = $row['total_balance'];
        $response['currency'] = $row['currency'];
    }

    $stmt->close();

    // Fetch portfolio data
    $stmt = $con->prepare("SELECT cryptoname, amount, cryptoprice FROM portfolio WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $response['portfolio'][] = $row;
    }

    $stmt->close();
}

$con->close();

// Encode the portfolio data for use in JavaScript
$portfolioData = json_encode($response['portfolio']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="sidebar">
        <div class="top">
            <div class="logo">
                <i class="bx bxl-codepen"></i>
                <span>CapitalEdge</span>
            </div>
        </div>
        <i class="bx bx-menu" id="btn"></i>
        <div class="user">
            <img src="images/people_14024721.png" alt="user" class="user-img">
            <div class="email">
                <p class="bold">
                <?php
                if (isset($_SESSION['email'])) {
                    echo htmlspecialchars($_SESSION['email']);
                } else {
                    echo 'Guest';
                }
                ?>
                </p>
            </div>
        </div>

        <ul>
            <li>
                <a href="#">
                    <i class="bx bx-grid-alt "></i>
                    <span class="nav-item">Dashboard</span>
                </a>
                <span class="tootip">Dashboard</span>
            </li>
            <li>
                <a href="position.php">
                    <i class="bx bx-wallet "></i>
                    <span class="nav-item">Positions</span>
                </a>
                <span class="tootip">Positions</span>
            </li>
            <li>
                <a href="history.php">
                    <i class="bx bx-history "></i>
                    <span class="nav-item">History</span>
                </a>
                <span class="tootip">History</span>
            </li>
            <li>
                <a href="#">
                    <i class="bx bx-cog "></i>
                    <span class="nav-item">Settings</span>
                </a>
                <span class="tootip">Settings</span>
            </li>
            <li>
                <a href="logout.php">
                    <i class="bx bx-log-out "></i>
                    <span class="nav-item">Logout</span>
                </a>
                <span class="tootip">Logout</span>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div id="heading">
            <h3>CapitalEdge</h3>
            <button id="depositButton" onclick="redirectTopage()">Deposit</button>
            <div class="currency-selector">
                <label for="currency">Select Currency:</label>
                <select id="currency" name="currency">
                    <option value="USD">USD</option>
                    <option value="EUR">EUR</option>
                    <option value="GBP">GBP</option>
                    <option value="ZAR">ZAR</option>
                    <option value="JPY">JPY</option>
                </select>
            </div>
            <div>
                <ul id="navbar">
                    <div class="search-container">
                        <input type="text" id="searchInput" class="search-input" placeholder="Search...">
                        <button class="search">
                            <i class="bx bx-search-alt "></i>
                        </button>
                    </div>
                    <div class="balance">
                        <p>Balance: <span><?php echo number_format($response['balance'], 2); ?> <?php echo strtoupper($response['currency']); ?></span></p>
                    </div>
                </ul>
            </div>
        </div>
        <div id="crypto">
            <h6>TOP STAKING ASSETS</h6>
            <div class="assets-container"></div>
            <div class="carousel-indicators">
                <span class="dot active" data-page="0"></span>
                <span class="dot" data-page="1"></span>
                <span class="dot" data-page="2"></span>
            </div>
        </div>

        <div id="portfolio">
            <p class="currency-symbol"><?php echo htmlspecialchars($response['currency']); ?></p>
        </div>

        <div id="crypto-port">
            <h6>POSITIONS</h6>
            <div class="assets-container-portfolio" id="portfolio-data">
                <!-- Portfolio data will be dynamically inserted here -->
            </div>
            <div class="carousel-indicators">
                <span class="dot active"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>
        </div>
    </div>

<script>
// Pass PHP portfolio data to JavaScript
const portfolioData = <?php echo $portfolioData; ?>;

let btn = document.querySelector('#btn');
let sidebar = document.querySelector('.sidebar');

btn.onclick = function() {
    sidebar.classList.toggle('active');
};

function redirectTopage() {
    window.location.href = "payment.php";
}

function updateCurrencyPreference(currency) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "update_currency.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log(xhr.responseText);
        }
    };
    xhr.send("currency=" + currency);
}
</script>

<script src="script.js"></script>

</body>
</html>
