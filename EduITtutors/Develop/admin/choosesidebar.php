<!-- partial:partials/_settings-panel.html -->
<div class="theme-setting-wrapper">
    <div id="settings-trigger"><i class="fas fa-fill-drip"></i></div>
    <div id="theme-settings" class="settings-panel">
        <i class="settings-close fa fa-times"></i>
        <p class="settings-heading">SIDEBAR SKINS</p>
        <div class="sidebar-bg-options selected" id="sidebar-light-theme">
            <div class="img-ss rounded-circle bg-light border mr-3"></div>Light
        </div>
        <div class="sidebar-bg-options" id="sidebar-dark-theme">
            <div class="img-ss rounded-circle bg-dark border mr-3"></div>Dark
        </div>
        <p class="settings-heading mt-2">HEADER SKINS</p>
        <div class="color-tiles mx-0 px-4">
            <div class="tiles primary"></div>
            <div class="tiles success"></div>
            <div class="tiles warning"></div>
            <div class="tiles danger"></div>
            <div class="tiles info"></div>
            <div class="tiles dark"></div>
            <div class="tiles default"></div>
        </div>
    </div>
</div>
<!-- partial -->
<!-- partial:partials/_sidebar.html -->
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <div class="nav-link">
                <div class="profile-image">
                    <?php if ($admin && !empty($admin['profile_img'])): ?>
                        <img src="uploads/User_Photo/<?= htmlspecialchars($admin['profile_img']) ?>" alt="image" />
                    <?php else: ?>
                        <img src="images/faces/face5.jpg" alt="image" />
                    <?php endif; ?>
                </div>
                <div class="profile-name">
                    <p class="name">
                        Welcome <?= $admin ? htmlspecialchars($admin['Name']) : 'Admin' ?>
                    </p>
                    <p class="designation">
                        <?= $admin ? htmlspecialchars($admin['Role']) : 'Super Admin' ?>
                    </p>
                </div>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="home.php">
                <i class="fa fa-home menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item d-none d-lg-block">
            <a class="nav-link" data-toggle="collapse" href="#sidebar-layouts" aria-expanded="false" aria-controls="sidebar-layouts">
                <i class="fa fa-puzzle-piece menu-icon"></i>
                <span class="menu-title">Users</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="sidebar-layouts">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="user_form.php">Users Insert From</a></li>
                    <li class="nav-item"> <a class="nav-link" href="usertable.php">User Table</a></li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
                <i class="far fa-compass menu-icon"></i>
                <span class="menu-title">Educations</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-basic">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="teacher.php">Teachers</a></li>
                    <li class="nav-item"> <a class="nav-link" href="teachercards.php">Teachers Information</a></li>
                    <li class="nav-item"> <a class="nav-link" href="department.php">Departments</a></li>
                    <li class="nav-item"> <a class="nav-link" href="departmentinfo.php">Department Table</a></li>

                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
                <i class="fas fa-window-restore menu-icon"></i>
                <span class="menu-title">Course</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="auth">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="Courses.php">Courses</a></li>
                    <li class="nav-item"> <a class="nav-link" href="coursebox.php">Courses display</a></li>
                    <li class="nav-item"> <a class="nav-link" href="course_content_lessons.php">Course Lessons</a></li>
                    <li class="nav-item"> <a class="nav-link" href="course_content_box.php">Course Lessons Display</a></li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#general-pages" aria-expanded="false" aria-controls="general-pages">
                <i class="fas fa-file menu-icon"></i>
                <span class="menu-title">General</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="general-pages">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="blog_form.php">Blog</a></li>
                </ul>
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="blogs_table.php">Blogs Display</a></li>
                </ul>`
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#apps" aria-expanded="false" aria-controls="apps">
                <i class="fas fa-address-book menu-icon"></i>
                <span class="menu-title">Education</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="apps">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="contact.php">Contact</a></li>
                </ul>
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="webinar_form.php">Webinar</a></li>
                </ul>
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="webinar_display.php">Webinars Info</a></li>
                </ul>
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="webinar_register.php">Webinars Register</a></li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#ui-advanced" aria-expanded="false" aria-controls="ui-advanced">
                <i class="fas fa-clipboard-list menu-icon"></i>
                <span class="menu-title">Order</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-advanced">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="order.php">Order Table</a></li>
                    <li class="nav-item"> <a class="nav-link" href="course_order_&_deadline.php">Course Status & Deadlines</a></li>    
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#form-elements" aria-expanded="false" aria-controls="form-elements">
                <i class="fab fa-wpforms menu-icon"></i>
                <span class="menu-title">Planner</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="form-elements">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="event.php">Event</a></li>
                </ul>
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="user_progress.php">Progress and Certificate</a></li>
                </ul>
            </div>
        </li>
    </ul>
</nav>