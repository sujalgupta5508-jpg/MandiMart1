<?php
$pageTitle = 'Crop Listings';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<main class="container">
    <section class="section">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>🍅 Tomato Listings</h2>
            <?php if (isLoggedIn() && isFarmer()): ?>
                <a href="add-crop.php" class="btn btn-tomato"><i class="bi bi-plus-circle"></i> List Your Tomatoes</a>
            <?php elseif (!isLoggedIn()): ?>
                <a href="login.php" class="btn btn-outline-tomato">Login to List</a>
            <?php endif; ?>
        </div>
        
        <div class="row g-3" id="cropList">
            <div class="col-12 text-center py-4">
                <div class="spinner-border text-tomato"></div> Loading listings...
            </div>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>

