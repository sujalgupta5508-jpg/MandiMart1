<?php
$pageTitle = 'Live Auctions';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<main class="container">
    <section class="section">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>🍅 Live Tomato Auctions</h2>
            <?php if (isLoggedIn() && isFarmer()): ?>
                <a href="create-auction.php" class="btn btn-tomato">
                    <i class="bi bi-hammer"></i> Create Auction
                </a>
            <?php endif; ?>
        </div>
        
        <p class="text-muted">Real-time auctions. Place your bid — highest bidder wins when timer ends!</p>
        
        <div class="row g-3" id="auctionList">
            <div class="col-12 text-center py-4">
                <div class="spinner-border text-tomato"></div> Loading auctions...
            </div>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>

