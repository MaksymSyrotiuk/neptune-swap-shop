<?php 


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
    <title>Sign Up</title>
</head>
<body class=>
    <nav id="navigation"></nav>
    <main>
        <img class="decoration register-decoration" src="media/Decoration.svg" alt="Decoration">
        <section class="block-section">
            <div class="register-name">
                <h2 class="block-title">Sign Up</h2>
            </div>
            <form action="../src/modules/register.php" method ="post">
                <h2 class="block-h2">New Account</h2>
                <p class="block-p">You can create a new account here</p>
                <label class="text-for-block">Login</label>
                <input type="text" name="login" placeholder="Login">
                <label class="text-for-block">Password</label>
                <input type="password" name="password" placeholder="Password">
                <label class="text-for-block">E-mail</label>
                <input type="email" name="email" placeholder="E-mail">
                <label class="text-for-block">Date of Birth</label>
                <input type="date" name="birth_date" placeholder="Password">
                <label  class="text-for-block">Sex</label>
                <select class="deal-category" name="gender">
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
                <button type="submit" class="register-button">Create</button>
            </form>
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
    <script>
        function alignText() {
            const inputs = document.querySelectorAll('input, select'),
                  texts = document.querySelectorAll('.text-for-block'),
                  h2s = document.querySelectorAll('.block-h2'),
                  ps = document.querySelectorAll('.block-p');
        
            if (inputs.length === 0) return; // Error protection if there are no inputs
        
            // Calculate the initial offset from the first input
            const firstInputX = inputs[0].getBoundingClientRect().left;
            const parentX = inputs[0].parentElement.getBoundingClientRect().left;
            const offsetX = firstInputX - parentX;
        
            // Apply margin-left to labels
            texts.forEach(text => {
                text.style.marginLeft = `${offsetX}px`;
            });
        
            // Apply margin-left to h2
            h2s.forEach(h2 => {
                h2.style.marginLeft = `${offsetX}px`;
            });
        
            // Apply margin-left to p
            ps.forEach(p => {
                p.style.marginLeft = `${offsetX}px`;
            });
        }
        
        // Run the function on load and window resize
        window.addEventListener("load", alignText);
        window.addEventListener("resize", alignText);
    </script>
</body>
</html>
