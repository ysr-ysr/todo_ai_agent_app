<?php
include 'config/db.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = htmlspecialchars($_POST["username"]);
  $email = htmlspecialchars($_POST["email"]);
  $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

  $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $username, $email, $password);

  if ($stmt->execute()) {

    $result = "Inscription réussie !";
  } else {
    echo $stmt->error;
    $result = "Registration failed.";
  }

  $stmt->close();
  $conn->close();
}
?>


<!DOCTYPE html>
<header>
  <link rel="icon" href="assets/images/favicon/favicon.ico" type="image/x-icon">


  <link rel="stylesheet" href="assets/mystyle.css">
  <style>
    body {
      background-image: url("assets/images/téléchargé\ \(9\).jpeg");
      background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            
            background-position: center;
      font-family: Verdana;
      height: fit-content;

    }

    form {
      max-width: 400px;
      margin: auto;
      padding: 20px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      border: 1px solid transparent;
      border-radius: 5px;
      display: flex;
      flex-direction: column;
      border-radius: 10px;
      backdrop-filter: blur(10px);
      /* flou de 10px */
      /* align-items: center; */
      /* justify-content: center; */
      text-align: left;
      color: #333;
      margin-top: 100px;
      background-color: transparent;

    }

    input {
      width: 90%;
      height: 30px;
      padding: 0 15px;
      background-color: transparent;
      backdrop-filter: blur(20px);
      /* flou de 10px */

      border: 2px solid #b58787ff;
      border-radius: 40px;
      /* outline: none; */
      color: white;
      font-size: 18px;
      display: flex;
      color: #4b0f0fff;
    }

    input:focus {
      border-color: #4b0f0fff;
      outline: none;
      color: #4b0f0fff;
      background-color: #c1c1c19a;
    }

    label {
      margin-bottom: 5px;
      font-weight: bold;
      color: #1f0707ff;

      text-align: left;
      display: block;
    }

    form h2 {
      text-align: center;
      font-size: x-large;
      margin-bottom: 20px;
      color: #1f0707ff;
    }

    h5 {
      text-align: center;
      margin-top: 20px;
      color: #1f0707ff;
    }

    input::placeholder {
      color: #f8c2bb;
    }


    
  </style>
</header>

<form method="post">

  <h2>Register</h2>
  <label>Username :</label><br>
  <input type="text" name="username" placeholder="User name" required><br><br>
  <label>Email :</label><br>
  <input type="email" name="email" placeholder="Email" required><br><br>

  <label>Password :</label><br>
  <input type="password" name="password" placeholder="Password" required><br><br>

  <button type="submit">
    Sign in
    <div class="star-1">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        xml:space="preserve"
        version="1.1"
        style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd"
        viewBox="0 0 784.11 815.53"
        xmlns:xlink="http://www.w3.org/1999/xlink">
        <defs></defs>
        <g id="Layer_x0020_1">
          <metadata id="CorelCorpID_0Corel-Layer"></metadata>
          <path
            class="fil0"
            d="M392.05 0c-20.9,210.08 -184.06,378.41 -392.05,407.78 207.96,29.37 371.12,197.68 392.05,407.74 20.93,-210.06 184.09,-378.37 392.05,-407.74 -207.98,-29.38 -371.16,-197.69 -392.06,-407.78z"></path>
        </g>
      </svg>
    </div>
    <div class="star-2">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        xml:space="preserve"
        version="1.1"
        style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd"
        viewBox="0 0 784.11 815.53"
        xmlns:xlink="http://www.w3.org/1999/xlink">
        <defs></defs>
        <g id="Layer_x0020_1">
          <metadata id="CorelCorpID_0Corel-Layer"></metadata>
          <path
            class="fil0"
            d="M392.05 0c-20.9,210.08 -184.06,378.41 -392.05,407.78 207.96,29.37 371.12,197.68 392.05,407.74 20.93,-210.06 184.09,-378.37 392.05,-407.74 -207.98,-29.38 -371.16,-197.69 -392.06,-407.78z"></path>
        </g>
      </svg>
    </div>
    <div class="star-3">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        xml:space="preserve"
        version="1.1"
        style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd"
        viewBox="0 0 784.11 815.53"
        xmlns:xlink="http://www.w3.org/1999/xlink">
        <defs></defs>
        <g id="Layer_x0020_1">
          <metadata id="CorelCorpID_0Corel-Layer"></metadata>
          <path
            class="fil0"
            d="M392.05 0c-20.9,210.08 -184.06,378.41 -392.05,407.78 207.96,29.37 371.12,197.68 392.05,407.74 20.93,-210.06 184.09,-378.37 392.05,-407.74 -207.98,-29.38 -371.16,-197.69 -392.06,-407.78z"></path>
        </g>
      </svg>
    </div>
    <div class="star-4">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        xml:space="preserve"
        version="1.1"
        style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd"
        viewBox="0 0 784.11 815.53"
        xmlns:xlink="http://www.w3.org/1999/xlink">
        <defs></defs>
        <g id="Layer_x0020_1">
          <metadata id="CorelCorpID_0Corel-Layer"></metadata>
          <path
            class="fil0"
            d="M392.05 0c-20.9,210.08 -184.06,378.41 -392.05,407.78 207.96,29.37 371.12,197.68 392.05,407.74 20.93,-210.06 184.09,-378.37 392.05,-407.74 -207.98,-29.38 -371.16,-197.69 -392.06,-407.78z"></path>
        </g>
      </svg>
    </div>
    <div class="star-5">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        xml:space="preserve"
        version="1.1"
        style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd"
        viewBox="0 0 784.11 815.53"
        xmlns:xlink="http://www.w3.org/1999/xlink">
        <defs></defs>
        <g id="Layer_x0020_1">
          <metadata id="CorelCorpID_0Corel-Layer"></metadata>
          <path
            class="fil0"
            d="M392.05 0c-20.9,210.08 -184.06,378.41 -392.05,407.78 207.96,29.37 371.12,197.68 392.05,407.74 20.93,-210.06 184.09,-378.37 392.05,-407.74 -207.98,-29.38 -371.16,-197.69 -392.06,-407.78z"></path>
        </g>
      </svg>
    </div>
    <div class="star-6">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        xml:space="preserve"
        version="1.1"
        style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd"
        viewBox="0 0 784.11 815.53"
        xmlns:xlink="http://www.w3.org/1999/xlink">
        <defs></defs>
        <g id="Layer_x0020_1">
          <metadata id="CorelCorpID_0Corel-Layer"></metadata>
          <path
            class="fil0"
            d="M392.05 0c-20.9,210.08 -184.06,378.41 -392.05,407.78 207.96,29.37 371.12,197.68 392.05,407.74 20.93,-210.06 184.09,-378.37 392.05,-407.74 -207.98,-29.38 -371.16,-197.69 -392.06,-407.78z"></path>
        </g>
      </svg>
    </div>
  </button>

  <h5>Already have an account ? <a style="color: #4b0f0fff; text-decoration: none;" href="login.php">Login</a></h5>

</form>