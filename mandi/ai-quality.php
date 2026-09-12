<?php
$pageTitle = 'AI Quality Check';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<main class="container">
    <section class="section">
        <h2>🍅 AI Tomato Quality Analysis</h2>
        <p class="text-muted">Upload two tomato batch images. Our AI analyzes freshness, color uniformity, size consistency, shape, and surface defects.</p>
        
        <div class="compare-grid">
            <div class="drop-zone" id="dz1" onclick="document.getElementById('file1').click()">
                <i class="bi bi-cloud-arrow-up fs-1"></i>
                <div>Tomato Batch 1</div>
                <div class="small text-muted">Click to upload</div>
                <input type="file" id="file1" accept="image/*" style="display:none" onchange="loadImg('file1','img1')">
                <img id="img1" style="display:none" alt="Batch 1">
            </div>
            <div class="drop-zone" id="dz2" onclick="document.getElementById('file2').click()">
                <i class="bi bi-cloud-arrow-up fs-1"></i>
                <div>Tomato Batch 2</div>
                <div class="small text-muted">Click to upload</div>
                <input type="file" id="file2" accept="image/*" style="display:none" onchange="loadImg('file2','img2')">
                <img id="img2" style="display:none" alt="Batch 2">
            </div>
        </div>
        
        <div class="text-center mt-3">
            <button class="btn btn-tomato btn-lg" onclick="runAICompare()">
                <i class="bi bi-cpu"></i> Analyze & Compare
            </button>
        </div>
        
        <div id="aiResult" class="mt-4"></div>
        
        <div class="alert alert-tomato mt-4">
            <h5><i class="bi bi-info-circle"></i> How It Works</h5>
            <p class="mb-0 small">Our AI model (powered by TensorFlow + OpenCV) analyzes:</p>
            <ul class="small mb-0">
                <li><strong>Color Analysis:</strong> Red intensity, uniformity, ripeness</li>
                <li><strong>Freshness Score:</strong> Based on shine, turgidity, stem condition</li>
                <li><strong>Size Consistency:</strong> Uniformity across the batch</li>
                <li><strong>Shape Analysis:</strong> Roundness, deformities</li>
                <li><strong>Defect Detection:</strong> Bruises, spots, cracks, pest damage</li>
            </ul>
            <p class="small mt-2 mb-0"><em>Note: This is a demonstration. Full AI integration requires Python backend with trained model.</em></p>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>

