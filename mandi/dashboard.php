<?php
$pageTitle = 'Dashboard';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/auth-check.php';
require_once 'includes/db-connect.php';

$userId = $_SESSION['user_id'];

// Get user's listings
$listings = $conn->query("SELECT * FROM crop_listings WHERE user_id = $userId ORDER BY created_at DESC");
$userListings = $listings->fetch_all(MYSQLI_ASSOC);

// Get user's auctions
$auctions = $conn->query("SELECT * FROM auctions WHERE user_id = $userId ORDER BY created_at DESC");
$userAuctions = $auctions->fetch_all(MYSQLI_ASSOC);

// Get messages
$messages = $conn->query("SELECT m.*, u.full_name as sender_name 
    FROM messages m 
    JOIN users u ON m.sender_id = u.id 
    WHERE m.receiver_id = $userId 
    ORDER BY m.created_at DESC LIMIT 10");
$userMessages = $messages->fetch_all(MYSQLI_ASSOC);
?>

<main class="container">
    <section class="section">
        <h2>👤 Welcome, <?php echo $_SESSION['user_name']; ?></h2>
        <p class="text-muted">Manage your listings, auctions, and messages.</p>
        
        <div class="row g-3">
            <!-- My Listings -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-tomato text-white">
                        <h5 class="mb-0"><i class="bi bi-box-seam"></i> My Listings</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($userListings) > 0): ?>
                            <div class="list-group">
                                <?php foreach ($userListings as $l): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-bold"><?php echo $l['variety']; ?></span>
                                            <span class="badge bg-<?php echo $l['grade'] === 'A' ? 'success' : ($l['grade'] === 'B' ? 'warning' : 'secondary'); ?>">
                                                <?php echo $l['grade']; ?>
                                            </span>
                                        </div>
                                        <div class="small text-muted">
                                            <?php echo $l['quantity_quintals']; ?> q · ₹<?php echo $l['expected_price']; ?>/q · 
                                            <span class="badge bg-<?php echo $l['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                <?php echo $l['status']; ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">No listings yet. <a href="add-crop.php">List your tomatoes</a></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- My Auctions -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-tomato text-white">
                        <h5 class="mb-0"><i class="bi bi-hammer"></i> My Auctions</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($userAuctions) > 0): ?>
                            <div class="list-group">
                                <?php foreach ($userAuctions as $a): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-bold"><?php echo $a['variety']; ?></span>
                                            <span class="badge bg-<?php echo $a['status'] === 'live' ? 'danger' : 'secondary'; ?>">
                                                <?php echo strtoupper($a['status']); ?>
                                            </span>
                                        </div>
                                        <div class="small text-muted">
                                            Current: ₹<?php echo $a['current_price']; ?>/q · 
                                            Bids: <?php echo $a['bids_count']; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">No auctions yet. <a href="create-auction.php">Create auction</a></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Messages -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-tomato text-white">
                        <h5 class="mb-0"><i class="bi bi-chat-dots"></i> Messages</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($userMessages) > 0): ?>
                            <div class="list-group">
                                <?php foreach (array_slice($userMessages, 0, 5) as $m): ?>
                                    <div class="list-group-item <?php echo $m['is_read'] ? '' : 'list-group-item-warning'; ?>">
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-bold"><?php echo $m['sender_name']; ?></span>
                                            <small class="text-muted"><?php echo formatDate($m['created_at']); ?></small>
                                        </div>
                                        <div class="small"><?php echo htmlspecialchars($m['message']); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">No messages yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>

