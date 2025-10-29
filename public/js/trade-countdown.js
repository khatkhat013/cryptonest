// Price simulation and countdown timer
let countdownTimer;
let currentPrice;
let orderId;
let purchasePrice;
let direction;

function startCountdown(seconds, price, order, dir) {
    let timeLeft = seconds;
    currentPrice = purchasePrice = parseFloat(price);
    orderId = order;
    direction = dir;
    
    countdownTimer = setInterval(() => {
        timeLeft--;
        
        // Update countdown display
        document.getElementById('countdown').textContent = timeLeft;
        
        // Calculate simulated price
        const progress = ((seconds - timeLeft) / seconds) * 100;
        updatePrice(progress);
        
        if (timeLeft <= 0) {
            clearInterval(countdownTimer);
            handleTradeComplete();
        }
    }, 1000);
}

function updatePrice(progress) {
    fetch('/api/trade/simulate-price', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            purchase_price: purchasePrice,
            direction: direction,
            progress: progress
        })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('current-price').textContent = data.price.toFixed(2);
        currentPrice = data.price;
    });
}

function handleTradeComplete() {
    fetch(`/api/trade/${orderId}/complete`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            final_price: currentPrice
        })
    })
    .then(response => response.json())
    .then(data => {
        // Show results
        document.getElementById('result-status').textContent = data.result.toUpperCase();
        document.getElementById('profit-amount').textContent = data.profit_amount;
        document.getElementById('results-section').style.display = 'block';
        document.getElementById('countdown-section').style.display = 'none';
    });
}

// Handle Done button click
document.getElementById('done-button').addEventListener('click', function() {
    // Add loading state to button
    const button = this;
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Redirecting...';
    
    // Small delay for better UX then redirect to home
    setTimeout(() => {
        window.location.href = '/';
    }, 500);
});