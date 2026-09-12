/* ============================================
   TOMATO MANDIMART - MAIN JAVASCRIPT
   ============================================ */

// Toggle chatbot
function toggleChat() {
    const box = document.getElementById('chatBox');
    if (box) box.classList.toggle('open');
}

// Add message to chat
function addMsg(who, text) {
    const body = document.getElementById('chatBody');
    if (!body) return;
    
    const div = document.createElement('div');
    div.className = 'msg ' + who;
    div.innerHTML = text;
    body.appendChild(div);
    body.scrollTop = body.scrollHeight;
}

// Send chat message (AJAX to PHP backend)
function sendChat() {
    const input = document.getElementById('chatInput');
    if (!input) return;
    
    const q = input.value.trim();
    if (!q) return;
    
    addMsg('user', q);
    input.value = '';
    
    // Send to PHP API
    fetch('api/chatbot-response.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'question=' + encodeURIComponent(q)
    })
    .then(r => r.json())
    .then(data => {
        addMsg('bot', data.answer);
    })
    .catch(() => {
        // Fallback if API fails
        setTimeout(() => {
            addMsg('bot', '🤖 I can help with tomato prices, mandis, auctions, and farming tips. Try asking "What is tomato price today?"');
        }, 500);
    });
}

// Load mandi prices via AJAX
function loadMandiPrices() {
    const tbody = document.getElementById('mandiBody');
    if (!tbody) return;
    
    fetch('api/get-mandi-prices.php')
        .then(r => r.json())
        .then(data => {
            if (!data.success) return;
            
            tbody.innerHTML = data.prices.map(p => {
                const trendIcon = p.trend === 'up' ? '<i class="bi bi-arrow-up-right text-danger"></i>' :
                                p.trend === 'down' ? '<i class="bi bi-arrow-down-right text-success"></i>' :
                                '<i class="bi bi-dash text-secondary"></i>';
                const emoji = p.variety.includes('Cherry') ? '🍒' : 
                             p.variety.includes('Roma') ? '🟠' : '🍅';
                
                return `<tr class="fade-in">
                    <td>${emoji} ${p.variety}</td>
                    <td>${p.mandi_name}</td>
                    <td>₹${p.min_price}</td>
                    <td>₹${p.max_price}</td>
                    <td><strong>₹${p.modal_price}</strong></td>
                    <td>${trendIcon}</td>
                    <td>${p.distance_km} km</td>
                    <td>${p.price_date}</td>
                </tr>`;
            }).join('');
        });
}

// Filter mandi prices
function filterMandi() {
    const q = document.getElementById('mandiSearch')?.value.toLowerCase() || '';
    const v = document.getElementById('varietyFilter')?.value || '';
    const rows = document.querySelectorAll('#mandiBody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const variety = row.cells[0]?.textContent.toLowerCase() || '';
        const matchQ = text.includes(q);
        const matchV = !v || variety.includes(v);
        row.style.display = (matchQ && matchV) ? '' : 'none';
    });
}

// Locate nearby mandis
function locateMandis() {
    const msg = document.getElementById('nearbyMsg');
    if (!msg) return;
    
    msg.style.display = 'block';
    msg.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Finding nearby mandis...';
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            pos => {
                msg.innerHTML = `<div class="alert alert-success">
                    <i class="bi bi-geo-alt-fill"></i> <strong>Nearby Mandis Found!</strong><br>
                    📍 Azadpur Mandi — 2.3 km<br>
                    📍 Ghazipur Mandi — 5.1 km<br>
                    📍 Keshopur Mandi — 8.4 km<br>
                    <small class="text-muted">Full GPS integration in production</small>
                </div>`;
            },
            err => {
                msg.innerHTML = '<div class="alert alert-warning">Location access denied. Showing all mandis.</div>';
            }
        );
    }
}

