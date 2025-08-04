<?php
session_start();
include 'config/db.php';

// Vérifier si l'utilisateur est connecté, sinon rediriger vers login
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username);
$stmt->fetch();
$stmt->close();
// Ajouter une nouvelle tâche
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['task'])) {
  $task = htmlspecialchars($_POST['task']);
  $stmt = $conn->prepare("INSERT INTO tasks (user_id, task) VALUES (?, ?)");
  $stmt->bind_param("is", $user_id, $task);
  $stmt->execute();
  $stmt->close();

  // Recharger la page pour afficher la nouvelle tâche
  header("Location: dashboard.php");
  exit();
}

// Marquer une tâche comme "done"
if (isset($_GET['done'])) {
  $task_id = intval($_GET['done']);
  $stmt = $conn->prepare("UPDATE tasks SET status = 'done' WHERE task_id = ? AND user_id = ?");
  $stmt->bind_param("ii", $task_id, $user_id);
  $stmt->execute();
  $stmt->close();

  header("Location: dashboard.php");
  exit();
}

// Supprimer une tâche
if (isset($_GET['delete'])) {
  $task_id = intval($_GET['delete']);
  $stmt = $conn->prepare("DELETE FROM tasks WHERE task_id = ? AND user_id = ?");
  $stmt->bind_param("ii", $task_id, $user_id);
  $stmt->execute();
  $stmt->close();

  header("Location: dashboard.php");
  exit();
}

// Récupérer les tâches "pending" (à faire)
$stmt1 = $conn->prepare("SELECT * FROM tasks WHERE user_id = ? AND status = 'pending' ORDER BY created_at DESC");
$stmt1->bind_param("i", $user_id);
$stmt1->execute();
$pendingTasks = $stmt1->get_result();

// Récupérer les tâches "done" (terminées)
$stmt2 = $conn->prepare("SELECT * FROM tasks WHERE user_id = ? AND status = 'done' ORDER BY created_at DESC");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$doneTasks = $stmt2->get_result();

// Les challenges quotidiennes et les quotes 
$quotes = [
  "“Success is the sum of small efforts repeated day in and day out.” – Robert Collier",
  "“Don’t watch the clock; do what it does. Keep going.” – Sam Levenson",
  "“Don’t wait for motivation, start and it will come.” – Unknown",
  "“Every day is a new chance to do better.” – Unknown",
  "“The future depends on what you do today.” – Mahatma Gandhi",
  "“You don’t need to be perfect, just consistent.” – Unknown",
  "“Do the best you can until you know better. Then when you know better, do better.” – Maya Angelou",
  "“Don’t give up, the beginning is always the hardest.” – Unknown",
  "“You are stronger than you think.” – Winnie the Pooh 🐻",
  "“Act as if it were impossible to fail.” – Winston Churchill"
];


$randomQuote = $quotes[array_rand($quotes)];


$challenges = [
  "📚 Read 5 pages of a book today.",
  "🧹 Tidy up a small corner of your room.",
  "📵 Stay away from your phone for 1 hour.",
  "🙏 Write down 3 things you’re grateful for.",
  "📝 Finish a task you’ve been putting off.",
  "🧘‍♀️ Do 5 minutes of breathing or meditation.",
  "🧠 Learn a new word in another language.",
  "🚶‍♂️ Go for a short walk outside.",
  "🎧 Listen to a motivating song and start your task.",
  "🍎 Drink a big glass of water and take care of yourself."
];


$randomChallenge = $challenges[array_rand($challenges)];



$selfCareTips = [
  "🍶 Drink a glass of fresh water.",
  "🎵 Listen to your favorite song.",
  "🌬️ Close your eyes for 1 minute and take a deep breath.",
  "🌙 Turn off your screen for 15 minutes.",
  "🧘‍♀️ Gently stretch your body for 2 minutes.",
  "📵 Put your phone away for 30 minutes.",
  "🛏️ Take a mini nap for 10–15 minutes.",
  "🧴 Take care of your skin with a bit of cream.",
  "🍎 Eat a fresh piece of fruit.",
  "📖 Read one page of a book that inspires you."
];

