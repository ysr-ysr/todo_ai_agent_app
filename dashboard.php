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
// if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['task'])) {
//   $task = htmlspecialchars($_POST['task']);
//   $stmt = $conn->prepare("INSERT INTO tasks (user_id, task) VALUES (?, ?)");
//   $stmt->bind_param("is", $user_id, $task);

//   $stmt->execute();
//   $stmt->close();


//   // Recharger la page pour afficher la nouvelle tâche
//   header("Location: dashboard.php");
//   exit();
// }
// Ajouter une tâche
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['task'])) {
  $task = htmlspecialchars($_POST['task']);
  $category = $_POST['category'];
  $priority = $_POST['priority'];

  $stmt = $conn->prepare("INSERT INTO tasks (user_id, task, category, priority) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("isss", $user_id, $task, $category, $priority);

  $stmt->execute();
  $stmt->close();

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

// Chart
// Compter les tâches pending
$pendingCountResult = $conn->query("SELECT COUNT(*) AS total FROM tasks WHERE status = 'pending' AND user_id = $user_id");
$pendingCount = $pendingCountResult->fetch_assoc()['total'];

// Compter les tâches done
$doneCountResult = $conn->query("SELECT COUNT(*) AS total FROM tasks WHERE status = 'done' AND user_id = $user_id");
$doneCount = $doneCountResult->fetch_assoc()['total'];



// delete

if (isset($_POST['delete_all'])) {
  $type = $_POST['delete_all'];

  if ($type === 'todo') {
    $sql = "DELETE FROM tasks WHERE user_id = ? AND status = 'pending'";
  } elseif ($type === 'done') {
    $sql = "DELETE FROM tasks WHERE user_id = ? AND status = 'done'";
  }

  if (isset($sql)) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
  }

  header("Location: dashboard.php");
  exit();
}

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
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
      gap: 10px;
      padding: 50px;
      margin-top: 80px;
    }

    .column {
      border: 1px solid #ccc;
      padding: 10px;
      flex: 1;
      min-height: 300px;
      box-shadow: 0 15px 30px rgba(47, 47, 47, 0.15);
      border: #ffffffff dashed 3px;


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
      margin-top: 10px;
      text-align: center;
      padding: 20px;
    }

    .bottom-form {
      margin-top: 20px;
      border: #ffffffff dashed 3px;
      padding: 10px;
      border-radius: 20px;
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
      padding: 12px 40px;
      background: #e5bfb8;
      font-size: 17px;
      font-weight: 700;
      color: #f4f5f5ff;
      border: 3px solid #e5bfb8;
      border-radius: 8px;
      box-shadow: 0 0 0 #e5bfb88c;
      transition: all 0.3s ease-in-out;
      cursor: pointer;
      width: 100%;
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


    input,
    select {
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
      margin: 5px;
    }

    input:focus,
    select:focus {
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



    /* chart style  */

    .dashboard-card {
      width: 800px;
      background: white;
      border-radius: 20px;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
      overflow: hidden;
      margin: auto;
    }

    .card-header {
      background: linear-gradient(135deg, #b27f78af 0%, #e5bfb8b5 100%);
      color: white;
      padding: 20px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .card-header h2 {
      font-weight: 500;
      font-size: 1.8rem;
    }

    .date-display {
      font-size: 0.9rem;
      opacity: 0.9;
    }

    .card-content {
      padding: 30px;
      display: flex;
      height: 100%;
      width: 100%;
    }

    .chart-container {
      width: 40%;
      position: relative;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    .chart-info {
      position: absolute;
      top: 47%;
      left: 50%;
      transform: translate(-50%, -50%);
      text-align: center;
    }

    .percentage {
      font-size: 2.2rem;
      font-weight: 700;
      color: #b2675a8c;
    }

    .chart-info p {
      color: #616161;
      font-size: 0.9rem;
      margin-top: 0.1px;
    }

    .stats-container {
      width: 60%;
      padding-left: 30px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .stat {
      display: flex;
      align-items: center;
      margin-bottom: 25px;
    }

    .color-indicator {
      width: 20px;
      height: 20px;
      border-radius: 50%;
      margin-right: 15px;
    }

    .done-color {
      background-color: #c77381ff;
    }

    .pending-color {
      background-color: #894260ff;
    }

    .stat-details h3 {
      font-size: 1.8rem;
      margin-bottom: 5px;
    }

    .stat-details p {
      color: #616161;
      font-size: 0.9rem;
    }

    .card-footer {
      background: #F5F7FA;
      padding: 20px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .task-summary {
      font-size: 0.9rem;
      color: #616161;
    }

    .view-report {
      color: #668ba8ff;
      text-decoration: none;
      font-weight: 500;
      display: flex;
      align-items: center;
    }

    .view-report i {
      margin-left: 8px;
      transition: transform 0.3s;
    }

    .view-report:hover i {
      transform: translateX(5px);
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

    .challenge-box:hover,
    .selfcare-box:hover,
    .quote-box:hover {
      transition: ease-in-out 0.3s;
      transform: scale(1.02);
      box-shadow: 0 4px 8px rgba(107, 41, 29, 0.1);

    }


    /* quotes */
    .quotes {
      background: #dad0d0ff;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      border: #ffffffff dashed 3px;

    }



    /* delete button */
    /* From Uiverse.io by philipo30 */
    .delete-button {
      position: relative;
      padding: 0.5em;
      border: none;
      background: transparent;
      cursor: pointer;
      font-size: 1em;
      transition: transform 0.2s ease;
      box-shadow: none;
    }

    .trash-svg {
      width: 4em;
      height: 4em;
      transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
      filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
      overflow: visible;
    }

    #lid-group {
      transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .delete-button:hover #lid-group {
      transform: rotate(-28deg) translateY(2px);
    }

    .delete-button:active #lid-group {
      transform: rotate(-12deg) scale(0.98);
    }

    .delete-button:hover .trash-svg {
      transform: scale(1.08) rotate(3deg);
    }

    .delete-button:active .trash-svg {
      transform: scale(0.96) rotate(-1deg);
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
              <th>Category</th>
              <th>Priority</th>
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
                    <label class="containercb">
                      <input type="checkbox" checked="checked" onchange="this.form.submit()">
                      <div class="checkmark"></div>
                    </label>
                  </form>
                </td>

                <!-- Task -->
                <td><?php echo htmlspecialchars($row['task']); ?></td>

                <!-- Category -->
                <td>
                  <?php
                  $color = "";
                  if ($row['category'] == "Personal") $color = "#e55a71ff";
                  if ($row['category'] == "Studies") $color = "#99ce89ff";
                  if ($row['category'] == "Home") $color = "#6e91b1ff";
                  if ($row['category'] == "Work") $color = "#7f6bb3ff";
                  ?>
                  <span style="color: <?php echo $color; ?>; font-weight: bold;">
                    <?php echo ucfirst($row['category']); ?>
                  </span>
                </td>

                <!-- Priority avec couleur -->
                <td>
                  <?php
                  $color = "";
                  if ($row['priority'] == "high") $color = "#d78787ff";
                  if ($row['priority'] == "medium") $color = "#deb66bff";
                  if ($row['priority'] == "low") $color = "#579e57ff";
                  ?>
                  <span style="color: <?php echo $color; ?>; font-weight: bold;">
                    <?php echo ucfirst($row['priority']); ?>
                  </span>
                </td>

                <!-- Created At -->
                <td><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>

                <!-- Edit -->
                <td><a href="edit.php?id=<?php echo intval($row['task_id']); ?>">✏️</a></td>

                <!-- Delete -->
                <td><a href="?delete=<?php echo intval($row['task_id']); ?>">🗑️</a></td>
              </tr>
            <?php endwhile; ?>
          </tbody>




        </table>
        <tfoot>

          <form method="POST">
            <!-- From Uiverse.io by philipo30 -->
            <button style="box-shadow: none;" class="delete-button" type="submit" name="delete_all" value="todo" aria-label="Delete item">
              <svg
                class="trash-svg"
                viewBox="0 -10 64 74"
                xmlns="http://www.w3.org/2000/svg">
                <g id="trash-can">
                  <rect
                    x="16"
                    y="24"
                    width="32"
                    height="30"
                    rx="3"
                    ry="3"
                    fill="#e74c3c"></rect>

                  <g transform-origin="12 18" id="lid-group">
                    <rect
                      x="12"
                      y="12"
                      width="40"
                      height="6"
                      rx="2"
                      ry="2"
                      fill="#c0392b"></rect>
                    <rect
                      x="26"
                      y="8"
                      width="12"
                      height="4"
                      rx="2"
                      ry="2"
                      fill="#c0392b"></rect>
                  </g>
                </g>
              </svg>
            </button>

          </form>

        </tfoot>
      <?php else: ?>
        <p><em> Nothing to do - Enjoy the silence !</em></p>
      <?php endif; ?>

      <!-- Formulaire pour ajouter une tâche -->
      <div class="bottom-form">
        <form method="post">
          <input type="text" name="task" placeholder="New task" required>

          <select name="category" required>
            <option value="" selected disabled>Category</option>
            <option value="Studies">📚 Studies</option>
            <option value="Home">🏠 Home</option>
            <option value="Work">💻 Work</option>
            <option value="Personal">🌸 Personal</option>
          </select>

          <select name="priority" required>
            <option value="" selected disabled>Priority</option>
            <option value="high">High 🔴</option>
            <option value="medium">Medium 🟡</option>
            <option value="low">Low 🟢</option>
          </select>
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


          </button>


        </form>





      </div>
    </div>

    <!-- Colonne centrale (bouton chatbot) -->


    <!-- Colonne des tâches terminées -->
    <div class="column">
      <h2>Tasks Done</h2>

      <?php if ($doneTasks->num_rows > 0): ?>

        <table>
          <thead>
            <tr>
              <th>Task name</th>
              <th>Category</th>
              <th>Priority</th>
              <th>Date</th>
              <th>🗑️</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $doneTasks->fetch_assoc()): ?>
              <tr>
                <td id="task-done"><?php echo htmlspecialchars($row['task']); ?></td>
                <td id="task-done"> <?php echo htmlspecialchars($row['category']); ?></td>
                <td id="task-done"><?php echo htmlspecialchars($row['priority']); ?></td>
                <td style="color: gray;"><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                <td>
                  <a href="?delete=<?php echo intval($row['task_id']); ?>">🗑️</a>
                </td>
              </tr>

            <?php endwhile; ?>
          </tbody>

        </table>
        <tfoot style="text-align: left;padding: 10px 0 10px 500px;">
          <!-- <tr>
            <td colspan="5">
              <h2>
                Delete All
                <a href="dashboard.php?delete_all=done" class="delete-all" title="Supprimer toutes les tâches terminées">🗑️</a>
              </h2>
            </td>
          </tr> -->

          <!-- From Uiverse.io by philipo30 -->


          <form method="POST" >
            
          <button style="box-shadow: none;" type="submit" name="delete_all" aria-label="Delete item" value="done" class="delete-button delete-all delete-button" title="Supprimer toutes les tâches terminées">
              <svg
                class="trash-svg"
                viewBox="0 -10 64 74"
                xmlns="http://www.w3.org/2000/svg">
                <g id="trash-can">
                  <rect
                    x="16"
                    y="24"
                    width="32"
                    height="30"
                    rx="3"
                    ry="3"
                    fill="#e74c3c"></rect>

                  <g transform-origin="12 18" id="lid-group">
                    <rect
                      x="12"
                      y="12"
                      width="40"
                      height="6"
                      rx="2"
                      ry="2"
                      fill="#c0392b"></rect>
                    <rect
                      x="26"
                      y="8"
                      width="12"
                      height="4"
                      rx="2"
                      ry="2"
                      fill="#c0392b"></rect>
                  </g>
                </g>
              </svg>
            </button>
          </form>

        </tfoot>
      <?php else: ?>
        <p><em>All tasks are done - Keep going !</em></p>
      <?php endif; ?>



    </div>



  </div>

  <!-- <canvas id="tasksChart" width="200" height="150"></canvas> -->



  <div class="dashboard-card">
    <div class="card-header">
      <h2><i class="fas fa-tasks"></i> Task Progress</h2>
      <div class="date-display"><?php echo date('F j, Y'); ?></div>
    </div>

    <div class="card-content">
      <div class="chart-container">
        <canvas id="tasksChart" width="200" height="200"></canvas>
        <div class="chart-info">
          <div class="percentage"><?php echo $completionRate; ?>%</div>
          <p>Completed</p>
        </div>
      </div>

      <div class="stats-container">
        <div class="stat">
          <div class="color-indicator done-color"></div>
          <div class="stat-details">
            <h3><?php echo $doneCount; ?></h3>
            <p>Tasks Completed</p>
          </div>
        </div>

        <div class="stat">
          <div class="color-indicator pending-color"></div>
          <div class="stat-details">
            <h3><?php echo $pendingCount; ?></h3>
            <p>Tasks Pending</p>
          </div>
        </div>

        <div class="stat">
          <i class="fas fa-bolt fa-2x" style="color: #FF9800; margin-right: 15px;"></i>
          <div class="stat-details">
            <h3><?php echo $pendingCount + $doneCount; ?></h3>
            <p>Total Tasks</p>
          </div>
        </div>
      </div>
    </div>

    <div class="card-footer">
      <div class="task-summary">
        <i class="fas fa-info-circle"></i> You've completed <?php echo $doneCount; ?> out of <?php echo $doneCount + $pendingCount; ?> tasks
      </div>
      <a href="chattbot.php" class="view-report">
        Ask for help from Luna<i class="fas fa-arrow-right"></i>
      </a>
    </div>
  </div>





  <div class="middle" style="margin-top: 10px;">
    <div id="chatbot" style="margin-top: 100px;" class="tooltip-wrapper">
      <button class="hover-me">Chat With Luna 🤖</button>
      <div class="tooltip">
        <span class="star">⭐</span>
        <span class="star">⭐</span>
        <span class="star">⭐</span>
        Luna is a chatbot that can <br> help you with your tasks. Click the button to start chatting!
      </div>
    </div>
  </div>


  <div class=" quotes" style="width: 60%; margin: 20px auto; padding: 20px; box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px; border-radius: 10px; background: #f7ebebff; text-align: center;color: #706e6eff;">
    <h2>“A Corner for Inspiration, Progress & Well-being” 💫</h2>
    <!-- <table>
          <thead>
            <tr>
              <th>The </th>
            </tr>
          </thead>
        </table> -->


    <div class="container">
      <div class="challenge-box column" style="height: fit-content;padding: 15px; border-left: 4px solid #e5bfb8; background-color: #f9f9f9;  font-style: italic;width: 100%;">
        <br><br><br><br><br>
        <strong>🌟 Challenge of the day :</strong><br>
        <?php echo $randomChallenge; ?>
      </div>



      <div class="quote-box column"
        style="height: fit-content;padding: 15px; border-left: 4px solid #e5bfb8; background-color: #f9f9f9;  font-style: italic;width: 100%;">
        <br><br><br><br><br>

        <strong>💬 Quote of the day :</strong><br>
        <?php echo $randomQuote; ?>
      </div>

      <div class="selfcare-box column" style=" height: fit-content;padding: 15px; border-radius: 10px; background-color: #f9f9f9;  font-size: 16px; border-left: 5px solid #e5bfb8;width: 100%;">
        <br><br><br><br><br>

        <strong>☕ Self-care of the day :</strong><br>
        <?php echo $randomSelfCare; ?>
      </div>
    </div>





  </div>






  <script>
    btn2 = document.getElementById("chatbot");
    btn2.addEventListener("click", () => {

      window.location.href = "chattbot.php";
    });

    // const ctx = document.getElementById('tasksChart').getContext('2d');
    // new Chart(ctx, {
    //   type: 'pie',
    //   data: {
    //     labels: ['Pending', 'Done'],
    //     datasets: [{
    //       data: [<?php echo $pendingCount; ?>, <?php echo $doneCount; ?>],
    //       backgroundColor: ['#2196F3', '#4CAF50']
    //     }]
    //   },
    //   options: {
    //     responsive: false, // important pour que ça reste petit
    //     plugins: {
    //       legend: {
    //         position: 'bottom',
    //         labels: {
    //           font: {
    //             size: 10
    //           }
    //         } // petite taille
    //       },
    //       title: {
    //         display: false
    //       }
    //     }
    //   }
    // });



    document.addEventListener('DOMContentLoaded', function() {
      const ctx = document.getElementById('tasksChart').getContext('2d');

      // Sample data - replace with your PHP variables
      const pendingCount = <?php echo $pendingCount ?? 0; ?>;
      const doneCount = <?php echo $doneCount ?? 0; ?>;
      const totalTasks = pendingCount + doneCount;
      const completionRate = Math.round((doneCount / totalTasks) * 100);

      // Update the percentage display
      document.querySelector('.percentage').textContent = completionRate + '%';

      new Chart(ctx, {
        type: 'pie',
        data: {
          labels: ['Pending', 'Done'],
          datasets: [{
            data: [pendingCount, doneCount],
            backgroundColor: ['#894260ff', '#c77381ff'],
            borderWidth: 0,
            hoverOffset: 10
          }]
        },
        options: {
          responsive: false,
          cutout: '65%',
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                font: {
                  size: 10
                },
                padding: 15
              }
            },
            title: {
              display: false
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  const label = context.label || '';
                  const value = context.raw || 0;
                  const totalTasks = pendingCount + doneCount;
                  const completionRate = totalTasks > 0 ? Math.round((doneCount / totalTasks) * 100) : 0;

                  return `${label}: ${value} (${completionRate}%)`;
                }
              }
            }
          }
        }
      });
    });
  </script>
</body>

</html>