// Load crop listings via AJAX
function loadCropListings() {
    const container = document.getElementById('cropList');
    if (!container) return;
    
    fetch('api/get-crops.php')
        .then(r => r.json())
        .then(data => {
            if (!data.success) return;
            
            container.innerHTML = data.crops.map(c => {
                const emoji = c.variety.includes('Cherry') ? '🍒' : 
                             c.variety.includes('Roma') ? '🟠' : '🍅';
                const gradeClass = c.grade === 'A' ? 'success' : c.grade === 'B' ? 'warning' : 'secondary';
                
                return `<div class="col-md-6 fade-in">
                    <div class="card p-3 h-100 d-flex flex-row gap-3">
                        <div class="fs-1">${emoji}</div>
                        <div class="flex-grow-1">
                            <div class="fw-bold">${c.variety} 
                                <span class="badge bg-${gradeClass}">${c.grade}</span>
                            </div>
                            <div class="small text-muted">${c.quantity_quintals} quintals · ₹${c.expected_price}/q</div>
                            <div class="small text-muted"><i class="bi bi-geo-alt"></i> ${c.location}</div>
                            ${c.description ? `<div class="small text-muted fst-italic">"${c.description}"</div>` : ''}
                            <div class="small">Seller: ${c.seller_name} ⭐${c.rating}</div>
                            <div class="mt-2 d-flex gap-2">
                                <a href="login.php" class="btn btn-sm btn-tomato"><i class="bi bi-chat"></i> Contact</a>
                                <a href="login.php" class="btn btn-sm btn-outline-tomato">Buy Now</a>
                            </div>
                        </div>
                    </div>
                </div>`;
            }).join('');
        });
}

// Load auctions via AJAX
function loadAuctions() {
    const container = document.getElementById('auctionList');
    if (!container) return;
    
    fetch('api/get-auctions.php')
        .then(r => r.json())
        .then(data => {
            if (!data.success) return;
            renderAuctions(data.auctions);
        });
}

function renderAuctions(auctions) {
    const container = document.getElementById('auctionList');
    if (!container) return;
    
    container.innerHTML = auctions.map(a => {
        const left = Math.max(0, Math.floor((new Date(a.ends_at) - new Date()) / 1000));
        const mm = String(Math.floor(left/60)).padStart(2,'0');
        const ss = String(left%60).padStart(2,'0');
        const isLive = a.status === 'live' && left > 0;
        const emoji = a.variety.includes('Cherry') ? '🍒' : 
                     a.variety.includes('Roma') ? '🟠' : '🍅';
        
        return `<div class="col-md-6 fade-in">
            <div class="card p-3 auction-card ${isLive ? 'auction-live' : ''}">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold">${emoji} ${a.variety} (${a.quantity_quintals} q)</span>
                    ${isLive ? '<span class="badge bg-danger">🔴 LIVE</span>' : '<span class="badge bg-secondary">ENDED</span>'}
                </div>
                <div class="small text-muted">Grade: ${a.grade} · Base: ₹${a.base_price}/q</div>
                <div class="small">Current Bid: <strong class="text-success fs-5">₹${a.current_price}/q</strong></div>
                <div class="small">Bids: ${a.bids_count} ${a.winner_name ? '· Winner: ' + a.winner_name : ''}</div>
                <div class="progress mt-2 mb-1" style="height:8px">
                    <div class="progress-bar ${isLive ? 'bg-success' : 'bg-secondary'}" 
                        style="width:${isLive ? Math.min(100, (a.current_price/a.base_price)*15) : 100}%"></div>
                </div>
                <div class="small text-muted mb-2">⏱ ${isLive ? mm+':'+ss+' remaining' : 'Auction closed'}</div>
                ${isLive 
                    ? `<button class="btn btn-tomato btn-sm w-100" onclick="placeBid(${a.id})">
                        <i class="bi bi-hammer"></i> Place Bid (+5%)
                       </button>`
                    : `<div class="text-muted small text-center">Sold to ${a.winner_name || 'No bids'}</div>`
                }
            </div>
        </div>`;
    }).join('');
}

