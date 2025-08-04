<?php
session_start();
include 'config/db.php';
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username);
$stmt->fetch();
$stmt->close();
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Vérifier si l'ID de la tâche est présent dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$task_id = intval($_GET['id']);
$error = "";
$success = "";

// Si formulaire soumis (méthode POST) => mise à jour de la tâche
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task'])) {
    $task = trim($_POST['task']);

    if ($task === "") {
        $error = "The task name should not be empty.";
    } else {
        // Mettre à jour la tâche dans la base
        $stmt = $conn->prepare("UPDATE tasks SET task = ? WHERE task_id = ? AND user_id = ?");
        $stmt->bind_param("sii", $task, $task_id, $user_id);
        if ($stmt->execute()) {
            $success = "Task updated.";
            // Redirection après mise à jour (optionnel)
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Error.";
        }
        $stmt->close();
    }
}

// Récupérer la tâche actuelle pour pré-remplir le formulaire
$stmt = $conn->prepare("SELECT task FROM tasks WHERE task_id = ? AND user_id = ?");
$stmt->bind_param("ii", $task_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Tâche non trouvée ou pas autorisé
    $stmt->close();
    header("Location: dashboard.php");
    exit();
}

$taskData = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <title>Update Task</title>
    <link rel="stylesheet" href="assets/mystyle.css">
  <link rel="icon" href="assets/images/favicon/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            /* background-image: url("assets/images/bg14.jpeg"); */
            background-color: rgb(247, 250, 252);

        }

        form {
            max-width: 400px;
            margin: auto;
        }

        label,
        input,
        button {
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }

        input[type="text"] {
            padding: 8px;
            font-size: 1em;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }

        a {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            color: #007BFF;
        }

        .btn2 {
            position: relative;
            display: inline-block;
            padding: 15px 30px;
            border: 2px solid #fefefe;
            text-transform: uppercase;
            color: #fefefe;
            text-decoration: none;
            font-weight: 600;
            font-size: 20px;
            transition: 0.3s;
            background-color: transparent;
            cursor: pointer;
            width: 300px;
            margin: 20px auto;
        }

        .btn2::before {
            content: "";
            position: absolute;
            top: -2px;
            left: -2px;
            width: calc(100% + 6px);
            height: calc(100% + 2px);
            background-color: #212121;
            transition: 0.3s ease-out;
            transform: scaleY(1);
        }

        .btn2::after {
            content: "";
            position: absolute;
            top: -2px;
            left: -2px;
            width: calc(100% + 4px);
            height: calc(100% - 50px);
            background-color: #212121;
            transition: 0.3s ease-out;
            transform: scaleY(1);
        }

        .btn2:hover::before {
            transform: translateY(-25px);
            height: 0;
        }

        .btn2:hover::after {
            transform: scaleX(0);
            transition-delay: 0.15s;
        }

        .btn2:hover {
            border: 2px solid #fefefe;
        }

        .btn2 .spn2 {
            position: relative;
            z-index: 3;
            text-decoration: none;
            border: none;
            background-color: transparent;
        }

        /* button style  */
        button {
            position: relative;
            padding: 12px 35px;
            background: #e5bfb8;
            font-size: 17px;
            font-weight: 500;
            color: #f4f5f5ff;
            border: 3px solid #e5bfb8;
            border-radius: 8px;
            box-shadow: 0 0 0 #e5bfb88c;
            transition: all 0.3s ease-in-out;
            cursor: pointer;
        }

        .star-1 {
            position: absolute;
            top: 20%;
            left: 20%;
            width: 25px;
            height: auto;
            filter: drop-shadow(0 0 0 #fffdef);
            z-index: -5;
            transition: all 1s cubic-bezier(0.05, 0.83, 0.43, 0.96);
        }

        .star-2 {
            position: absolute;
            top: 45%;
            left: 45%;
            width: 15px;
            height: auto;
            filter: drop-shadow(0 0 0 #fffdef);
            z-index: -5;
            transition: all 1s cubic-bezier(0, 0.4, 0, 1.01);
        }

        .star-3 {
            position: absolute;
            top: 40%;
            left: 40%;
            width: 5px;
            height: auto;
            filter: drop-shadow(0 0 0 #fffdef);
            z-index: -5;
            transition: all 1s cubic-bezier(0, 0.4, 0, 1.01);
        }

        .star-4 {
            position: absolute;
            top: 20%;
            left: 40%;
            width: 8px;
            height: auto;
            filter: drop-shadow(0 0 0 #fffdef);
            z-index: -5;
            transition: all 0.8s cubic-bezier(0, 0.4, 0, 1.01);
        }

        .star-5 {
            position: absolute;
            top: 25%;
            left: 45%;
            width: 15px;
            height: auto;
            filter: drop-shadow(0 0 0 #fffdef);
            z-index: -5;
            transition: all 0.6s cubic-bezier(0, 0.4, 0, 1.01);
        }

        .star-6 {
            position: absolute;
            top: 5%;
            left: 50%;
            width: 5px;
            height: auto;
            filter: drop-shadow(0 0 0 #fffdef);
            z-index: -5;
            transition: all 0.8s ease;
        }

        button:hover {
            background: transparent;
            color: #e5bfb8;
            box-shadow: 0 0 25px #e5bfb8;
        }

        button:hover .star-1 {
            position: absolute;
            top: -80%;
            left: -30%;
            width: 25px;
            height: auto;
            filter: drop-shadow(0 0 10px #fffdef);
            z-index: 2;
        }

        button:hover .star-2 {
            position: absolute;
            top: -25%;
            left: 10%;
            width: 15px;
            height: auto;
            filter: drop-shadow(0 0 10px #fffdef);
            z-index: 2;
        }

        button:hover .star-3 {
            position: absolute;
            top: 55%;
            left: 25%;
            width: 5px;
            height: auto;
            filter: drop-shadow(0 0 10px #fffdef);
            z-index: 2;
        }

        button:hover .star-4 {
            position: absolute;
            top: 30%;
            left: 80%;
            width: 8px;
            height: auto;
            filter: drop-shadow(0 0 10px #fffdef);
            z-index: 2;
        }

        button:hover .star-5 {
            position: absolute;
            top: 25%;
            left: 115%;
            width: 15px;
            height: auto;
            filter: drop-shadow(0 0 10px #fffdef);
            z-index: 2;
        }

        button:hover .star-6 {
            position: absolute;
            top: 5%;
            left: 60%;
            width: 5px;
            height: auto;
            filter: drop-shadow(0 0 10px #fffdef);
            z-index: 2;
        }

        .fil0 {
            fill: #fffdef;
        }

        .column {
            width: 40%;
            margin: auto;
            padding: 10px;

        }

        input {
            width: 100%;
            height: 30px;
            padding: 0 15px;
            background-color: transparent;
            backdrop-filter: blur(20px);
            /* flou de 10px */

            border: 2px solid white;
            border-radius: 40px;
            /* outline: none; */
            color: white;
            font-size: 18px;
            display: flex;
        }

        input:focus {
            border-color: #e5bfb8;
            outline: none;
            color: #3c4855ff;
        }


        .column-edita {
            background: #d6c8c58c;
            /* blanc transparent */
            backdrop-filter: blur(10px);
            /* flou de 10px */
            font-family: Verdana, Geneva, Tahoma, sans-serif;
            color: #333333;
            text-align: center;
            border-radius: 10px;
            border: none;
            width: 600px;
            padding: 20px;
            border: 1px solid #ccc;
            padding: 10px;
            flex: 1;
            height: 300px;
            border: #bda7a7ff dashed;

        }

        .container,
        .back {
            display: flex;
            gap: 20px;
            padding: 20px;
            margin-top: 70px;
        }


        .back {
            margin: auto;
            width: 30%;
            background: #d6c8c58c;
            /* blanc transparent */
            backdrop-filter: blur(10px);
            margin-top: 50px;
            text-align: center;
            text-align: center;
            border: #bda7a7ff dashed;
            border-radius: 10px;
        }

        .back a {

            color: #754242ff;
            margin-left: 10px;
            text-decoration: none;
            margin: 0 10px;
        }

        .back a:hover {
            color: #b3867fff;
            text-decoration: #754242ff dashed;
        }

        .container {
            width: 75%;
            margin: auto;

        }


        .chat .video a img {
            border-radius: 10px;

        }

        .head {
            text-align: center;
            margin: auto;
            color: #754242ff;
            background-color: #e8e1df;
            border: #bda7a7ff dashed;
            width: 75%;
            border-radius: 10Px;
            margin-top: 80px;


        }

        .video {
            margin-bottom: 4px;
        }

        :root {
            --primary-color: rgb(112, 79, 93);
            /* Bleu foncé */
            --secondary-color: rgb(229, 191, 184);
            /* Violet */
            --accent-color: rgba(222, 222, 222, 1);
            /* Rouge */
            --background-color: rgb(247, 250, 252);
            /* Blanc cassé */
            --text-color: rgb(45, 55, 72);
            /* Texte foncé */
            --border-color: rgb(203, 213, 224);
            /* Bordure grise */
            --chat-bubble-user: rgb(108, 99, 255);
            /* Violet pour l'utilisateur */
            --chat-bubble-bot: #FFFFFF;
            /* Blanc pour le bot */
            --success-color: #48BB78;
            /* Vert */
            --text-light: #718096;
            /* Texte secondaire */
            --warning-color: #ED8936;
            /* Orange */
        }

        .navbar {
            background: var(--primary-color);
            color: white;
            padding: 1rem 2rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand i {
            color: var(--accent-color);
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-links a:hover {
            color: var(--accent-color);
            transform: translateY(-2px);
        }

        .nav-links a i {
            font-size: 1.1rem;
        }
    </style>
</head>


<body>

    <nav class="navbar">
        <div class="navbar-brand">
            <i class="fas fa-robot"></i>
            <span><?php echo "Hey, " . $username . " 👋"; ?></span>
        </div>
        <div class="nav-links">
            <a href="dashboard.php"><i class=" fas fa-tasks"></i> <span class="active">Dashboard</span></a>
            <a href="chattbot.php"><i class="fas fa-comment-dots"></i> <span>Luna</span></a>
            <a href="logout.php"><i class="fas fa-user"></i> <span>Logout</span></a>
        </div>
    </nav>

    <div class="head">
        <h2>Here you can update your task name !</h2>

        <h4>If you need any help, click on the chatbot to talk with Luna your assistant🤖</h4>
    </div>
    <div class="container">



        <div class="column-edita">
            <h2>Update your task📜</h2>

            <?php if ($error): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <?php if ($success): ?>
                <p class="success"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>

            <!-- <form method="post">
            <label for="task">Task name :</label>
            <input type="text" id="task" name="task" value="<?php echo htmlspecialchars($taskData['task']); ?>" required>
            <button type="submit">Update task</button>
            </form> -->

            <div class="post">
                <form method="post">
                    <input placeholder="Task name" type="text" id="task" name="task" value="<?php echo htmlspecialchars($taskData['task']); ?>" required>
                    <button class="add-task">
                        Update Task
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

                </form>
            </div>
        </div>

        <div class="column-edit chat">
            <div class="video">
                <a href="chattbot.php">
                    <img src="assets/images/chat2.gif" alt="Chat qui danse" width="300">
                </a>

            </div>


        </div>
    </div>

    <div class="back">

        <button type="submit" id="back" class="add-task">
           ⬅ Back To Dashboard
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
            <button type="submit" id="chat" class="add-task">
                Go To Luna ➡
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



    </div>





    <script>
        btn = document.getElementById("back");
        btn.addEventListener("click", () => {

            window.location.href = "dashboard.php";
        });
        btn2 = document.getElementById("chat");
        btn2.addEventListener("click", () => {

            window.location.href = "chattbot.php";
        });
    </script>
</body>

</html>