<?php
    require "../src/modules/db.php";   
    session_start();
    if (!isset($_SESSION['user_id'])) {
        echo '<script>
                alert("You\'re not logged in!"); 
                setTimeout(function() { 
                    window.location.href = "index.php"; 
                }, 100);
              </script>';
        exit;
    }
    $userId = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    <title>Neptune Swap</title>
</head>
<body>
    <nav id="navigation"></nav>
    <main>
        <section class="block-section market-section">
            <div class="register-name">
                <h2 class="block-title">Chat</h2>
            </div>
            <h2 class="block-h2 chat-h2 select-user-h2">Select the user to chat with</h2>
            <div class="users-list">
                <div class="user">
                    <p class="chat-p">User</p>
                </div>
            </div>
            <button class="haggle-return-button" style="display: none;">✖</button>
            <div class="chat-block" style="display: none;">
                <h2 class="block-h2 chat-h2"></h2>
                    <div class="messages-block">   
                            <div class="my-message"> 
                                <p class="sender-name"></p>
                                <p class=" message"></p>   
                                <p class="message-time"></p>
                            </div>
                            <div class="user-message">
                                <p class="sender-name"></p>
                                <p class=" message">T</p>  
                                <p class="message-time"></p>
                            </div>
                    </div>
                    <div class="input-block">
                        <input class="chat-input" type="text" placeholder="Write a message">
                        <button class="send-button">></button>
                    </div>
            </div>

            <div class="page-switcher">
                <button class="switch-page-button" id="prev-page">&lt;</button>
                <p class="page-text" id="page-text">1</p>
                <button class="switch-page-button" id="next-page">&gt;</button>
            </div>
        </section>
    </main>

    <!-- Navigation module script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            fetch("../src/modules/navigation.php") 
            .then(response => response.text())
            .then(html => {
                document.querySelector("#navigation").innerHTML = html;
            });
        });
    </script>

    <footer></footer>
    <!-- Footer module script -->
    <script type="module">  
        document.addEventListener("DOMContentLoaded", function () {
            fetch("../src/modules/footer.html") 
            .then(response => response.text())
            .then(html => {
                document.querySelector("footer").innerHTML = html;
            });
        });
    </script>

    <script src="scripts/messages.js" defer></script>




</body>
</html>

