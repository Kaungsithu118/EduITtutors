<header id="header" class="fixed-top">
    <!-- Top Header - Hidden on Mobile -->
    <nav class="header-top first-header bg-dark pt-2 pb-2 px-xl-5 d-none d-lg-block">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <ul class="info d-flex flex-wrap list-unstyled m-0">
                    <li class="location text-white text-capitalize d-flex align-items-center me-3 me-xl-4"
                        style="font-size: 15px;">
                        <i class="fa-solid fa-location-dot text-white me-1"></i>
                        No 34, Kannar Road, Yangon
                    </li>
                    <li class="phone text-white text-capitalize d-flex align-items-center" style="font-size: 15px;">
                        <i class="fa-solid fa-phone me-1"></i>
                        +959 123 456 789
                    </li>
                </ul>
                <ul class="social-links d-flex flex-wrap list-unstyled m-0">
                    <li class="social"><a href="#"><i class="fa-brands fa-facebook text-white"></i></a></li>
                    <li class="social ms-3"><a href="#"><i class="fa-brands fa-x-twitter text-white"></i></a></li>
                    <li class="social ms-3"><a href="#"><i class="fa-brands fa-linkedin text-white"></i></a></li>
                    <li class="social ms-3"><a href="#"><i class="fa-brands fa-instagram text-white"></i></a></li>
                    <li class="social ms-3"><a href="#"><i class="fa-brands fa-youtube text-white"></i></a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-2 main_nav header_container">
        <div class="container my-3">
            <!-- Logo -->
            <div class="logo_container">
                <a class="navbar-brand me-auto" href="#">
                    <div class="logo_text">EduIT<span>tutors</span></div>
                </a>
            </div>

            <!-- Mobile Icons -->
            <div class="d-flex d-lg-none align-items-center justify-content-center" style="height: 50px; margin-left:-60px;">

                <?php if (($_SESSION['role'] ?? '') !== 'Admin'): ?>
                    <div class="shopping_cart me-5 mt-2 position-relative">
                        <i class="fa fa-shopping-cart"></i>
                        <span class="cart-count position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                            <?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>
                        </span>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 30px; transition: background-color 0.2s ease;">
                            <img class="rounded-circle me-2"
                                src="<?php echo !empty($user['profile_img']) ? 'admin/uploads/User_Photo/' . htmlspecialchars($user['profile_img']) : '../Develop/admin/uploads/user_default_photo.png'; ?>"
                                alt="Profile" width="40" height="40"
                                style="object-fit: cover; border-radius: 50%; border: 2px solid #ddd; box-shadow: 0 0 4px rgba(0, 0, 0, 0.1); transition: transform 0.2s ease;">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profilesetting.php">
                                    <i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="store.php">
                                    <i class="fa-solid fa-book me-2"></i>My Courses</a></li>
                            <li><a class="dropdown-item" href="logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>

                            <?php if (($_SESSION['role'] ?? '') === 'Admin'): ?>
                                <li><a class="dropdown-item" href="../Develop/admin/home.php">
                                        <i class="fas fa-cog me-2"></i>Admin Panel</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="login.php">
                        <div class="mt-1" style="color: black; font-size: 20px;"><i class="fa-solid fa-user"></i></div>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
                aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Content -->
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">Categories</a>
                        <ul class="dropdown-menu" aria-labelledby="categoriesDropdown">
                            <li><a class="dropdown-item" href="about.php">About</a></li>
                            <li><a class="dropdown-item" href="course.php">Courses</a></li>
                            <li><a class="dropdown-item" href="blog.php">Blog</a></li>
                            <li><a class="dropdown-item" href="departments.php">Department</a></li>
                            <li><a class="dropdown-item" href="webinar.php">Webinar</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                </ul>

                <!-- Replace the existing search section with this -->
                <div class="d-flex my-2 my-lg-0 ms-lg-3 position-relative">
                    <form id="search-form" class="input-group">
                        <input id="course-search" class="form-control" type="search" placeholder="Search courses..." aria-label="Search" autocomplete="off">
                        <button class="btn search-btn me-lg-3" type="submit"><i class="fas fa-search"></i></button>
                    </form>
                    <div id="search-results" class="dropdown-menu w-100" style="display: none;"></div>
                </div>


                <!-- Profile Section -->
                <div class="d-none d-lg-flex align-items-center justify-content-center" style="height: 50px;">
                    <?php if (($_SESSION['role'] ?? '') !== 'Admin'): ?>
                        <div class="shopping_cart me-5 mt-2 position-relative">
                            <i class="fa fa-shopping-cart"></i>
                            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                                <span class="cart-count position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                    <?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 30px; transition: background-color 0.2s ease;">
                                <img class="rounded-circle me-2"
                                    src="<?php echo !empty($user['profile_img']) ? 'admin/uploads/User_Photo/' . htmlspecialchars($user['profile_img']) : '../Develop/admin/uploads/user_default_photo.png'; ?>"
                                    alt="Profile" width="40" height="40"
                                    style="object-fit: cover; border-radius: 50%; border: 2px solid #ddd; box-shadow: 0 0 4px rgba(0, 0, 0, 0.1); transition: transform 0.2s ease;">
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="profilesetting.php">
                                        <i class="fas fa-user me-2"></i>Profile</a></li>
                                <?php if (($_SESSION['role'] ?? '') !== 'Admin'): ?>
                                    <li><a class="dropdown-item" href="store.php">
                                            <i class="fa-solid fa-book me-2"></i>My Courses</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="logout.php">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>

                                <?php if (($_SESSION['role'] ?? '') === 'Admin'): ?>
                                    <li><a class="dropdown-item" href="../Develop/admin/home.php">
                                            <i class="fas fa-cog me-2"></i>Admin Panel</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="login.php">
                            <div class="mt-1" style="color: black; font-size: 20px;"><i class="fa-solid fa-user"></i></div>
                        </a>
                    <?php endif; ?>
                    <!-- Add this near your navigation menu -->
                    <div class="dropdown ms-4" id="language">
                        <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-globe"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                            <li><a href="?lang=en" class="dropdown-item font-weight-medium">English</a></li>
                        </ul>
                    </div>

                    <!-- Hidden Google Translate Element -->
                    <div id="google_translate_element" style="display:none;"></div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Cart Sidebar -->
    <div class="cart-sidebar-overlay"></div>
    <div class="cart-sidebar">
        <div class="cart-sidebar-header">
            <h5 class="mb-0 fs-5" style="color: white;">Your Shopping Cart</h5>
            <button class="close-cart-sidebar btn btn-sm btn-light">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="cart-sidebar-body">
            <!-- Cart items will be loaded here dynamically -->
            <div class="empty-cart-message">
                <i class="fas fa-shopping-cart"></i>
                <p>Your cart is empty</p>
            </div>
        </div>
        <div class="cart-sidebar-footer text-black">
            <div class="d-flex justify-content-between mb-3">
                <strong>Subtotal:</strong>
                <strong class="cart-subtotal">$0.00</strong>
            </div>
            <a href="cart.php" class="btn btn-primary w-100 py-2">
                <i class="fas fa-credit-card me-2"></i> Proceed to Checkout
            </a>
        </div>
    </div>
</header>