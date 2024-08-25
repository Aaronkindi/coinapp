<?php
    session_start();
 ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="styles3.css">
</head>
<body>
    <div class="container">
        <div class="image-section">
            <img src="images/R.jpg" width="500px" height="500px" alt="Trade With Confidence">
        </div>
        <div class="form-section">
            <div class="form-container">
                <ul class="tabs">
                  <li class="tab"> <a href="signup.html">Sign Up</a></li>
                   <li class="tab active"> <a href="loginform.php">Login</a></li> 
                </ul>
                <form action="login.php" method="POST">
                    <div class="input-group">
                        <label for="email">
                            <img src="images/email_2099199.png" width="15px" height="15px" alt="Email Icon">
                            <input type="email" id="email" name="email" placeholder="Email" required>
                        </label>
                    </div>
                    <div class="input-group">
                        <label for="password">
                            <img src="images/lock_3917642.png" width="15px" height="15px" alt="Password Icon">
                            <input type="password" id="password" name="password" placeholder="Password" required>
                        </label>
                    </div>
                    <button type="submit">Sign In</button>
                </form>
                
                <div class="social-login">
                    <p>Or</p>
                    <div class="social-icons">
                        <img src="images/google_13170545.png" width="30px" height="30px" alt="Google">
                        <img src="images/facebook_5968764.png" width="20px" height="30px" alt="Facebook">
                        <img src="images/apple_831329.png" width="30px" height="30px"  alt="Apple">
                    </div>
                </div>
                <p class="privacy-text">
                    By creating an account, you agree to the <a href="#">privacy policy</a> and to receive economic and marketing communications from AvaTrade. You can remove yourself from the mailing list at any time.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
