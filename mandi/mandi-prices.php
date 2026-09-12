<?php
$pageTitle = 'Mandi Prices';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<main class="container">
    <section class="section">
        <h2>🍅 Live Tomato Mandi Prices</h2>
        <p class="text-muted">Real-time prices from major tomato mandis across India. Updated daily at 8 AM.</p>
        
        <div class="d-flex flex-wrap gap-2 mb-3">
            <input type="text" id="mandiSearch" class="form-control" style="max-width:260px" 
                   placeholder="Search mandi..." oninput="filterMandi()">
            <select id="varietyFilter" class="form-select" style="max-width:180px" onchange="filterMandi()">
                <option value="">All Varieties</option>
                <option value="desi">Desi/Local</option>
                <option value="hybrid">Hybrid</option>
                <option value="cherry">Cherry</option>
                <option value="roma">Roma/Plum</option>
            </select>
            <button class="btn btn-outline-tomato" onclick="locateMandis()">
                <i class="bi bi-geo-alt"></i> Find Nearby
            </button>
        </div>
        
        <div id="nearbyMsg"></div>
        
        <div class="table-responsive">
            <table class="table table-striped price-table">
                <thead>
                    <tr>
                        <th>Variety</th>
                        <th>Mandi</th>
                        <th>Min (₹/q)</th>
                        <th>Max (₹/q)</th>
                        <th>Modal (₹/q)</th>
                        <th>Trend</th>
                        <th>Distance</th>
                        <th>Updated</th>
                    </tr>
                </thead>
                <tbody id="mandiBody">
                    <tr><td colspan="8" class="text-center py-4">
                        <div class="spinner-border text-tomato"></div> Loading prices...
                    </td></tr>
                </tbody>
            </table>
        </div>
        
        <div class="alert alert-tomato mt-3">
            <i class="bi bi-info-circle"></i> <strong>Note:</strong> Prices are indicative and may vary based on quality, quantity, and negotiation. 
            Contact mandi directly for exact rates.
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>

