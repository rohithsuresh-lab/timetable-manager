<?php
// Optional: session_start(); if you plan to use sessions later
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <link rel="stylesheet" href="signin.css" /> <!-- Make sure this path is correct -->
</head>
<body>
  <div class="container">
    <h2>Login</h2>

    <form action="validate_login.php" method="POST">
      <label>Email</label>
      <input type="email" name="email" placeholder="Enter your email" required />
      
      <label>Password</label>
      <input type="password" name="password" placeholder="Enter your password" required />
      
      <div class="options">
        <a href="#">Forgot Password</a>
      </div>
      
      <button type="submit" class="submit-btn">Login</button>
    </form>
    
    <p class="signin-link">Don't have an account? <a href="#">Sign Up</a></p>
  </div>
</body>
</html>
