<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row default-layout-navbar">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo" href="../index.php" style="font-weight: 800; font-size:30px;">EduIT<span style="color: #14bdee;">tutors</span></a>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-stretch">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="fas fa-bars"></span>
        </button>
        <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item d-none d-lg-flex">
                <a class="nav-link" href="Courses.php">
                    <span class="btn btn-primary">+ Create New Course</span>
                </a>
            </li>
            <li class="nav-item dropdown d-none d-lg-flex">
                <div class="nav-link">
                    <span class="dropdown-toggle btn btn-outline-dark" id="languageDropdown"
                        data-toggle="dropdown">Language</span>
                    <div class="dropdown-menu navbar-dropdown" aria-labelledby="languageDropdown">
                        <a class="dropdown-item font-weight-medium" href="#">
                            English
                        </a>
                    </div>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#"
                    data-toggle="dropdown">
                    <i class="fas fa-bell mx-0"></i>
                    <?php
                    // Count new user registrations from the past week
                    $oneWeekAgo = date('Y-m-d', strtotime('-1 week'));
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE Register_Date >= ?");
                    $stmt->execute([$oneWeekAgo]);
                    $newUsersCount = $stmt->fetchColumn();
                    
                    // Count unread contact messages
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM contacts WHERE status = 'unread'");
                    $stmt->execute();
                    $unreadMessagesCount = $stmt->fetchColumn();
                    
                    $totalNotifications = $newUsersCount + $unreadMessagesCount;
                    ?>
                    <span class="count"><?= $totalNotifications > 0 ? $totalNotifications : '' ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                    aria-labelledby="notificationDropdown">
                    <a class="dropdown-item preview-item" href="usertable.php">
                        <div class="preview-thumbnail">
                            <div class="preview-icon bg-info">
                                <i class="fas fa-user-plus mx-0"></i>
                            </div>
                        </div>
                        <div class="preview-item-content">
                            <h6 class="preview-subject font-weight-medium">New user registration</h6>
                            <p class="font-weight-light small-text">
                                <?= $newUsersCount ?> new <?= $newUsersCount == 1 ? 'user' : 'users' ?>
                            </p>
                        </div>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item preview-item" href="contact.php">
                        <div class="preview-thumbnail">
                            <div class="preview-icon bg-info">
                                <i class="fas fa-envelope mx-0"></i>
                            </div>
                        </div>
                        <div class="preview-item-content">
                            <h6 class="preview-subject font-weight-medium">Unread Contact Messages</h6>
                            <p class="font-weight-light small-text">
                                <?= $unreadMessagesCount ?> unread <?= $unreadMessagesCount == 1 ? 'message' : 'messages' ?>
                            </p>
                        </div>
                    </a>
                </div>
            </li>
            
            <li class="nav-item nav-profile dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                    <?php if ($admin && !empty($admin['profile_img'])): ?>
                        <img src="uploads/User_Photo/<?= htmlspecialchars($admin['profile_img']) ?>" alt="profile" />
                    <?php else: ?>
                        <img src="images/faces/face5.jpg" alt="profile" />
                    <?php endif; ?>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                    <a class="dropdown-item" href="user_view.php?id=<?= $admin['User_ID'] ?? '' ?>">
                        <i class="fas fa-user text-primary"></i>
                        Profile
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="../logout.php">
                        <i class="fas fa-power-off text-primary"></i>
                        Logout
                    </a>
                </div>
            </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
            data-toggle="offcanvas">
            <span class="fas fa-bars"></span>
        </button>
    </div>
</nav>