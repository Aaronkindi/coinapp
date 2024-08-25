<?php
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'coin';

// Connect to the database
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Check if the form data is set
if (!isset($_POST['email'], $_POST['password'])) {
    exit('Please fill out the form!');
}

// Check if email and password are not empty
if (empty($_POST['email']) || empty($_POST['password'])) {
    exit('Please complete the registration form!');
}

$email = $_POST['email'];
$password = $_POST['password'];

// Check if the email already exists
// Insert the new user into the database without hashing the password
if ($stmt = $con->prepare('INSERT INTO users (password, email) VALUES (?, ?)')) {
    $stmt->bind_param('ss', $password, $email);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header('Location: loginform.php');
        exit();
    } else {
        echo 'Registration failed. Please try again.';
    }

    $stmt->close(); // Closing the statement
} else {
    // Log the error
    echo 'Could not prepare statement! Error: ' . htmlspecialchars($con->error);
}


$con->close();
?>
