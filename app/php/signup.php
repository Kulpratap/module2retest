<?php
if (($_SESSION['loggedin'] == true)) {
  header('Location:/home');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="../app/styles/signup.css">
  <title>Sign Up</title>
</head>

<body>
  <section class="center-container">
    <section class="image-container">
      <img src="../app/images/bg_signup.jpg" alt="">
    </section>

    <section class="container">
      <form action="CheckSignup" method='post' class="name-form" id='myForm'>
        <h1 id="page-heading">Please Register</h1>
        <div class="input-wrapper">
          <input type="text" name="fname" id="fname" placeholder="First Name" required onclick=>
          <input type="text" name="lname" id="lname" placeholder="Last Name">
        </div>
        <span id="name-error"></span>
        <div class="input-wrapper">
          <label for="username">
            <i class="fa-solid fa-user"></i>
          </label>
          <input type="text" name="username" id="username" required placeholder='Ex: name123'>
        </div>
        <div class="input-wrapper">
          <label for="password">
            <i class="fa-solid fa-lock icon"></i>
          </label>
          <input type="password" name="password" id="password" required placeholder="Enter your Password">
          <span class="eye-icon" onclick="togglePasswordVisibility('password')">
            <i class="fa-solid fa-eye hide-btn"></i>
          </span>
        </div>
        <div class="input-wrapper">
          <label for="password">
            <i class="fa-solid fa-lock icon"></i>
          </label>
          <input type="password" name="confirm_password" id="repassword" required placeholder="Confirm your Password">
          <span class="eye-icon" onclick="togglePasswordVisibility('repassword')">
            <i class="fa-solid fa-eye hide-btn"></i>
          </span>
        </div>
        <span id="passwordError" style="color: red; display: none;">Passwords do not match!</span>
        <div class="input-wrapper">
          <label for="email">
            <i class="fa-solid fa-envelope"></i>
          </label>
          <input type="email" name="email" id="email" placeholder="abc@example.com">
        </div>
        <input type="submit" value="Sign Up" name='submit'>
        <span class='signup-link'>Already registered?
          <a href="/login">Login from here</a>.</span>
      </form>
    </section>
  </section>
  <script src="../app/script/signup.js"></script>

</body>
</html>