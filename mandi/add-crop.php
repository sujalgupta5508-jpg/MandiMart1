<?php
$pageTitle = 'Add Tomato Listing';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/auth-check.php';

// Only farmers can access
if (!isFarmer()) {
    redirect('index.php', 'Only farmers can list crops!', 'error');
}

require_once 'includes/db-connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $variety = clean($_POST['variety']);
    $qty = (int)$_POST['quantity'];
    $grade = clean($_POST['grade']);
    $price = (int)$_POST['price'];
    $location = clean($_POST['location']);
    $description = clean($_POST['description']);
    
    $imagePath = null;
    if (!empty($_FILES['image']['tmp_name'])) {
        $upload = uploadImage($_FILES['image'], 'crop_' . $_SESSION['user_id']);
        if ($upload['success']) {
            $imagePath = $upload['path'];
        }
    }
    
    $stmt = $conn->prepare("INSERT INTO crop_listings 
        (user_id, variety, quantity_quintals, grade, expected_price, location, description, image_path) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isisssss", $_SESSION['user_id'], $variety, $qty, $grade, $price, $location, $description, $imagePath);
    
    if ($stmt->execute()) {
        redirect('dashboard.php', 'Tomatoes listed successfully!', 'success');
    } else {
        $error = 'Failed to list crop. Please try again.';
    }
}
?>

<main class="container">
    <section class="section">
        <h2>🍅 List Your Tomatoes</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card p-4">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Tomato Variety</label>
                            <select name="variety" class="form-select" required>
                                <option value="Desi/Local">Desi/Local</option>
                                <option value="Hybrid TO-1057">Hybrid TO-1057</option>
                                <option value="Cherry Tomato">Cherry Tomato</option>
                                <option value="Roma/Plum">Roma/Plum</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Quantity (Quintals)</label>
                            <input type="number" name="quantity" class="form-control" min="1" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Quality Grade</label>
                            <select name="grade" class="form-select" required>
                                <option value="A">A Grade - Premium</option>
                                <option value="B" selected>B Grade - Standard</option>
                                <option value="C">C Grade - Economy</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Expected Price (₹/Quintal)</label>
                            <input type="number" name="price" class="form-control" min="1" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Location / Mandi</label>
                            <input type="text" name="location" class="form-control" 
                                   value="<?php echo $_SESSION['user_mandi'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" 
                                      placeholder="Describe your tomatoes..."></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Tomato Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                        
                        <button type="submit" class="btn btn-tomato w-100">
                            <i class="bi bi-plus-circle"></i> List Tomatoes
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card p-4 bg-light">
                    <h5><i class="bi bi-lightbulb text-tomato"></i> Pricing Tips</h5>
                    <ul class="small text-muted">
                        <li>A Grade: Premium quality, uniform size, no defects</li>
                        <li>B Grade: Standard quality, minor variations acceptable</li>
                        <li>C Grade: Economy, suitable for processing</li>
                        <li>Check current mandi prices before setting your rate</li>
                        <li>Include transport cost in your expected price</li>
                    </ul>
                    
                    <h5 class="mt-3"><i class="bi bi-camera text-tomato"></i> Photo Tips</h5>
                    <ul class="small text-muted">
                        <li>Take photo in natural daylight</li>
                        <li>Show tomatoes in a basket or crate</li>
                        <li>Include a scale reference if possible</li>
                        <li>Clear, focused images get more buyer interest</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>

