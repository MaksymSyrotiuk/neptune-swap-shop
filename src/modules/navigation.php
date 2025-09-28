<?php
session_start();
require "db.php";

// Initialize variables for counting offers and messages
$tradeRequestsCount = 0;
$unreadMessagesCount = 0;
$totalCount = 0;

if (isset($_SESSION["user_id"])) {
    $userId = $_SESSION["user_id"];

    // Get the current user balance
    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $_SESSION["balance"] = $stmt->fetchColumn();


    // Count offers sent to the user as a seller
    $stmt = $pdo->prepare("SELECT COUNT(*) as offer_count
                            FROM trade_requests
                            WHERE seller_id = ?");
    $stmt->execute([$userId]);
    $tradeRequestsCount = $stmt->fetchColumn();

    // Count unread messages for each chat
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as unread_messages
        FROM messages
        WHERE (sender_id != ? AND is_read = 0)
        AND chat_id IN (
            SELECT id FROM chats
            WHERE seller_id = ? OR buyer_id = ?
        )
    ");
    $stmt->execute([$userId, $userId, $userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $unreadMessagesCount = $row ? $row['unread_messages'] : 0;

    // Sum the number of offers and unread messages
    $totalCount = $tradeRequestsCount + $unreadMessagesCount;
}
?>

<label class="switcher" for="menu-switcher">
    <header>
        <i>&#xf20d;</i>
    </header>
</label>
<div class="logo-block">   
        <img src="media/Logo.svg" id="logo">
</div>
<nav-menu>
    <menu-head>
        <a class="menu-site" id="start-nav" href="index.php">Start</a>
    </menu-head>
</nav-menu>

<nav-menu>
    <menu-head class="nav-shop-button">
        <a class="menu-site" id="shop-nav" href="marketplace.php">Shop</a>
    </menu-head>
</nav-menu>

<nav-menu class="under-menu">
    <menu-head>
        <a class="menu-site">Profile<?php echo isset($_SESSION["user_id"]) ? ($totalCount > 0 ? " ($totalCount)" : "") : ""; ?> ⯆</a>
    </menu-head>
    <menu-body>
        <?php if (isset($_SESSION["user_id"])): ?>
            <div class="menu-item">
                <a class="selection" id="inventory-nav" href="messages.php">Chat<?php echo $unreadMessagesCount > 0 ? " ($unreadMessagesCount)" : ""; ?></a>
            </div>
            <div class="menu-item">
                <a class="selection" id="stats-nav" href="transactions.php">Transactions</a>
            </div>
            <div class="menu-item">
                <a class="selection" id="purchase-history-nav" href="deal-create.php">Make an offer</a>
            </div>
            <div class="menu-item">
                <a class="selection" id="purchase-history-nav" href="my-offers.php">My offers<?php echo $tradeRequestsCount > 0 ? " ($tradeRequestsCount)" : ""; ?></a>
            </div>
        <?php else: ?>
            <div class="menu-item">
                <a class="selection" id="inventory-nav" href="messages.php">Chat</a>
            </div>
            <div class="menu-item">
                <a class="selection" id="stats-nav" href="transactions.php">Transactions</a>
            </div>
            <div class="menu-item">
                <a class="selection" id="purchase-history-nav" href="deal-create.php">Make an offer</a>
            </div>
            <div class="menu-item">
                <a class="selection" id="purchase-history-nav" href="my-offers.php">My offers</a>
            </div>
        <?php endif; ?>
    </menu-body>
</nav-menu>

<?php if (isset($_SESSION["user_id"])): ?>
    <!-- Show user nickname, currency and exit button -->
    <div class="user-info-block">
        <nav-menu class="sign-in-nav">
            <menu-head>
                <span class="menu-site">👤 <?php echo $_SESSION["username"]; ?></span>
            </menu-head>
        </nav-menu>
    
        <nav-menu>
            <menu-head>
                <a class="menu-site" id="sign-up-nav" href="#"><?php echo $_SESSION["balance"]; ?>€</a>
            </menu-head>
        </nav-menu>
    
        <nav-menu>
            <menu-head>
                <a class="menu-site" href="modules/logout.php">Logout</a>
            </menu-head>
        </nav-menu>
    </div>
<?php else: ?>
    <!-- If the user is NOT logged in, we show Sign In and Sign Up -->
    <div class="user-info-block">
        <nav-menu class="sign-in-nav">
            <menu-head>
                <a class="menu-site" id="sign-in-nav" href="signin.php">Sign In</a>
            </menu-head>
        </nav-menu>

        <nav-menu>
            <menu-head>
                <a class="menu-site" id="sign-up-nav" href="signup.php">Sign Up</a>
            </menu-head>
        </nav-menu>
    </div>
<?php endif; ?>
