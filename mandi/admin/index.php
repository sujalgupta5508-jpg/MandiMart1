<?php
$pageTitle = 'Admin Dashboard';
require_once '../includes/header.php';
require_once '../includes/auth-check.php';

if (!isAdmin()) {
    redirect('../index.php', 'Access denied.', 'error');
}

require_once '../includes/db-connect.php';

// Get stats
$stats = [
    'users' => $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'],
    'farmers' => $conn->query("SELECT COUNT(*) as c FROM users WHERE user_type = 'farmer'")->fetch_assoc()['c'],
    'buyers' => $conn->query("SELECT COUNT(*) as c FROM users WHERE user_type = 'buyer'")->fetch_assoc()['c'],
    'listings' => $conn->query("SELECT COUNT(*) as c FROM crop_listings")->fetch_assoc()['c'],
    'auctions' => $conn->query("SELECT COUNT(*) as c FROM auctions")->fetch_assoc()['c'],
    'orders' => $conn->query("SELECT COALESCE(SUM(total_amount), 0) as c FROM orders WHERE status = 'completed'")->fetch_assoc()['c']
];
?>

<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand">🍅 TomatoMart Admin</span>
        <a href="../logout.php" class="btn btn-sm btn-outline-light">Logout</a>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 bg-light min-vh-100 p-3">
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link active" href="index.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
                <li class="nav-item"><a class="nav-link" href="listings.php">Listings</a></li>
                <li class="nav-item"><a class="nav-link" href="auctions.php">Auctions</a></li>
                <li class="nav-item"><a class="nav-link" href="transactions.php">Transactions</a></li>
            </ul>
        </div>
        
        <!-- Main -->
        <div class="col-md-10 p-4">
            <h2>Admin Dashboard</h2>
            
            <div class="row g-3 mb-4">
                <div class="col-md-2">
                    <div class="card p-3 text-center">
                        <div class="fs-3 fw-bold text-tomato"><?php echo $stats['users']; ?></div>
                        <div class="small text-muted">Total Users</div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card p-3 text-center">
                        <div class="fs-3 fw-bold text-tomato"><?php echo $stats['farmers']; ?></div>
                        <div class="small text-muted">Farmers</div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card p-3 text-center">
                        <div class="fs-3 fw-bold text-tomato"><?php echo $stats['buyers']; ?></div>
                        <div class="small text-muted">Buyers</div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card p-3 text-center">
                        <div class="fs-3 fw-bold text-tomato"><?php echo $stats['listings']; ?></div>
                        <div class="small text-muted">Listings</div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card p-3 text-center">
                        <div class="fs-3 fw-bold text-tomato"><?php echo $stats['auctions']; ?></div>
                        <div class="small text-muted">Auctions</div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card p-3 text-center">
                        <div class="fs-3 fw-bold text-tomato">₹<?php echo number_format($stats['orders']); ?></div>
                        <div class="small text-muted">Trade Value</div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="card">
                <div class="card-header">Recent Activity</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead><tr><th>Time</th><th>Action</th><th>User</th><th>Details</th></tr></thead>
                        <tbody>
                            <tr><td>Just now</td><td>New listing</td><td>Ramesh Kumar</td><td>50q Desi Tomato @ ₹1,500</td></tr>
                            <tr><td>5 min ago</td><td>Bid placed</td><td>Amit Patel</td><td>Auction #12 - ₹2,520</td></tr>
                            <tr><td>12 min ago</td><td>User registered</td><td>New Farmer</td><td>Phone: 98XXXXXX55</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

