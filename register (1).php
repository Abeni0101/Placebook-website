<?php
include 'includes/autoloader.inc.php';
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
      $email = $_POST['email'];
      $password = $_POST['password'];
      $fullname = $_POST['fullname'];
      $country = $_POST['location'];
      $profile_image= null;
      $controler = new Controller();
      $sucess = $controler->registerUser($fullname, $email, $password, $profile_image, $country);
      if($sucess)
      {
        header('location: login.php');
        exit;
      }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./public/css/lo.css">
</head>
<body>
    <div class="login-container">
        <div class="left-section">
          <div class="logo-background"></div>
          <!-- <h1>Get Started</h1> -->
        </div>
        <div class="right-section" id="rightSection">
          <div class="form-container">
            <h2>Create Account</h2>
            <form method="POST" action="">
              <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" id="fullname" name="fullname" placeholder="Enter your Full name" required>
              </div>
              <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Enter your email" required>
              </div>
              <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
              </div>
              <div class="form-group">
                <label for="location">Where are you from</label>
                <input type="text" name="location" id="location" placeholder="Enter your country" required>
              </div>
              <button type="submit" name="submit" class="btn-primary">Sign Up</button>
              <div class="links">
                <a href="login.php">Have an account?</a>
              </div>
            </form>
          </div>
        </div>
      </div>
      
</body>
</html>