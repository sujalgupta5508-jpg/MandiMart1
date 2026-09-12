<?php
$pageTitle = 'Compare Tomatoes';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/db-connect.php';

// Get active listings for comparison
$result = $conn->query("SELECT c.*, u.full_name, u.rating, u.mandi_name 
    FROM crop_listings c 
    JOIN users u ON c.user_id = u.id 
    WHERE c.status = 'active' 
    ORDER BY c.created_at DESC LIMIT 20");
$listings = $result->fetch_all(MYSQLI_ASSOC);
?>

<main class="container">
    <section class="section">
        <h2>🍅 Tomato Comparison Tool</h2>
        <p class="text-muted">Compare different tomato listings side by side. Make informed buying decisions.</p>
        
        <?php if (count($listings) >= 2): ?>
        <div class="row g-3 mb-4">
            <div class="col-md-5">
                <label class="form-label">Select First Listing</label>
                <select id="compA" class="form-select" onchange="updateCompare()">
                    <?php foreach ($listings as $i => $l): ?>
                        <option value="<?php echo $i; ?>" 
                                data-variety="<?php echo $l['variety']; ?>"
                                data-price="<?php echo $l['expected_price']; ?>"
                                data-grade="<?php echo $l['grade']; ?>"
                                data-qty="<?php echo $l['quantity_quintals']; ?>"
                                data-seller="<?php echo $l['full_name']; ?>"
                                data-rating="<?php echo $l['rating']; ?>"
                                data-location="<?php echo $l['location']; ?>">
                            <?php echo $l['variety']; ?> - <?php echo $l['full_name']; ?> (₹<?php echo $l['expected_price']; ?>/q)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 text-center align-self-center">
                <i class="bi bi-arrow-left-right fs-2 text-tomato"></i>
            </div>
            <div class="col-md-5">
                <label class="form-label">Select Second Listing</label>
                <select id="compB" class="form-select" onchange="updateCompare()">
                    <?php foreach ($listings as $i => $l): ?>
                        <option value="<?php echo $i; ?>" 
                                <?php echo $i === 1 ? 'selected' : ''; ?>
                                data-variety="<?php echo $l['variety']; ?>"
                                data-price="<?php echo $l['expected_price']; ?>"
                                data-grade="<?php echo $l['grade']; ?>"
                                data-qty="<?php echo $l['quantity_quintals']; ?>"
                                data-seller="<?php echo $l['full_name']; ?>"
                                data-rating="<?php echo $l['rating']; ?>"
                                data-location="<?php echo $l['location']; ?>">
                            <?php echo $l['variety']; ?> - <?php echo $l['full_name']; ?> (₹<?php echo $l['expected_price']; ?>/q)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div id="compareResult"></div>
        
        <script>
        function updateCompare() {
            const selA = document.getElementById('compA');
            const selB = document.getElementById('compB');
            const optA = selA.options[selA.selectedIndex];
            const optB = selB.options[selB.selectedIndex];
            
            const dataA = {
                variety: optA.dataset.variety,
                price: +optA.dataset.price,
                grade: optA.dataset.grade,
                qty: +optA.dataset.qty,
                seller: optA.dataset.seller,
                rating: +optA.dataset.rating,
                location: optA.dataset.location
            };
            const dataB = {
                variety: optB.dataset.variety,
                price: +optB.dataset.price,
                grade: optB.dataset.grade,
                qty: +optB.dataset.qty,
                seller: optB.dataset.seller,
                rating: +optB.dataset.rating,
                location: optB.dataset.location
            };
            
            const scoreA = (dataA.rating * 10) + (dataA.grade === 'A' ? 5 : dataA.grade === 'B' ? 3 : 0) - (dataA.price / 1000);
            const scoreB = (dataB.rating * 10) + (dataB.grade === 'A' ? 5 : dataB.grade === 'B' ? 3 : 0) - (dataB.price / 1000);
            const best = scoreA >= scoreB ? dataA : dataB;
            
            const gradeColor = g => g === 'A' ? 'success' : g === 'B' ? 'warning' : 'secondary';
            
            document.getElementById('compareResult').innerHTML = `
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-tomato">
                            <tr>
                                <th>Attribute</th>
                                <th class="text-tomato">${dataA.variety} (${dataA.seller})</th>
                                <th class="text-tomato">${dataB.variety} (${dataB.seller})</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td><strong>Price</strong></td><td>₹${dataA.price}/q</td><td>₹${dataB.price}/q</td></tr>
                            <tr><td><strong>Grade</strong></td>
                                <td><span class="badge bg-${gradeColor(dataA.grade)}">${dataA.grade}</span></td>
                                <td><span class="badge bg-${gradeColor(dataB.grade)}">${dataB.grade}</span></td></tr>
                            <tr><td><strong>Quantity</strong></td><td>${dataA.qty} q</td><td>${dataB.qty} q</td></tr>
                            <tr><td><strong>Rating</strong></td><td>⭐${dataA.rating}</td><td>⭐${dataB.rating}</td></tr>
                            <tr><td><strong>Location</strong></td><td>${dataA.location}</td><td>${dataB.location}</td></tr>
                            <tr><td><strong>Value Score</strong></td>
                                <td class="${scoreA >= scoreB ? 'fw-bold text-success' : ''}">${scoreA.toFixed(1)}</td>
                                <td class="${scoreB >= scoreA ? 'fw-bold text-success' : ''}">${scoreB.toFixed(1)}</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="alert alert-success">
                    <i class="bi bi-trophy-fill"></i> <strong>Recommendation:</strong> 
                    ${best.variety} from ${best.seller} offers better value 
                    (Grade ${best.grade}, ₹${best.price}/q, ⭐${best.rating}).
                </div>
            `;
        }
        document.addEventListener('DOMContentLoaded', updateCompare);
        </script>
        
        <?php else: ?>
            <div class="alert alert-info">Need at least 2 active listings to compare. Check back soon!</div>
        <?php endif; ?>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>

