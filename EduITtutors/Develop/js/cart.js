document.addEventListener('DOMContentLoaded', function () {
    // Cart functionality
    const cart = {
        items: [],
        total: 0,

        init: function () {
            this.loadCart();
            this.setupEventListeners();
            this.updateCartUI();
        },

        loadCart: function () {
            // Load cart from localStorage with user-specific key
            const userId = this.getUserId();
            const cartKey = userId ? `eduittutors_cart_${userId}` : 'eduittutors_cart_guest';
            const savedCart = localStorage.getItem(cartKey);

            if (savedCart) {
                this.items = JSON.parse(savedCart);
                this.calculateTotal();
            }
        },

        saveCart: function () {
            const userId = this.getUserId();
            const cartKey = userId ? `eduittutors_cart_${userId}` : 'eduittutors_cart_guest';
            localStorage.setItem(cartKey, JSON.stringify(this.items));
        },

        getUserId: function () {
            // Get user ID from the global variable set by PHP
            return typeof window.userId !== 'undefined' ? window.userId : null;
        },

        addItem: function (course) {
            // Check if course already exists in cart
            const existingItem = this.items.find(item => item.id === course.id);

            if (!existingItem) {
                this.items.push({
                    id: course.id,
                    name: course.name,
                    teacher: course.teacher,
                    price: course.price,
                    image: course.image
                });

                this.calculateTotal();
                this.saveCart();
                this.updateCartUI();

                // Show success message
                this.showToast('Course added to cart successfully');
            } else {
                // Show message that course is already in cart
                this.showToast('This course is already in your cart');
            }
        },

        removeItem: function (courseId) {
            this.items = this.items.filter(item => item.id !== courseId);
            this.calculateTotal();
            this.saveCart();
            this.updateCartUI();
            this.showToast('Course removed from cart');
        },

        calculateTotal: function () {
            this.total = this.items.reduce((sum, item) => sum + item.price, 0);
        },

        updateCartUI: function () {
            const cartBody = document.querySelector('.cart-sidebar-body');
            const cartSubtotal = document.querySelector('.cart-subtotal');
            const cartCount = document.querySelectorAll('.cart-count');

            // Update cart count in navbar
            const itemCount = this.items.length;
            cartCount.forEach(el => {
                if (itemCount > 0) {
                    el.textContent = itemCount;
                    el.style.display = 'flex';
                } else {
                    el.style.display = 'none';
                }
            });

            // Update cart items
            if (this.items.length === 0) {
                cartBody.innerHTML = `
                    <div class="empty-cart-message">
                        <i class="fas fa-shopping-cart"></i>
                        <p>Your cart is empty</p>
                    </div>
                `;
                cartSubtotal.textContent = '$0.00';
                return;
            }

            let itemsHTML = '';
            this.items.forEach(item => {
                itemsHTML += `
                    <div class="cart-sidebar-item" data-course-id="${item.id}">
                        <img src="${item.image}" alt="${item.name}" class="cart-sidebar-item-img">
                        <div class="cart-sidebar-item-details">
                            <h6 class="cart-sidebar-item-title">${item.name}</h6>
                            <p class="cart-sidebar-item-teacher mb-1">${item.teacher}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="cart-sidebar-item-price">$${item.price.toFixed(2)}</span>
                                <button class="btn btn-sm btn-outline-danger remove-item">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });

            cartBody.innerHTML = itemsHTML;
            cartSubtotal.textContent = `$${this.total.toFixed(2)}`;
        },

        showToast: function (message) {
            const toast = document.createElement('div');
            toast.className = 'position-fixed bottom-0 end-0 p-3';
            toast.style.zIndex = '1100';
            toast.innerHTML = `
                <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-info text-white">
                        <strong class="me-auto">Notice</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            `;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        },

        setupEventListeners: function () {
            const self = this;

            // Toggle cart sidebar
            document.querySelectorAll('.shopping_cart i.fa-shopping-cart, .shopping_cart').forEach(el => {
                el.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.querySelector('.cart-sidebar').classList.add('open');
                    document.querySelector('.cart-sidebar-overlay').classList.add('open');
                    document.body.style.overflow = 'hidden';
                });
            });

            // Close cart sidebar
            document.querySelector('.close-cart-sidebar').addEventListener('click', closeCart);
            document.querySelector('.cart-sidebar-overlay').addEventListener('click', closeCart);

            function closeCart() {
                document.querySelector('.cart-sidebar').classList.remove('open');
                document.querySelector('.cart-sidebar-overlay').classList.remove('open');
                document.body.style.overflow = '';
            }

            // Handle cart item removal
            document.querySelector('.cart-sidebar-body').addEventListener('click', function (e) {
                if (e.target.closest('.remove-item')) {
                    const itemElement = e.target.closest('.cart-sidebar-item');
                    if (itemElement) {
                        self.removeItem(itemElement.dataset.courseId);
                    }
                }
            });

            // Add to cart buttons
            document.querySelectorAll('[data-course-id]').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();

                    const course = {
                        id: this.dataset.courseId,
                        name: this.dataset.courseName,
                        teacher: this.dataset.courseTeacher,
                        price: parseFloat(this.dataset.coursePrice),
                        image: this.dataset.courseImage
                    };

                    const userId = window.userId || null;

                    if (userId) {
                        fetch(`check_course_owned.php?course_id=${course.id}`)
                            .then(res => res.json())
                            .then(data => {
                                if (data.owned) {
                                    cart.showToast('You have already purchased this course');
                                } else {
                                    cart.addItem(course);
                                    openCartSidebar();
                                }
                            })
                            .catch(() => {
                                cart.showToast('Error checking purchase status. Try again.');
                            });
                    } else {
                        // Guest users - just add normally
                        cart.addItem(course);
                        openCartSidebar();
                    }
                });

                function openCartSidebar() {
                    if (!document.querySelector('.cart-sidebar').classList.contains('open')) {
                        document.querySelector('.cart-sidebar').classList.add('open');
                        document.querySelector('.cart-sidebar-overlay').classList.add('open');
                        document.body.style.overflow = 'hidden';
                    }
                }
            });

        }
    };

    // Initialize cart
    cart.init();
});