<?php
    require "../src/modules/db.php";   
    session_start();
    if (!isset($_SESSION['user_id'])) {
        echo '<script>
                alert("You\'re not logged in!"); 
                setTimeout(function() { 
                    window.location.href = "signin.php"; 
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
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    <title>Neptune Swap</title>
</head>
<body class=>
    <nav id="navigation"></nav>
    <main>
        <img class="decoration register-decoration" src="media/Decoration.svg" alt="Decoration">
        <section  class="block-section">
            <div class="register-name">
                <h2 class="block-title">Make a deal</h2>
            </div>
            <form action="../src/modules/create-ad.php" method ="post" enctype="multipart/form-data">
                <h2 class="block-h2">Make offer</h2>
                <label class="text-for-block">Offername</label>
                <input type="text"  name="offername" maxlength = "30" placeholder="Offer Name">
                <label class="text-for-block">Category</label>
                <select class="deal-category" name="category">
                    <option class="deal-option" value="tablet">Tablet</option>
                    <option class="deal-option"  value="pc">PC</option>
                    <option class="deal-option"  value="phone">Phone</option>
                    <option class="deal-option"  value="laptop">Laptop</option>
                </select>
                <label class="text-for-block">Photo of product(recommend size 256x256 px)</label>
                    <label class="input-file">
                        <input type="file" name="file" accept="image/png">
                        <span class="input-file-btn" >Insert</span>           
                        <span class="input-file-text">Select the file</span>
                    </label>   
                <label class="text-for-block">Brief information 1</label>
                <input type="text"  name="brief-information-1" maxlength = "35" placeholder="Brief information 1">
                <label class="text-for-block">Brief information 2</label>
                <input type="text"  name="brief-information-2" maxlength = "35" placeholder="Brief information 2">
                <label class="text-for-block">Brief information 3</label>
                <input type="text"  name="brief-information-3" maxlength = "35" placeholder="Brief information 3"> 
                <label class="text-for-block">Description</label>
                <textarea type="text" class="description-input"  name="description" placeholder="Description"></textarea>
                <label class="text-for-block">Price</label>
                <input type="text"  name="price" placeholder="Price">                             
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
                  h2s = document.querySelectorAll('.register-h2'),
                  ps = document.querySelectorAll('.register-p'),
                  photoInputs = document.querySelectorAll('.input-file'),
                  titles = document.querySelectorAll('.block-h2'),
                  categories = document.querySelectorAll('.deal-category');
        
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

            // Apply margin-left to button for an image
            photoInputs.forEach(photoInput => {
                photoInput.style.marginLeft = `${offsetX}px`;
            });

            // Apply margin-left to title
            titles.forEach(title => {
                title.style.marginLeft = `${offsetX}px`;
            });
        }
        
        // Run the function on load and window resize
        window.addEventListener("load", alignText);
        window.addEventListener("resize", alignText);
    </script>

    <script>
        const fileInputs = document.querySelectorAll('.input-file input[type=file]');
        
        fileInputs.forEach(input => {
          input.addEventListener('change', function() {
            let file = this.files[0];
            let inputFile = this;
            let inputFileParent = inputFile.closest('.input-file');
            let textElement = inputFileParent.querySelector('.input-file-text');
        
            if (textElement) {
              textElement.textContent = file.name;
            }
          });
        });
</script>   
</body>
</html>
