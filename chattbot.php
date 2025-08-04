<?php
$response = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $msg = $_POST["message"];

    $data = ['message' => $msg];
    $options = [
        'http' => [
            'header'  => "Content-Type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents('http://127.0.0.1:5000/ask', false, $context);

    if ($result !== false) {
        $json = json_decode($result, true);
        $response = $json["response"] ?? "No answer from the chatbot.";
    } else {
        $response = "Communication error with the chatbot.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luna Chatbot</title>
    <link rel="stylesheet" href="assets/mystyle.css">
  <link rel="icon" href="assets/images/favicon/favicon.ico" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Nouvelle palette de couleurs uniquement */
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

        /* Le reste du CSS reste inchangé */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Navbar Styles */
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

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 6rem 2rem 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .chat-container {
            display: flex;
            width: 100%;
            height: 70vh;
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        /* Chat Sidebar */
        .chat-sidebar {
            width: 300px;
            background: var(--secondary-color);
            color: white;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
        }

        .bot-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .bot-name {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .bot-title {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 1.5rem;
        }

        .bot-stats {
            width: 100%;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1rem;
            margin-top: auto;
        }

        .stat-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .stat-item:last-child {
            margin-bottom: 0;
        }

        .stat-value {
            font-weight: 600;
        }

        /* Chat Area */
        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .chat-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .chat-header-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .chat-header-info h3 {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .chat-header-info p {
            font-size: 0.8rem;
            color: var(--text-light);
        }

        .chat-display {
            flex: 1;
            padding: 1.5rem;
            overflow-y: auto;
            background-color: var(--background-color);
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .chat-bubble {
            max-width: 70%;
            padding: 1rem 1.25rem;
            border-radius: 1rem;
            line-height: 1.5;
            word-wrap: break-word;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            position: relative;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .chat-bubble.user {
            align-self: flex-end;
            background-color: var(--chat-bubble-user);
            color: white;
            border-bottom-right-radius: 0.25rem;
        }

        .chat-bubble.bot {
            align-self: flex-start;
            background-color: var(--chat-bubble-bot);
            color: var(--text-color);
            border-bottom-left-radius: 0.25rem;
            border: 1px solid var(--border-color);
        }

        .chat-bubble .time {
            display: block;
            font-size: 0.7rem;
            opacity: 0.7;
            margin-top: 0.5rem;
            text-align: right;
        }

        .chat-input-area {
            padding: 1.5rem;
            border-top: 1px solid var(--border-color);
            background-color: white;
            display: flex;
            gap: 1rem;
        }

        .chat-input-area input[type="text"] {
            flex: 1;
            padding: 0.75rem 1.25rem;
            border: 2px solid var(--border-color);
            border-radius: 2rem;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .chat-input-area input[type="text"]:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(77, 68, 219, 0.2);
        }

        /* .chat-input-area button {
            padding: 0.75rem 1.5rem;
            background-color: var(--accent-color);
            color: white;
            border: none;
            border-radius: 2rem;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(245, 101, 101, 0.2);
        } */

        /* .chat-input-area button:hover {
            background-color: #E53E3E;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(245, 101, 101, 0.3);
        } */

        /* Response styling */
        #resp {
            white-space: pre-wrap;
        }

        #response strong , .chat-display .chat-bubble span {
            color: var(--secondary-color);
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .chat-sidebar {
                width: 250px;
                padding: 1.5rem;
            }

            .bot-avatar {
                width: 100px;
                height: 100px;
            }
        }

        @media (max-width: 768px) {
            .chat-container {
                flex-direction: column;
                height: auto;
            }

            .chat-sidebar {
                width: 100%;
                padding: 1.5rem;
                flex-direction: row;
                align-items: center;
                gap: 1rem;
            }

            .bot-avatar {
                width: 60px;
                height: 60px;
                margin-bottom: 0;
            }

            .bot-info {
                text-align: left;
            }

            .bot-stats {
                display: none;
            }

            .nav-links {
                gap: 1rem;
            }

            .navbar {
                padding: 1rem;
            }

            .navbar-brand {
                font-size: 1.2rem;
            }
        }

        @media (max-width: 576px) {
            .main-content {
                padding: 5rem 1rem 1rem;
            }

            .chat-input-area {
                flex-direction: column;
            }

            .chat-input-area button {
                justify-content: center;
            }

            .chat-bubble {
                max-width: 85%;
            }

            .nav-links a span {
                display: none;
            }

            .nav-links a i {
                font-size: 1.3rem;
            }
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
    </style>
</head>

<body>
    <!-- Fixed Navbar -->
    <nav class="navbar">
        <div class="navbar-brand">
            <i class="fas fa-robot"></i>
            <span>Luna AI</span>
        </div>
        <div class="nav-links">
            <a href="dashboard.php"><i class="fas fa-tasks"></i> <span>Dashboard</span></a>
            <a href="chattbot.php"><i class=" fas fa-comment-dots"></i> <span class="active">Luna Chat</span></a>
            <a href="logout.php"><i class="fas fa-user"></i> <span>Logout</span></a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="chat-container">
            <!-- Chat Sidebar -->
            <div class="chat-sidebar">
                <img src="assets/images/chatbot7.gif" alt="Luna Avatar" class="bot-avatar">
                <div class="bot-info">
                    <h3 class="bot-name">Luna</h3>
                    <p class="bot-title">Your AI Assistant, ready to help!</p>
                </div>

            </div>

            <!-- Chat Area -->
            <div class="chat-area">
                <div class="chat-header">
                    <img src="assets/images/chatbot7.gif" alt="Luna Avatar" class="chat-header-avatar">
                    <div class="chat-header-info">
                        <h3>Luna Chat</h3>
                        <p>AI-powered assistant</p>
                    </div>
                </div>

                <div class="chat-display" id="chatDisplay">
                    <!-- Default welcome message -->
                    <div class="chat-bubble bot">
                       <span><strong>Luna :</strong></span> 
                        Hello! I'm Luna, your AI assistant. How can I help you today?
                        <!-- <span class="time"><?= date('H:i') ?></span> -->
                    </div>

                    <!-- AI Response -->
                    <?php if (!empty($response)): ?>
                        <div class="chat-bubble bot">
                            <div id="response">
                                <strong >Luna:</strong><br>
                                <div id="resp"><?= $response ?></div>
                            </div>
                            <!-- <span class="time"><?= date('H:i') ?></span> -->
                        </div>
                    <?php endif; ?>
                </div>

                <form method="post" class="chat-input-area">
                    <input type="text" id="question" name="message" placeholder="Type your message here..." required>

                    <button type="submit" id="sendMessageBtn" class="add-task">
                        <i class="fas fa-paper-plane"></i>
                        <span>Send</span>
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

                </form>
            </div>
        </div>
    </div>

    <script>
        // Auto-scroll to bottom of chat
        const chatDisplay = document.getElementById('chatDisplay');
        chatDisplay.scrollTop = chatDisplay.scrollHeight;

        // Add animation to send button
        const sendBtn = document.getElementById('sendMessageBtn');
        sendBtn.addEventListener('click', function() {
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-paper-plane"></i> <span>Send</span>';
            }, 2000);
        });
    </script>
</body>

</html>