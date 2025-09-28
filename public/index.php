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
    <title>Neptune Swap</title>
</head>
<body class=>
    <nav id="navigation"></nav>
    <main>
        <img class="decoration" src="media/Decoration.svg" alt="Decoration">
        <section class="introduction-section">
            <div class="introduction-info">
                <h1>Neptune Swap</h1>
                <p class="quote-p">Technologies that don't drown in time</p>
                <p class="introduction-text">Why let your old laptop, PC, tablet, or smartphone collect dust when you can<br> exchange it for something better? At Neptune Swap, we make tech trading easy,<br> secure, and hassle-free. Whether you're looking to swap your old device for a newer<br> model, sell it for a great price, or find a budget-friendly upgrade, our platform<br> connects you with the best deals.</p>
                <button onclick="document.location='deal-create.php'" class="introduction-start-button ">Start</button>
            </div>
            <div class="introduction-div">
                <img class="introduction-photo" src="media/samsung-photo.png" alt="samsung">
            </div>
        </section>
        <section class="site-info">
            <div class="text-info">
                <h2>Why Choose Neptune Swap?</h2>
                <p>At Neptune Swap, we believe that great tech should always find a second<br> life.  Whether you want to upgrade your laptop, replace your old<br> smartphone, or  simply find a better deal on a tablet or PC, our platform<br> makes the process smooth and hassle-free.
                </p>
            </div>
            <div class="img-info">
                <img class="info-photo" src="media/laptop-photo.png" alt="laptop">
            </div>
        </section>
        <section class="site-info info-2">
            <div class="img-info">
                <img class="info-photo" src="media/iphone-photo.png" alt="laptop">
            </div>
            <div class="text-info">
                <h2>Save Money & Reduce Waste</h2>
                <p>
                    Why spend a fortune on brand-new devices when you can find high-quality,<br> pre-owned tech at a fraction of the price? Plus, by giving your old gadgets a<br> new home, you’re helping reduce electronic<br> waste!
                </p>
            </div>
        </section>
        <section class="site-info info-3">
            <div class="text-info">
                <h2>Easy & Secure Trading</h2>
                <p>
                    Browse a wide selection of used devices, connect with trusted<br> users, and make safe exchanges. Our secure system ensures<br> that you get exactly what you’re<br> looking for.
                </p>
            </div>
            <div class="img-info">
                <img class="info-photo" src="media/tablet-photo.png" alt="laptop">
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
</body>
</html>
