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

if (isset($_SESSION['email']) && isset($_POST['currency'])) {
    $email = $_SESSION['email'];
    $currency = $_POST['currency'];

    // Update the user's currency preference in the database
    $stmt = $con->prepare("UPDATE payments SET currency = ? WHERE email = ?");
    $stmt->bind_param('ss', $currency, $email);
    $stmt->execute();
    $stmt->close();
    echo "Currency updated successfully";
} else {
    echo "Failed to update currency";
}

$con->close();
?>
