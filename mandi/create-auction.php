<?php
$pageTitle = 'Create Auction';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/auth-check.php';

if (!isFarmer()) {
    redirect('index.php', 'Only farmers can create auctions!', 'error');
}

require_once 'includes/db-connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $variety = clean($_POST['variety']);
    $qty = (int)$_POST['quantity'];
    $basePrice = (int)$_POST['base_price'];
    $duration = (int)$_POST['duration'];
    $grade = clean($_POST['grade']);
    
    $endsAt = date('Y-m-d H:i:s', strtotime("+{$duration} minutes"));
    
    $stmt = $conn->prepare("INSERT INTO auctions 
        (user_id, variety, quantity_quintals, base_price, current_price, ends_at, grade) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isiiiss", $_SESSION['user_id'], $variety, $qty, $basePrice, $basePrice, $endsAt, $grade);
    
    if ($stmt->execute()) {
        redirect('auctions.php', 'Auction started! Buyers can now bid.', 'success');
    } else {
        $error = 'Failed to create auction.';
    }
}
?>

<main class="container">
    <section class="section">
        <h2>🍅 Create Tomato Auction</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card p-4">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Tomato Variety</label>
                            <select name="variety" class="form-select" required>
                                <option>Desi/Local</option>
                                <option>Hybrid TO-1057</option>
                                <option>Cherry Tomato</option>
                                <option>Roma/Plum</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Quantity (Quintals)</label>
                            <input type="number" name="quantity" class="form-control" min="10" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Base Price (₹/Quintal)</label>
                            <input type="number" name="base_price" class="form-control" min="1" required>
                            <div class="form-text">Bidding starts at this price. Each bid increases by 5%.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Duration</label>
                            <select name="duration" class="form-select" required>
                                <option value="15">15 minutes</option>
                                <option value="30" selected>30 minutes</option>
                                <option value="45">45 minutes</option>
                                <option value="60">1 hour</option>
                                <option value="120">2 hours</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Grade</label>
                            <select name="grade" class="form-select" required>
                                <option>A Grade</option>
                                <option selected>B Grade</option>
                                <option>C Grade</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-tomato w-100">
                            <i class="bi bi-hammer"></i> Start Auction
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="alert alert-tomato">
                    <h5><i class="bi bi-info-circle"></i> How Auctions Work</h5>
                    <ol class="mb-0">
                        <li>Set your base price and auction duration</li>
                        <li>Buyers place bids (each bid +5% over current)</li>
                        <li>Highest bidder wins when timer ends</li>
                        <li>We notify both parties to complete the deal</li>
                        <li>No commission — direct farmer-to-buyer!</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>

