<?php require_once __DIR__ . '/functions.php'; ?>
<nav class="navbar navbar-expand-lg navbar-tomato">
    <div class="container">
        <a class="navbar-brand brand" href="index.php">
            🍅 Tomato<span>Mart</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="mandi-prices.php">Mandi Prices</a></li>
                <li class="nav-item"><a class="nav-link" href="crop-listings.php">Crop Listings</a></li>
                <li class="nav-item"><a class="nav-link" href="auctions.php">Auctions</a></li>
                <li class="nav-item"><a class="nav-link" href="compare.php">Compare</a></li>
                <li class="nav-item"><a class="nav-link" href="ai-quality.php">AI Quality</a></li>
            </ul>
            <ul class="navbar-nav">
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <li class="nav-item"><a class="nav-link" href="admin/">Admin</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link btn btn-warning btn-sm fw-bold px-3" href="login.php">Login / Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

