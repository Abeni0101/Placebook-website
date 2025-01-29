<?php
include_once 'includes/autoloader.inc.php';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $email = $_POST['email'];
  $pass = $_POST['password'];
  $view = new Controller();
  $error = $view->loginUser($email,$pass);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up</title>
  <link rel="stylesheet" href="./public/css/lo.css">
</head>
<body>
    <div class="login-container">
        <div class="left-section">
          <div class="logo-background"></div>
          <!-- <h1>Get Started</h1> -->
        </div>
        <div class="right-section active">
          <div class="form-container">
            <h2>Log In</h2>
            <form method="post" action="">
              <?php $error ?>
              <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Enter your Email">
              </div>
              <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter your password">
              </div>
              <button type="submit" name="submit" class="btn-primary">Sign In</button>
              <div class="links">
                <a href="register.php">Not a Member yet?</a>
              </div>
            </form>
          </div>
        </div>
      </div>



<script src="../../public/js/register.js"></script>      
</body>
</html>