$randomSelfCare = $selfCareTips[array_rand($selfCareTips)];
?>





<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <title>Dashboard</title>
  <link rel="icon" href="assets/images/favicon/favicon.ico" type="image/x-icon">

  <link rel="stylesheet" href="assets/mystyle.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
      /* background-image: url("assets/images/bg14.jpeg"); */
      background-color: rgb(247, 250, 252);

      /* background-size: cover;
            background-position: center; */
    }

    .container {
      display: flex;
      gap: 20px;
      padding: 20px;
      margin-top: 80px;
    }

    .column {
      border: 1px solid #ccc;
      padding: 10px;
      flex: 1;
      min-height: 300px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th,
    td {
      padding: 8px;
      border: 1px solid #ddd;
      text-align: center;
    }

    th {
      background-color: #f2f2f2;
    }

    ul {
      list-style: none;
      padding-left: 0;
    }

    li {
      margin-bottom: 10px;
    }

    .task-actions a {
      margin-left: 10px;
      text-decoration: none;
      color: #007BFF;
    }

    .middle {
      text-align: center;
      padding: 20px;
    }

    .bottom-form {
      margin-top: 20px;
    }

    table tbody #task-done {

      color: #888888;
      /* gris moyen */
      text-decoration: line-through;
      /* barre au milieu */
      font-style: italic;
      /* optionnel, pour un effet style */

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
      filter: drop-shadow(0 0 0 #e5bfb8);
      z-index: -5;
      transition: all 1s cubic-bezier(0.05, 0.83, 0.43, 0.96);
    }

    .star-2 {
      position: absolute;
      top: 45%;
      left: 45%;
      width: 15px;
      height: auto;
      filter: drop-shadow(0 0 0 #e5bfb8);
      z-index: -5;
      transition: all 1s cubic-bezier(0, 0.4, 0, 1.01);
    }

    .star-3 {
      position: absolute;
      top: 40%;
      left: 40%;
      width: 5px;
      height: auto;
      filter: drop-shadow(0 0 0 #e5bfb8);
      z-index: -5;
      transition: all 1s cubic-bezier(0, 0.4, 0, 1.01);
    }

    .star-4 {
      position: absolute;
      top: 20%;
      left: 40%;
      width: 8px;
      height: auto;
      filter: drop-shadow(0 0 0 #e5bfb8);
      z-index: -5;
      transition: all 0.8s cubic-bezier(0, 0.4, 0, 1.01);
    }

    .star-5 {
      position: absolute;
      top: 25%;
      left: 45%;
      width: 15px;
      height: auto;
      filter: drop-shadow(0 0 0 #e5bfb8);
      z-index: -5;
      transition: all 0.6s cubic-bezier(0, 0.4, 0, 1.01);
    }

    .star-6 {
      position: absolute;
      top: 5%;
      left: 50%;
      width: 5px;
      height: auto;
      filter: drop-shadow(0 0 0 #e5bfb8);
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
      filter: drop-shadow(0 0 10px #e5bfb8);
      z-index: 2;
    }

    button:hover .star-2 {
      position: absolute;
      top: -25%;
      left: 10%;
      width: 15px;
      height: auto;
      filter: drop-shadow(0 0 10px #e5bfb8);
      z-index: 2;
    }

    button:hover .star-3 {
      position: absolute;
      top: 55%;
      left: 25%;
      width: 5px;
      height: auto;
      filter: drop-shadow(0 0 10px #e5bfb8);
      z-index: 2;
    }

    button:hover .star-4 {
      position: absolute;
      top: 30%;
      left: 80%;
      width: 8px;
      height: auto;
      filter: drop-shadow(0 0 10px #e5bfb8);
      z-index: 2;
    }

    button:hover .star-5 {
      position: absolute;
      top: 25%;
      left: 115%;
      width: 15px;
      height: auto;
      filter: drop-shadow(0 0 10px #e5bfb8);
      z-index: 2;
    }

    button:hover .star-6 {
      position: absolute;
      top: 5%;
      left: 60%;
      width: 5px;
      height: auto;
      filter: drop-shadow(0 0 10px #e5bfb8);
      z-index: 2;
    }

    .fil0 {
      fill: #fffdef;
    }


    input {
      width: 60%;
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




    /* style check box */

    /* Hide the default checkbox */
    .containercb {
      margin-left: 15px;
    }

    .containercb input {
      position: absolute;
      opacity: 0;
      cursor: pointer;
      height: 0;
      width: 0;
      margin: auto;

    }

    .containercb {
      border-radius: 9px;
      display: block;
      position: relative;
      cursor: pointer;
      font-size: 20px;
      user-select: none;
      box-shadow: rgba(139, 18, 113, 0.2) 0px 8px 24px;
      background-image: linear-gradient(45deg, #f3d5f7, #fbf6e7, #e6fcf5);
    }

    /* Create a custom checkbox */
    .checkmark {
      border-radius: 9px;
      position: relative;
      top: 0;
      left: 0;
      height: 1.3em;
      width: 1.3em;
      background-color: linear-gradient(45deg, #f8e7fa, #fbf6e7, #e6fcf5);
    }

    /* Create the checkmark/indicator (hidden when not checked) */
    .checkmark:after {
      content: "";
      position: absolute;
      display: none;
    }

    /* Show the checkmark when checked */
    .containercb input:checked~.checkmark:after {
      display: block;
    }

    /* Style the checkmark/indicator */
    .containercb .checkmark:after {
      left: 0.45em;
      top: 0.25em;
      width: 0.25em;
      height: 0.5em;
      border: solid rgb(233, 182, 182);
      border-width: 0 0.15em 0.15em 0;
      transform: rotate(45deg);
    }




    /* 
    .navbar {
      background-color: (112, 79, 93);
      padding: 1em;
      text-align: center;
      color: #e5bfb8;
    } */
    /* 
    .navbar a {
      color: #e5bfb8;
      text-decoration: none;
      transition: color 0.2s ease-in-out;
      /* transition smooth sur les liens */
    /* }

    .navbar a:hover {
      color: #fefefe; */
    /**/
    /* couleur au survol */
    /* }

    .navbar .nav-links {
      list-style: none;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: space-between;
    }

    .navbar .nav-links li {
      margin-right: 20px;
    } */




    /* nav style  */


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

    .challenge-box:hover,
    .selfcare-box:hover,
    .quote-box:hover {
      transition: ease-in-out 0.3s;
      transform: scale(1.02);
      box-shadow: 0 4px 8px rgba(107, 41, 29, 0.1);

    }






    
  </style>
</head>

<body>

  <!-- <nav class="navbar">
    <div class="navbar-brand">
      <i class="fas fa-check-circle"></i>
      <span class="user-info"><?php echo "Hey, " . $username . " 👋"; ?></span>
    </div>
    <div class="nav-links">
      <a href="dashboard.php" class="active"><i class="fas fa-home"></i> </a>
      <a href="chatbot.php"><i class="fas fa-robot"></i> AI Chatbot</a>
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
  </nav> -->


  <nav class="navbar">
    <div class="navbar-brand">
      <i class="fas fa-robot"></i>
      <span><?php echo "Hey, " . $username . " 👋"; ?></span>
    </div>
    <div class="nav-links">
      <a href="dashboard.php"><i class=" fas fa-tasks"></i> <span class="active">Dashboard</span></a>
      <a href="chattbot.php"><i class="fas fa-comment-dots"></i> <span>Luna Chat</span></a>
      <a href="logout.php"><i class="fas fa-user"></i> <span>Logout</span></a>
    </div>
  </nav>

  <div class="container">

    <!-- Colonne des tâches à faire -->
    <div class="column">
      <h2>To-Do List</h2>

      <?php if ($pendingTasks->num_rows > 0): ?>
        <table>
          <thead>
            <tr>
              <th>✔️</th>
              <th>Task name</th>
              <th>Date</th>
              <th>✏️</th>
              <th>🗑️</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $pendingTasks->fetch_assoc()): ?>
              <tr>
                <td>
                  <form method="get" style="margin:0;">
                    <input type="hidden" name="done" value="<?php echo intval($row['task_id']); ?>">
                    <!-- <input type="checkbox" onchange="this.form.submit()"> -->
                    <label class="containercb">
                      <input type="checkbox" checked="checked" onchange="this.form.submit()">
                      <div class="checkmark"></div>
                    </label>
                  </form>
                </td>
                <td><?php echo htmlspecialchars($row['task']); ?></td>
                <td><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                <td><a href="edit.php?id=<?php echo intval($row['task_id']); ?>">✏️</a></td>
                

                <td><a href="?delete=<?php echo intval($row['task_id']); ?>">🗑️</a></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p><em> Nothing to do - Enjoy the silence !</em></p>
      <?php endif; ?>

      <!-- Formulaire pour ajouter une tâche -->
      <div class="bottom-form">
        <form method="post">
          <input type="text" name="task" placeholder="New task" required>
          <button class="add-task">
            Add Task
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

    <!-- Colonne centrale (bouton chatbot) -->
    <div class="middle">
      <div id="chatbot" class="tooltip-wrapper">
        <button class="hover-me">Chat With Luna 🤖</button>
        <div class="tooltip">
          <span class="star">⭐</span>
          <span class="star">⭐</span>
          <span class="star">⭐</span>
          Luna is a chatbot that can <br> help you with your tasks. Click the button to start chatting!
        </div>
      </div>
    </div>

    <!-- Colonne des tâches terminées -->
    <div class="column">
      <h2>Tasks Done</h2>

      <?php if ($doneTasks->num_rows > 0): ?>
        <table>
          <thead>
            <tr>
              <th>Task name</th>
              <th>Date</th>
              <th>🗑️</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $doneTasks->fetch_assoc()): ?>
              <tr>
                <td id="task-done"><?php echo htmlspecialchars($row['task']); ?></td>
                <td><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                <td>
                  <a href="?delete=<?php echo intval($row['task_id']); ?>">🗑️</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p><em>All tasks are done - Keep going !</em></p>
      <?php endif; ?>
    </div>



  </div>


  <div class="container" style="width: 60%; margin: 20px auto;">

    <!-- <table>
          <thead>
            <tr>
              <th>The </th>
            </tr>
          </thead>
        </table> -->
    <div class="challenge-box column" style="height: fit-content;padding: 15px; border-left: 4px solid #e5bfb8; background-color: #f9f9f9; margin: 20px 0; font-style: italic;width: 100%;">
      <br><br><br><br><br>
      <strong>🌟 Challenge of the day :</strong><br>
      <?php echo $randomChallenge; ?>
    </div>



    <div class="quote-box column"
      style="height: fit-content;padding: 15px; border-left: 4px solid #e5bfb8; background-color: #f9f9f9; margin: 20px 0; font-style: italic;width: 100%;">
      <br><br><br><br><br>

      <strong>💬 Quote of the day :</strong><br>
      <?php echo $randomQuote; ?>
    </div>

    <div class="selfcare-box column" style=" height: fit-content;padding: 15px; border-radius: 10px; background-color: #f9f9f9; margin: 20px 0; font-size: 16px; border-left: 5px solid #e5bfb8;width: 100%;">
      <br><br><br><br><br>

      <strong>☕ Self-care of the day :</strong><br>
      <?php echo $randomSelfCare; ?>
    </div>





  </div>




  

  <script>
    btn2 = document.getElementById("chatbot");
    btn2.addEventListener("click", () => {

      window.location.href = "chattbot.php";
    });


  </script>
</body>

</html>