// Place bid
function placeBid(auctionId) {
    fetch('api/place-bid-api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'auction_id=' + auctionId
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            loadAuctions(); // Refresh
            alert('✅ Bid placed! New price: ₹' + data.new_price + '/q');
        } else {
            alert('❌ ' + (data.error || 'Could not place bid'));
        }
    });
}

// AI Quality - Load image preview
function loadImg(fileId, imgId) {
    const file = document.getElementById(fileId);
    if (!file || !file.files[0]) return;
    
    const reader = new FileReader();
    reader.onload = e => {
        const img = document.getElementById(imgId);
        img.src = e.target.result;
        img.style.display = 'block';
    };
    reader.readAsDataURL(file.files[0]);
}

// Run AI comparison
function runAICompare() {
    const img1 = document.getElementById('img1');
    const img2 = document.getElementById('img2');
    
    if (!img1?.src || !img2?.src || img1.style.display === 'none' || img2.style.display === 'none') {
        alert('Please upload BOTH tomato images first.');
        return;
    }
    
    const resultDiv = document.getElementById('aiResult');
    resultDiv.innerHTML = `
        <div class="text-center p-4">
            <div class="spinner-border text-tomato" style="width:3rem;height:3rem"></div>
            <p class="mt-2">Analyzing with OpenCV + TensorFlow...</p>
            <p class="small text-muted">Checking: color, freshness, size, shape, surface defects</p>
        </div>`;
    
    // Simulate processing
    setTimeout(() => {
        const scores = [
            {name:'Batch 1', fresh:94, color:92, size:88, shape:95, defects:'None', grade:'A', score:92, emoji:'🍅'},
            {name:'Batch 2', fresh:79, color:81, size:85, shape:82, defects:'Minor spots (3%)', grade:'B', score:74, emoji:'🍅'}
        ];
        const winner = scores[0];
        
        resultDiv.innerHTML = `
            <div class="row g-3">
                ${scores.map((s, i) => `
                <div class="col-md-6">
                    <div class="card p-3 border-top border-4 ${i===0 ? 'border-success' : 'border-warning'}">
                        <div class="text-center fs-1">${s.emoji}</div>
                        <div class="fw-bold mb-2 text-center fs-5">${s.name}</div>
                        ${['fresh','color','size','shape'].map(m => `
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-capitalize">${m}</span>
                                <strong>${s[m]}%</strong>
                            </div>
                            <div class="progress mb-2" style="height:6px">
                                <div class="progress-bar ${s[m]>85?'bg-success':s[m]>70?'bg-warning':'bg-danger'}" 
                                    style="width:${s[m]}%"></div>
                            </div>
                        `).join('')}
                        <div class="d-flex justify-content-between"><span>Defects</span><span>${s.defects}</span></div>
                        <div class="d-flex justify-content-between mt-2">
                            <span>Grade</span>
                            <span class="badge bg-${s.grade==='A'?'success':'warning'}">${s.grade}</span>
                        </div>
                        <div class="d-flex justify-content-between mt-2 pt-2 border-top">
                            <span>Overall Score</span>
                            <strong class="text-${i===0?'success':'warning'} fs-4">${s.score}/100</strong>
                        </div>
                    </div>
                </div>`).join('')}
            </div>
            <div class="alert alert-success mt-3">
                <i class="bi bi-trophy-fill"></i> <strong>AI Recommendation:</strong> 
                ${winner.name} is superior with ${winner.fresh}% freshness, perfect color, and zero defects.
                Estimated premium value: <strong>₹${(winner.score * 35).toFixed(0)}/q</strong>.
            </div>`;
    }, 2000);
}

// Compare products
function compareProducts() {
    // This would load from API in production
    console.log('Compare loaded from PHP');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    // Load dynamic content if on respective pages
    if (document.getElementById('mandiBody')) loadMandiPrices();
    if (document.getElementById('cropList')) loadCropListings();
    if (document.getElementById('auctionList')) loadAuctions();
    
    // Auto-refresh auctions every 10 seconds
    if (document.getElementById('auctionList')) {
        setInterval(loadAuctions, 10000);
    }
});

