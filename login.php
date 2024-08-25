<?php
session_start(); // Start the session

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

$email = $_POST['email'];
$password = $_POST['password'];

// Check if the email exists
if ($stmt = $con->prepare('SELECT id, password FROM users WHERE email = ?')) {
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $stored_password);
        $stmt->fetch();
        
        // Verify password
        if ($password === $stored_password) {
            session_regenerate_id();
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = $email;
            $_SESSION['id'] = $id;
            header('Location: dashboard.php');
            exit();
        } else {
            echo 'Incorrect password!';
        }
    } else {
        echo 'Incorrect email!';
    }

    $stmt->close();
} else {
    echo 'Could not prepare statement!';
}

$con->close();
?>
