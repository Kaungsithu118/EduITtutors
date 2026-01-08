<?php
include("profilecalling.php");

// Get cart items from localStorage (via AJAX) or from session
$cart_items = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_data'])) {
    $cart_items = json_decode($_POST['cart_data'], true);
    $_SESSION['cart'] = $cart_items; // Store in session for invoice generation
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - EduITtutors</title>
    <link rel="icon" type="image/png" href="photo/logo/EduITtutors_Colorver_Logo.png" style="width: 250px; height: auto;">

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/cart.css">
    <link rel="stylesheet" href="css/cartslidebar.css">
    <link rel="stylesheet" href="css/search.css">
    <link rel="stylesheet" href="css/chatbot.css">


    <!-- Font Awesome for play icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />


    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <link rel="stylesheet" type="text/css"
        href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" />


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,700|Roboto:300,400,500,700,900"
        rel="stylesheet">

    <!-- Owl Carousel stylesheet -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

    <style>

    </style>
</head>

<body>

    <?php
    include("header.php");
    ?>


    <div class="home">
        <div class="breadcrumbs_container">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="breadcrumbs">
                            <ul>
                                <li><a href="index.php">Home</a></li>
                                <li><a href="course.php">Courses</a></li>
                                <li style="color: #384158; font-size: 16px; font-weight: 700;">Cart</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="cart-container">
        <div class="container">
            <h1 class="cart-header mb-5">Your Course Cart</h1>

            <div class="cart-table-container mb-4">
                <div class="table-responsive">
                    <table class="table cart-table">
                        <thead>
                            <tr>
                                <th scope="col">Course</th>
                                <th scope="col">Format</th>
                                <th scope="col">Price</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody id="cart-items-body">
                            <!-- Cart items will be loaded here dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-8">
                    <div class="card payment-card mb-4">
                        <div class="card-body m-3">
                            <h5 class="card-title mb-4">Payment Method</h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="payment-method active">
                                        <i class="fab fa-cc-mastercard payment-icon"></i>
                                        <div>
                                            <h6 class="mb-0">Credit Card</h6>
                                            <small class="text-muted">Visa, Mastercard, etc.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="payment-method">
                                        <i class="fab fa-cc-paypal payment-icon"></i>
                                        <div>
                                            <h6 class="mb-0">PayPal</h6>
                                            <small class="text-muted">Secure online payments</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <form class="payment-form mt-4">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="cardName" class="form-label">Name on Card</label>
                                        <input type="text" class="form-control" id="cardName" placeholder="John Doe">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="cardNumber" class="form-label">Card Number</label>
                                        <input type="text" class="form-control" id="cardNumber" placeholder="1234 5678 9012 3456">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="expiryDate" class="form-label">Expiry Date</label>
                                        <input type="text" class="form-control" id="expiryDate" placeholder="MM/YY">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="cvv" class="form-label">CVV</label>
                                        <input type="text" class="form-control" id="cvv" placeholder="123">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="zipCode" class="form-label">ZIP Code</label>
                                        <input type="text" class="form-control" id="zipCode" placeholder="10001">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card payment-card">
                        <div class="card-body m-3">
                            <h5 class="card-title mb-4">Order Summary</h5>

                            <div class="summary-item">
                                <span>Subtotal</span>
                                <span id="cart-subtotal">$0.00</span>
                            </div>
                            <div class="summary-item">
                                <span>Tax (7%)</span>
                                <span id="cart-tax">$0.00</span>
                            </div>

                            <hr>

                            <div class="summary-item summary-total">
                                <span>Total</span>
                                <span id="cart-total">$0.00</span>
                            </div>

                            <button id="checkout-btn" class="checkout-btn btn btn-primary mt-3" disabled>
                                <span>Proceed to Invoice</span>
                                <i class="fas fa-arrow-right ms-2"></i>
                            </button>

                            <div class="mt-3 text-center">
                                <small class="text-muted">By placing your order, you agree to our <a href="#">Terms of Service</a></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Loading Overlay -->
    <div id="loading-overlay">
        <div class="spinner"></div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" style="display: none;">
        <div class="toast-header bg-info text-white">
            <strong class="me-auto">Notice</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>
    <script>
        // Set the user ID from PHP session
        window.userId = <?= isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null' ?>;
    </script>

    <!-- Search functionality (your script block) -->
    <script src="js/search.js"></script>
    <!-- Include jQuery and Owl Carousel JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get user ID from PHP
            const userId = <?= isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null' ?>;

            // Load cart from localStorage with consistent key
            const cartKey = userId ? `eduittutors_cart_${userId}` : 'eduittutors_cart_guest';
            const cart = JSON.parse(localStorage.getItem(cartKey)) || [];

            // Initialize toast
            const toastEl = document.getElementById('toast');
            const toast = new bootstrap.Toast(toastEl);

            // Function to show toast message
            function showToast(message) {
                const toastBody = toastEl.querySelector('.toast-body');
                toastBody.textContent = message;
                toast.show();

                // Auto-hide after 3 seconds
                setTimeout(() => {
                    toast.hide();
                }, 3000);
            }

            // Render the cart
            renderCart(cart);

            function renderCart(cartItems) {
                const cartBody = document.getElementById('cart-items-body');
                const subtotalEl = document.getElementById('cart-subtotal');
                const taxEl = document.getElementById('cart-tax');
                const totalEl = document.getElementById('cart-total');
                const checkoutBtn = document.getElementById('checkout-btn');

                if (cartItems.length === 0) {
                    cartBody.innerHTML = `
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                <p class="mb-3">Your cart is empty</p>
                                <a href="course.php" class="btn btn-primary">Browse Courses</a>
                            </td>
                        </tr>
                    `;
                    checkoutBtn.disabled = true;
                    return;
                }

                let html = '';
                let subtotal = 0;

                cartItems.forEach(item => {
                    subtotal += item.price;
                    html += `
                        <tr class="cart-item">
                            <td class="item-cell">
                                <div class="d-flex align-items-center">
                                    <img src="${item.image}" class="item-img me-3" alt="${item.name}">
                                    <div>
                                        <p class="item-title mb-1">${item.name}</p>
                                        <p class="item-author mb-0">${item.teacher}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary">Online</span>
                            </td>
                            <td>
                                <span class="fw-bold">$${item.price.toFixed(2)}</span>
                            </td>
                            <td>
                                <i class="fas fa-trash remove-item" data-course-id="${item.id}"></i>
                            </td>
                        </tr>
                    `;
                });

                cartBody.innerHTML = html;

                // Calculate and update totals
                const tax = subtotal * 0.07; // 7% tax
                const total = subtotal + tax;

                subtotalEl.textContent = `$${subtotal.toFixed(2)}`;
                taxEl.textContent = `$${tax.toFixed(2)}`;
                totalEl.textContent = `$${total.toFixed(2)}`;

                // Enable checkout button
                checkoutBtn.disabled = false;

                // Add event listeners for remove buttons
                document.querySelectorAll('.remove-item').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const courseId = this.dataset.courseId;
                        removeFromCart(courseId);
                    });
                });
            }

            function removeFromCart(courseId) {
                const userId = <?= isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null' ?>;
                const cartKey = userId ? `eduittutors_cart_${userId}` : 'eduittutors_cart_guest';
                let cart = JSON.parse(localStorage.getItem(cartKey)) || [];

                // Remove item from cart
                cart = cart.filter(item => item.id !== courseId);

                // Save updated cart
                localStorage.setItem(cartKey, JSON.stringify(cart));

                // Update cart count in navbar
                updateCartCount(cart.length);

                // Re-render cart
                renderCart(cart);

                // Show notification
                showToast('Course removed from cart');
            }

            function updateCartCount(count) {
                document.querySelectorAll('.cart-count').forEach(el => {
                    el.textContent = count;
                    el.style.display = count > 0 ? 'flex' : 'none';
                });
            }

            // Payment method selection
            document.querySelectorAll('.payment-method').forEach(method => {
                method.addEventListener('click', function() {
                    document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // Enhanced checkout handler
            document.getElementById('checkout-btn').addEventListener('click', function() {
                const userId = <?= isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null' ?>;
                const cartKey = userId ? `eduittutors_cart_${userId}` : 'eduittutors_cart_guest';
                const cart = JSON.parse(localStorage.getItem(cartKey)) || [];

                if (cart.length === 0) {
                    showToast('Your cart is empty');
                    return;
                }

                // Validate payment form
                const cardName = document.getElementById('cardName').value.trim();
                const cardNumber = document.getElementById('cardNumber').value.trim();
                const expiryDate = document.getElementById('expiryDate').value.trim();
                const cvv = document.getElementById('cvv').value.trim();
                const zipCode = document.getElementById('zipCode').value.trim();

                if (!cardName || !cardNumber || !expiryDate || !cvv || !zipCode) {
                    showToast('Please fill in all payment details');
                    return;
                }

                // Show loading overlay
                document.getElementById('loading-overlay').style.display = 'flex';
                showToast('Processing your payment...');

                // First save cart data to session
                fetch('save_cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            cart_data: cart
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Clear the cart from localStorage immediately
                            localStorage.removeItem(cartKey);

                            // Update cart count in UI to show 0
                            updateCartCount(0);

                            // Now submit the full form to process_order.php
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = 'process_order.php';

                            // Add payment details
                            const addField = (name, value) => {
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = name;
                                input.value = value;
                                form.appendChild(input);
                            };

                            addField('payment_method', 'Credit Card');
                            addField('card_name', cardName);
                            addField('card_number', cardNumber);
                            addField('card_expiry', expiryDate);
                            addField('card_cvv', cvv);
                            addField('card_zip', zipCode);
                            addField('cart_data', JSON.stringify(cart));

                            document.body.appendChild(form);
                            form.submit();
                        } else {
                            throw new Error('Failed to save cart');
                        }
                    })
                    .catch(error => {
                        document.getElementById('loading-overlay').style.display = 'none';
                        showToast('Error processing payment. Please try again.');
                        console.error('Error:', error);
                    });
            });

            // Initialize cart count on page load
            updateCartCount(cart.length);
        });
    </script>
</body>

</html>