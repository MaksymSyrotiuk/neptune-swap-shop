<?php
require "../src/modules/db.php";
session_start();

// Check if the offer ID is passed in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request: Offer ID is missing.");
}

$offerId = intval($_GET['id']);

// Get the offer title
$stmt = $pdo->prepare("SELECT title FROM ads WHERE id = ?");
$stmt->execute([$offerId]);
$offer = $stmt->fetch(PDO::FETCH_ASSOC);
$offerTitle = $offer ? htmlspecialchars($offer['title']) : "Unknown Offer";

// Get the total number of requests
$stmt = $pdo->prepare("SELECT COUNT(*) FROM trade_requests WHERE product_id = ?");
$stmt->execute([$offerId]);
$totalSuggestions = $stmt->fetchColumn();

$limit = 8; // Number of requests per page
$totalPages = $totalSuggestions > 0 ? ceil($totalSuggestions / $limit) : 1;
$page = isset($_GET['page']) ? max(1, min(intval($_GET['page']), $totalPages)) : 1;
$offset = ($page - 1) * $limit;

// Query requests for the current page
$stmt = $pdo->prepare("
    SELECT tr.*, u.username 
    FROM trade_requests tr
    JOIN users u ON tr.buyer_id = u.id
    WHERE tr.product_id = ?
    ORDER BY tr.created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->bindValue(1, $offerId, PDO::PARAM_INT);
$stmt->bindValue(2, (int)$limit, PDO::PARAM_INT);
$stmt->bindValue(3, (int)$offset, PDO::PARAM_INT);
$stmt->execute();
$suggestions = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$suggestions) {
    $suggestions = [];
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
    <title>Suggestions</title>
</head>
<body>
    <nav id="navigation"></nav>
    <main>
        <section class="block-section market-section">
            <div class="register-name">
                <h2 class="block-title">Suggestions</h2>
            </div>

            <h2 class="request-title"><?php echo $offerTitle; ?> - Price reduction request</h2>

            <?php if (count($suggestions) > 0): ?>
                <div class="suggestions">
                    <?php foreach ($suggestions as $suggestion): ?>
                        <div class="suggestion-block">
                            <div class="suggestion-titel">
                                <p class="suggestion-name">Price reduction request - ID <?php echo $suggestion['id']; ?></p>
                            </div>
                            <p class="customer-name">Customer: <?php echo htmlspecialchars($suggestion['username']); ?></p>
                            <p class="suggestion-price">Price: <?php echo htmlspecialchars($suggestion['price']); ?>€</p>
                            <div class="button-block">
                                <button class="first-button block-button" data-id="<?php echo $suggestion['id']; ?>">Accept</button>
                                <button class="second-button block-button" data-id="<?php echo $suggestion['id']; ?>">Decline</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="page-switcher">
                    <?php if ($page > 1): ?>
                        <a href="?id=<?php echo $offerId; ?>&page=<?php echo $page - 1; ?>" class="switch-page-button"><</a>
                    <?php endif; ?>
                    <p class="page-text">Page <?php echo $page; ?> of <?php echo $totalPages; ?></p>
                    <?php if ($page < $totalPages): ?>
                        <a href="?id=<?php echo $offerId; ?>&page=<?php echo $page + 1; ?>" class="switch-page-button">></a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p class="no-suggestions">No suggestions available for this offer.</p>
            <?php endif; ?>
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

    <script src="scripts/declineOffer.js" defer></script>
    <script src="scripts/acceptOffer.js" defer></script>
</body>
</html>
