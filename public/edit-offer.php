<?php
require "../src/modules/db.php";

session_start();
if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in.");
}

$user_id = $_SESSION['user_id'];
$offer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($offer_id <= 0) {
    die("Invalid offer ID.");
}

// Getting ad data from the database
$query = $pdo->prepare("SELECT * FROM ads WHERE id = :offer_id AND user_id = :user_id");
$query->execute(['offer_id' => $offer_id, 'user_id' => $user_id]);
$offer = $query->fetch(PDO::FETCH_ASSOC);

if (!$offer) {
    die("Offer not found or you don't have permission to edit it.");
}

$is_editable = ($offer['status'] === 'active'); // 💡 Статус
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
    <title>Edit Offer - <?php echo htmlspecialchars($offer['title']); ?></title>
</head>
<body>
    <nav id="navigation"></nav>
    <main>
        <img class="decoration register-decoration" src="media/Decoration.svg" alt="Decoration">
        <section class="block-section">
            <div class="register-name">
                <h2 class="block-title"><?= !$is_editable ? 'Description of' : 'Edit' ?> "<?php echo htmlspecialchars($offer['title']); ?>"</h2>
            </div>
            <form action="../src/modules/update_offer.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="offer_id" value="<?= $offer_id ?>">
            
                <h2 class="block-h2"><?= $is_editable ? 'New information' : 'Offer information' ?></h2>
            
                <label class="text-for-block">Offername</label>
                <input type="text" name="offername" maxlength = "30" value="<?= htmlspecialchars($offer['title']) ?>" placeholder="Offer Name" <?= !$is_editable ? 'readonly' : '' ?>>
            
                <label class="text-for-block">Photo of product (recommend size 256x256 px)</label>
                <label class="input-file">
                    <input type="file" name="file" accept="image/png" <?= !$is_editable ? 'disabled' : '' ?>>
                    <span class="input-file-btn"><?= $is_editable ? 'Insert' : 'Uploaded' ?></span>
                    <span class="input-file-text"><?= $offer['photo'] ? htmlspecialchars($offer['photo']) : 'Select the file' ?></span>
                </label>
            
                <label class="text-for-block">Brief information 1</label>
                <input maxlength = "35" type="text" name="brief-information-1" value="<?= htmlspecialchars($offer['brief1']) ?>" placeholder="Brief information 1" <?= !$is_editable ? 'readonly' : '' ?>>
            
                <label class="text-for-block">Brief information 2</label>
                <input maxlength = "35" type="text" name="brief-information-2" value="<?= htmlspecialchars($offer['brief2']) ?>" placeholder="Brief information 2" <?= !$is_editable ? 'readonly' : '' ?>>
            
                <label class="text-for-block">Brief information 3</label>
                <input maxlength = "35" type="text" name="brief-information-3" value="<?= htmlspecialchars($offer['brief3']) ?>" placeholder="Brief information 3" <?= !$is_editable ? 'readonly' : '' ?>>
            
                <label class="text-for-block">Description</label>
                <textarea class="description-input" name="description" placeholder="Description" <?= !$is_editable ? 'readonly' : '' ?>><?= htmlspecialchars($offer['description']) ?></textarea>
            
                <label class="text-for-block">Price</label>
                <input class="price-input" type="text" name="price" value="<?= htmlspecialchars($offer['price']) ?>" placeholder="Price" <?= !$is_editable ? 'readonly' : '' ?>>
            
                <?php if ($is_editable): ?>
                    <button type="submit" class="register-button">Save Changes</button>
                <?php endif; ?>
            </form>
        </section>
    </main>

    <script>
const priceInput = document.querySelectorAll(".price-input");

function marginBottomPrice() {
    if (!document.querySelectorAll(".register-button").length) {
        priceInput.forEach(element => {
            element.style.marginBottom = "20px";
        });
    }
}

marginBottomPrice();
    </script>

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
            const firstInputX = Array.from(inputs).find(input => input.offsetParent !== null).getBoundingClientRect().left;
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
