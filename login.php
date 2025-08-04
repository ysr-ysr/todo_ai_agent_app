<?php
session_start();
include 'config/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = htmlspecialchars($_POST['email']);
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();

  $result = $stmt->get_result();
  $user = $result->fetch_assoc();

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['user_id'];
    header("Location: dashboard.php");
    exit();
  } else {
    $error = "Email or password false.";
  }

  $stmt->close();
  $conn->close();
}
?>

<!DOCTYPE html>
<header>
  <link rel="stylesheet" href="assets/mystyle.css">
  <link rel="icon" href="assets/images/favicon/favicon.ico" type="image/x-icon">

  <style>
    body {
      /* background-image: url("assets/images/téléchargé\ \(11\).jpeg"); */
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
      /* border: 1px solid #ccc; */
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
      color: #f5e1e1ff;
    }

    button::placeholder {
      color: #cbaba3ff;
    }

    input:focus {
      border-color: #4b0f0fff;
      outline: none;
      color: #f5e1e1ff;
      background-color: #ffffff17;
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
      color: #200c06ff;
    }

    .error-msg .error {
      height: 20px;
      width: 100%;
      background: #d88383ff;
      padding: 10px;
      color: #792121ff;
      border-radius: 10px;
      display: flex;
      text-align: center;
      justify-content: center;


    }

    input::placeholder {
      color: #fff5f2a3;
    }

  </style>
</header>

<body>
  <div class="login-box">

    <?php if ($error): ?>
      <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="post">
      <h2>Login</h2>
      <label>Email :</label><br>
      <input id="email" type="email" name="email" placeholder="Email" required><br><br>

      <label>Password :</label><br>
      <input id="password" type="password" name="password" placeholder="Password" required><br>

      <div class="error-msg">
        <?php if ($error): ?>
          <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
      </div> <br>

      <button id="btn" type="submit">
        Login
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
      <h5>Don't have an account ? <a style="color: #4b0f0fff; text-decoration: none;" href="register.php">Register</a></h5>


    </form>

  </div>


  <!-- <script>
    let pass = document.getElementById("password").value;
    let email = document.getElementById("email").value;
    let btn = document.getElementById("btn");

    btn.addEventListener("click",()=> {
      <?php if ($error): ?>
          document.querySelectorAll('input').style.border = "1px solid red";
        <?php endif; ?>
    });

    


  </script> -->
</body>

</html>