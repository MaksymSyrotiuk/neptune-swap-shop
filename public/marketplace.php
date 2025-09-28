<?php
    require "../src/modules/db.php";   
    session_start();
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
    } else {
        $userId = null;
}

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
    <title>Marketplace</title>
</head>
<body class=>
    <nav id="navigation"></nav>
    <main>
        <img class="decoration register-decoration" src="media/Decoration.svg" alt="Decoration">
        <section class="block-section market-section">
            <div class="register-name">
                <h2 class="block-title">Listings</h2>
            </div>  
            <div class="filter-block">
                <div class="first-filter-block">
                    <p class="offer-p">Found 500 offers</p>
                    <div class="multiselect">
                        <div class="select-btn">
                            <span>Select a category ▼</span>
                        </div>
                        <div class="options">
                            <div class="option">
                                <label for="option1">Tablet</label>
                                <input type="checkbox" id="option1" value="1">
                            </div>
                            <div class="option">
                                <label for="option2">PC</label>
                                <input type="checkbox" id="option2" value="2">
                              
                            </div>
                            <div class="option">
                                <label for="option3">Phone</label>
                                <input type="checkbox" id="option3" value="3">
                              
                            </div>
                            <div class="option">
                                <label for="option4">Laptop</label>
                                <input type="checkbox" id="option4" value="4">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="search-block">
                    <p class="search-p">Search</p>
                    <input class="filter-search" placeholder="Search" type="text">
                </div>
                <div class="sorting-block">
                    <p class="sorting-p">Sorting by</p>
                    <select class="search-category" name="category">
                        <option value="no">No criteria</option>
                        <option class="deal-option" value="cheap">From cheap to expensive</option>
                        <option class="deal-option"  value="expensive">From expensive to cheap</option>
                        <option class="deal-option"  value="first">From A to Z</option>
                        <option class="deal-option"  value="last">From Z to A</option>
                    </select>
                </div>
            </div>
            <div class="shop-block" id="shop-block"></div>
            <div class="page-switcher">
                <button class="switch-page-button" id="prev-page"><</button>
                <p class="page-text" id="page-text">1</p>
                <button class="switch-page-button" id="next-page">></button>
            </div>
            <div class="preview-item-block" style="display: none;">
                <button class="return-button">⮜</button>
                <div class="short-information-block">
                    <div class="preview-image-block">
                        <img class="preview-image" src="uploads/67d476b1d84b9_588524d86f293bbfae451a31.png" alt="image">
                    </div>
                    <div class="text-block">
                        <h2 class="preview-item-name"></h2>
                        <p class="seller-name"></p>
                        <p class="preview-brief-text"></p>
                        <p class="preview-brief-text"></p>
                        <p class="preview-brief-text"></p>
                    </div>
                </div>
                    <div class="description-block">
                    </div>
                    <div class="preview-price-block">
                        <p class="preview-price">700€</p>
                    </div>
                    <div class="buttons-block">
                        <button class="first-button">Buy</button>
                        <button class="second-button">Contact with the Seller</button>
                        <button class="third-button">Haggle</button>
                    </div>
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
    <script src="scripts/categories.js" defer></script>
    <script src="scripts/shop.js" defer></script>
    <script>    

    </script>
</body>
</html>
