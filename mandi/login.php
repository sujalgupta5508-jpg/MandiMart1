<?php
$pageTitle = 'Login';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/db-connect.php';

$error = '';
$activeTab = 'login';

// Handle Login
if (isset($_POST['login'])) {
    $phone = clean($_POST['phone']);
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE phone = ? AND status = 'active'");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['user_mandi'] = $user['mandi_name'];
            
            if ($user['user_type'] === 'admin') {
                redirect('admin/', 'Welcome, Admin!');
            } else {
                redirect('dashboard.php', 'Welcome back, ' . $user['full_name'] . '!', 'success');
            }
        } else {
            $error = 'Invalid password';
        }
    } else {
        $error = 'User not found or account pending';
    }
}

// Handle Registration
if (isset($_POST['register'])) {
    $name = clean($_POST['reg_name']);
    $phone = clean($_POST['reg_phone']);
    $email = clean($_POST['reg_email']);
    $password = $_POST['reg_password'];
    $type = clean($_POST['reg_type']);
    $mandi = clean($_POST['reg_mandi']);
    
    if (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
        $activeTab = 'register';
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $status = ($type === 'buyer') ? 'active' : 'pending'; // Farmers need verification
        
        $stmt = $conn->prepare("INSERT INTO users (full_name, phone, email, password_hash, user_type, mandi_name, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $name, $phone, $email, $hash, $type, $mandi, $status);
        
        if ($stmt->execute()) {
            $success = 'Account created! ' . ($type === 'farmer' ? 'Pending admin verification.' : 'You can now login.');
            $activeTab = 'login';
        } else {
            $error = 'Phone number already registered';
            $activeTab = 'register';
        }
    }
}
?>

<main class="container">
    <section class="section">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card p-4">
                    
                    <!-- Tabs -->
                    <ul class="nav nav-pills nav-pills-tomato mb-3" id="authTabs">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $activeTab === 'login' ? 'active' : ''; ?>" 
                               href="#" onclick="showTab('login')">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $activeTab === 'register' ? 'active' : ''; ?>" 
                               href="#" onclick="showTab('register')">Register</a>
                        </li>
                    </ul>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <!-- Login Form -->
                    <div id="loginForm" style="<?php echo $activeTab !== 'login' ? 'display:none' : ''; ?>">
                        <h4 class="mb-3">Welcome Back</h4>
                        <form method="POST">
                            <input type="hidden" name="login" value="1">
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" name="phone" class="form-control" placeholder="9876543210" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-tomato w-100 mb-2">Login</button>
                            <div class="text-center">
                                <small class="text-muted">Demo: 9876543210 / password123</small>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Register Form -->
                    <div id="registerForm" style="<?php echo $activeTab !== 'register' ? 'display:none' : ''; ?>">
                        <h4 class="mb-3">Create Account</h4>
                        <form method="POST">
                            <input type="hidden" name="register" value="1">
                            <div class="mb-2">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="reg_name" class="form-control" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="reg_phone" class="form-control" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Email (optional)</label>
                                <input type="email" name="reg_email" class="form-control">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">I am a</label>
                                <select name="reg_type" class="form-select" required>
                                    <option value="farmer">Farmer</option>
                                    <option value="buyer">Buyer</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Nearest Mandi</label>
                                <select name="reg_mandi" class="form-select" required>
                                    <option value="">Select Mandi</option>
                                    <option>Azadpur Mandi, Delhi</option>
                                    <option>Ghazipur Mandi, Delhi</option>
                                    <option>Keshopur Mandi, Delhi</option>
                                    <option>Okhla Mandi, Delhi</option>
                                    <option>Narela Mandi, Delhi</option>
                                    <option>Karnal Mandi, Haryana</option>
                                    <option>Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="reg_password" class="form-control" minlength="6" required>
                            </div>
                            <button type="submit" class="btn btn-tomato w-100">Create Account</button>
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>
    </section>
</main>

<script>
function showTab(tab) {
    document.getElementById('loginForm').style.display = tab === 'login' ? 'block' : 'none';
    document.getElementById('registerForm').style.display = tab === 'register' ? 'block' : 'none';
    document.querySelectorAll('#authTabs .nav-link').forEach(el => el.classList.remove('active'));
    event.target.classList.add('active');
}
</script>

<?php require_once 'includes/footer.php'; ?>

