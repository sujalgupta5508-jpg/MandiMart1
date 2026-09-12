<footer class="mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5>🍅 Tomato MandiMart</h5>
                <p>India's first tomato-exclusive marketplace. Direct farmer-to-buyer trading with zero middlemen.</p>
            </div>
            <div class="col-md-4">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="mandi-prices.php">Mandi Prices</a></li>
                    <li><a href="crop-listings.php">Crop Listings</a></li>
                    <li><a href="auctions.php">Live Auctions</a></li>
                    <li><a href="about.php">About Us</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Contact</h5>
                <p><i class="bi bi-envelope"></i> support@tomatomart.com<br>
                <i class="bi bi-phone"></i> 1800-TOMATO-1<br>
                <i class="bi bi-geo-alt"></i> Delhi, India</p>
            </div>
        </div>
        <hr class="my-3">
        <p class="text-center mb-0">🍅 Tomato MandiMart — College Project | Direct Trade · Live Prices · AI Quality</p>
    </div>
</footer>

<!-- Chatbot -->
<button class="chat-fab" onclick="toggleChat()"><i class="bi bi-chat-dots-fill"></i></button>
<div class="chat-box" id="chatBox">
    <div class="chat-head">
        <span><i class="bi bi-robot"></i> TomatoBot</span>
        <button class="btn btn-sm btn-light" onclick="toggleChat()">✕</button>
    </div>
    <div class="chat-body" id="chatBody">
        <div class="msg bot">👋 Namaste! I'm TomatoBot. Ask me about tomato prices, mandis, auctions, or farming tips!</div>
    </div>
    <div class="chat-input">
        <input type="text" id="chatInput" placeholder="Type your question..." onkeydown="if(event.key==='Enter')sendChat()">
        <button onclick="sendChat()"><i class="bi bi-send"></i></button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
<?php if (isset($extraJs)) echo $extraJs; ?>
</body>
</html>

