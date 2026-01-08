<?php
session_start();
require_once '../Develop/admin/connect.php'; // Adjust path as needed based on your file structure
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
try {
    // Get user data from database using PDO
    $user_id = $_SESSION['user_id'];
    // Fetch user data from database
    $stmt = $pdo->prepare("SELECT * FROM user WHERE User_ID = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        die("User not found");
    }
    // Prepare values for the form
    $dob = isset($user['date_of_birth']) ? htmlspecialchars($user['date_of_birth']) : '';
    $register_date = date('F j, Y', strtotime($user['Register_Date']));
    $profile_img = (!empty($user['profile_img']) && file_exists(__DIR__ . '/admin/uploads/User_Photo/' . $user['profile_img']))
        ? 'admin/uploads/User_Photo/' . $user['profile_img']
        : '../Develop/admin/uploads/user_default_photo.png';

    // Process areas of interest for checkboxes
    $areas = [];
    if (!empty($user['areas_of_interest'])) {
        $areas = explode(',', $user['areas_of_interest']);
    }

    $softwareChecked = in_array('Software', $areas) ? 'checked' : '';
    $networkChecked = in_array('Networking', $areas) ? 'checked' : '';
    $dataScienceChecked = in_array('Data Science', $areas) ? 'checked' : '';
    $businessitChecked = in_array('Business IT', $areas) ? 'checked' : '';
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | EduITtutors</title>
    <link rel="icon" type="image/png" href="photo/logo/EduITtutors_Colorver_Logo.png" style="width: 250px; height: auto;">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/chatbot.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/cartslidebar.css">
    <link rel="stylesheet" href="css/search.css">
    <!-- Custom CSS -->
    <link href="css/profilesetting.css" rel="stylesheet">

    <style>
        /* Password Strength Meter Styles */
        .password-strength-container {
            margin-top: 8px;
        }

        .password-strength-meter {
            height: 5px;
            background: #e0e0e0;
            border-radius: 3px;
            margin-bottom: 3px;
            overflow: hidden;
        }

        .password-strength-meter .progress-bar {
            height: 100%;
            width: 0;
            transition: width 0.3s ease, background-color 0.3s ease;
        }

        .password-strength-text {
            font-size: 12px;
            color: #6c757d;
        }

    </style>
</head>

<body>

    <?php
    include("header.php");
    ?>






    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <main class="col-md-12 ms-sm-auto col-lg-12 px-5 px-md-4 justify-content-center align-items-center mt-3 " style="padding-top: 160px; padding-bottom: 50px;">
                <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-5 border-bottom">
                    <h1 class="h2">My Profile</h1>
                </div>

                <div class="row">
                    <div class="col-lg-4">
                        <!-- Profile Card -->
                        <div class="card glass-card mb-4">
                            <div class="profile-header p-4 text-center">
                                <div class="d-flex justify-content-center">
                                    <img src="<?php echo htmlspecialchars($profile_img); ?>" alt="Profile Picture"
                                        class="profile-picture rounded-circle mb-3 shadow" id="profileImageDisplay"
                                        style="width:120px;height:120px;object-fit:cover;">
                                </div>
                                <h4 class="mb-0 fw-semibold text-dark"><?php echo htmlspecialchars($user['Name']); ?></h4>
                                <p class="text-dark small"><?php echo htmlspecialchars($user['description'] ?? 'No description'); ?></p>
                                <div class="d-flex justify-content-center gap-2 mb-2 mt-3">
                                    <form id="profileUploadForm" enctype="multipart/form-data">
                                        <input type="file" id="profileImageUpload" name="profile_image" accept="image/*" hidden>
                                        <button type="button" class="btn btn-upload btn-sm" onclick="document.getElementById('profileImageUpload').click()">
                                            <i class="fas fa-upload me-1"></i> Upload
                                        </button>
                                    </form>
                                    <button class="btn btn-remove btn-sm" id="removeProfileImageBtn"
                                        <?php echo empty($user['profile_img']) ? 'disabled' : ''; ?>>
                                        <i class="fas fa-trash-alt me-1"></i> Remove
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Account Status</label>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-success me-2 px-3 py-1">Active</span>
                                        <small class="text-muted">Verified <i class="fas fa-check-circle text-success"></i></small>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Member Since</label>
                                    <p class="text-muted mb-0"><?php echo $register_date; ?></p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Email</label>
                                    <p class="text-muted mb-0"><?php echo htmlspecialchars($user['Email']); ?></p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Phone</label>
                                    <p class="text-muted mb-0"><?php echo htmlspecialchars($user['phone'] ?? 'Not provided'); ?></p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Institution</label>
                                    <p class="text-muted mb-0"><?php echo htmlspecialchars($user['institution'] ?? 'Not provided'); ?></p>
                                </div>
                            </div>
                        </div>

                        <?php if (($_SESSION['role'] ?? '') !== 'Admin'): ?>
                            <!-- Learning Progress Card -->
                            <div class="card glass-card mb-4">
                                <div class="card-header bg-white border-0">
                                    <h5 class="mb-0 fw-semibold">Learning Progress</h5>
                                </div>
                                <div class="card-body">
                                    <?php
                                    // Calculate overall progress
                                    $stmt = $pdo->prepare("
                                        SELECT 
                                            COUNT(DISTINCT oi.Course_ID) as total_courses,
                                            COUNT(DISTINCT up.Course_ID) as completed_courses
                                        FROM order_items oi
                                        LEFT JOIN user_progress up ON oi.Course_ID = up.Course_ID AND up.User_ID = :user_id
                                        WHERE oi.Order_ID IN (
                                            SELECT Order_ID FROM orders WHERE User_ID = :user_id
                                        ) AND oi.Access_Status = 'Active'
                                    ");
                                    $stmt->execute([':user_id' => $user_id]);
                                    $progress_data = $stmt->fetch(PDO::FETCH_ASSOC);

                                    $total_courses = $progress_data['total_courses'] ?? 0;
                                    $completed_courses = $progress_data['completed_courses'] ?? 0;
                                    $overall_progress = $total_courses > 0 ? round(($completed_courses / $total_courses) * 100) : 0;
                                    ?>

                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Overall Completion</label>
                                        <div class="d-flex align-items-center">
                                            <div class="progress w-100 me-3" style="height: 8px; border-radius: 6px;">
                                                <div class="progress-bar bg-gradient-success" role="progressbar"
                                                    style="width: <?= $overall_progress ?>%"
                                                    aria-valuenow="<?= $overall_progress ?>"
                                                    aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                            </div>
                                            <small class="fw-medium text-dark"><?= $overall_progress ?>%</small>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Course Statistics</label>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                Total Enrolled Courses
                                                <span class="badge bg-primary rounded-pill px-3"><?= $total_courses ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                Completed Courses
                                                <span class="badge bg-success rounded-pill px-3"><?= $completed_courses ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                In Progress
                                                <span class="badge bg-warning rounded-pill px-3"><?= $total_courses - $completed_courses ?></span>
                                            </li>
                                        </ul>
                                    </div>

                                    <a href="course.php" class="btn btn-gradient btn-sm w-100">
                                        <i class="fas fa-plus me-1"></i> Enroll in New Course
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>


                    <div class="col-lg-8">
                        <!-- Settings Tabs -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <ul class="nav nav-pills card-header-pills">
                                    <li class="nav-item mx-3">
                                        <a class="nav-link active" href="#personal" data-bs-toggle="tab">Personal
                                            Info</a>
                                    </li>
                                    <li class="nav-item mx-3">
                                        <a class="nav-link" href="#account" data-bs-toggle="tab">Account</a>
                                    </li>
                                    <li class="nav-item mx-3">
                                        <a class="nav-link" href="#education" data-bs-toggle="tab">Education</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="personal">

                                        <!-- HTML Form for Update -->
                                        <form class="forms-sample" method="post" action="update_profile.php" enctype="multipart/form-data">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="firstName" class="form-label">Name</label>
                                                    <input type="text" class="form-control" id="Name" name="name" value="<?php echo htmlspecialchars($user['Name']); ?>">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="lastName" class="form-label">Description</label>
                                                    <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($user['description'] ?? ''); ?>">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="bio" class="form-label">Bio</label>
                                                <textarea class="form-control" id="bio" name="bio" rows="3"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="phone" class="form-label">Phone</label>
                                                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="birthdate" class="form-label">Date of Birth</label>
                                                <input type="date" class="form-control" id="birthdate" name="birthdate" value="<?php echo $dob; ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label for="address" class="form-label">Address</label>
                                                <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-4">
                                                    <label for="country" class="form-label">Country</label>
                                                    <select id="country" class="form-select" name="country">
                                                        <option value="">Select Country</option>
                                                        <?php if (!empty($user['country'])): ?>
                                                            <option value="<?php echo htmlspecialchars($user['country']); ?>" selected><?php echo htmlspecialchars($user['country']); ?></option>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="city" class="form-label">City</label>
                                                    <select id="city" class="form-select" name="city" <?php echo empty($user['country']) ? 'disabled' : ''; ?>>
                                                        <?php if (!empty($user['city'])): ?>
                                                            <option value="<?php echo htmlspecialchars($user['city']); ?>" selected><?php echo htmlspecialchars($user['city']); ?></option>
                                                        <?php else: ?>
                                                            <option value="" selected disabled>Select a city</option>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-primary" name="update_personal">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Account Tab -->
                                    <div class="tab-pane fade" id="account">
                                        <form>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Account Type</label>
                                                <div class="form">
                                                    <p class="fs-6 fw-semibold"><?php
                                                                                $role = htmlspecialchars($user['Role'] ?? 'Student');
                                                                                echo ucfirst(strtolower($role));
                                                                                ?></p>
                                                </div>
                                                <div class="mb-4 mt-4">
                                                    <h6 class="fw-bold">Password</h6>
                                                    <p class="text-muted small">Last changed at <?php echo htmlspecialchars($user['last_password_change']); ?></p>
                                                    <button type="button" class="btn btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                                        Change Password
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>


                                    <!-- Education Tab -->
                                    <div class="tab-pane fade" id="education">
                                        <form id="educationForm">
                                            <input type="hidden" name="update_education" value="1">

                                            <div id="educationMessage" class="mb-3"></div>

                                            <div class="mb-3">
                                                <label for="institution" class="form-label">Institution *</label>
                                                <input type="text" class="form-control" id="institution" name="institution"
                                                    value="<?php echo htmlspecialchars($user['institution'] ?? ''); ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="degree" class="form-label">Degree Program *</label>
                                                <input type="text" class="form-control" id="degree" name="degree_program"
                                                    value="<?php echo htmlspecialchars($user['degree_program'] ?? ''); ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="areas_of_interest" class="form-label">Areas of Interest</label>
                                                <input type="text" class="form-control" id="areas_of_interest" name="areas_of_interest"
                                                    value="<?php echo htmlspecialchars($user['areas_of_interest'] ?? ''); ?>"
                                                    placeholder="Software, Networking, Data Science, Business IT">
                                                <small class="form-text text-muted">Separate multiple interests with commas</small>
                                            </div>

                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-primary" id="saveEducationBtn">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Current Courses Card -->
                        <?php if (($_SESSION['role'] ?? '') !== 'Admin'): ?>
                            <div class="coursecard mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">My Current Courses</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Course</th>
                                                    <th>Instructor</th>
                                                    <th>Progress</th>
                                                    <th>Course Period</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Get user's enrolled courses
                                                $stmt = $pdo->prepare("
                                                    SELECT oi.*, c.Course_Name, t.Teacher_Name 
                                                    FROM order_items oi
                                                    JOIN courses c ON oi.Course_ID = c.Course_ID
                                                    JOIN teachers t ON c.Teacher_ID = t.Teacher_ID
                                                    WHERE oi.Order_ID IN (
                                                        SELECT Order_ID FROM orders WHERE User_ID = :user_id
                                                    ) AND oi.Access_Status = 'Active'
                                                ");
                                                $stmt->execute([':user_id' => $user_id]);
                                                $enrolled_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                if (empty($enrolled_courses)) {
                                                    echo '<tr><td colspan="5" class="text-center">You are not enrolled in any courses yet.</td></tr>';
                                                } else {
                                                    foreach ($enrolled_courses as $course) {
                                                        // Get total content count for this course
                                                        $stmt = $pdo->prepare("
                                                            SELECT COUNT(*) as total_content 
                                                            FROM course_content 
                                                            WHERE Course_ID = :course_id
                                                        ");
                                                        $stmt->execute([':course_id' => $course['Course_ID']]);
                                                        $content_count = $stmt->fetch(PDO::FETCH_ASSOC)['total_content'];

                                                        // Get completed content count for this course
                                                        $stmt = $pdo->prepare("
                                                            SELECT COUNT(*) as completed_content 
                                                            FROM user_progress 
                                                            WHERE User_ID = :user_id AND Course_ID = :course_id
                                                        ");
                                                        $stmt->execute([
                                                            ':user_id' => $user_id,
                                                            ':course_id' => $course['Course_ID']
                                                        ]);
                                                        $completed_count = $stmt->fetch(PDO::FETCH_ASSOC)['completed_content'];

                                                        // Calculate progress percentage
                                                        $progress = $content_count > 0 ? round(($completed_count / $content_count) * 100) : 0;

                                                        // Determine progress bar color based on percentage
                                                        $progress_class = '';
                                                        if ($progress >= 75) {
                                                            $progress_class = 'bg-success';
                                                        } elseif ($progress >= 25) {
                                                            $progress_class = 'bg-primary';
                                                        } else {
                                                            $progress_class = 'bg-warning';
                                                        }

                                                        // Format course period
                                                        $start_date = date('M j, Y', strtotime($course['Start_Date']));
                                                        $end_date = date('M j, Y', strtotime($course['End_Date']));
                                                        $course_period = "$start_date - $end_date";
                                                ?>
                                                        <tr>
                                                            <td>
                                                                <strong><?= htmlspecialchars($course['Course_Name']) ?></strong><br>
                                                                <small class="text-muted"><?= htmlspecialchars($course['Course_ID']) ?></small>
                                                            </td>
                                                            <td><?= htmlspecialchars($course['Teacher_Name']) ?></td>
                                                            <td>
                                                                <div class="progress course-progress">
                                                                    <div class="progress-bar <?= $progress_class ?>"
                                                                        style="width: <?= $progress ?>%"></div>
                                                                </div>
                                                                <small><?= $progress ?>% Complete</small>
                                                            </td>
                                                            <td><?= $course_period ?></td>
                                                            <td>
                                                                <a href="course_content.php?id=<?= $course['Course_ID'] ?>"
                                                                    class="btn btn-sm btn-outline-primary">
                                                                    <?= $progress > 0 ? 'Continue' : 'Start' ?>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <a href="course.php" class="btn btn-primary">Browse More Courses</a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="passwordChangeForm" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title">Change Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php if (!empty($user['Password'])): ?>
                            <div class="mb-3">
                                <label for="currentPassword" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="currentPassword" name="current_password">
                            </div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="newPassword" name="new_password" required
                                onkeyup="checkPasswordStrength(this.value)">
                            <div class="password-strength-container">
                                <div class="password-strength-text" id="password-strength-text">
                                    Password must contain at least 8 characters, one uppercase, one lowercase, and one number
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                            <div class="invalid-feedback">Passwords don't match</div>
                        </div>
                        <div id="passwordChangeMessage"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="submitPasswordBtn">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Ready to Leave?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Select "Logout" below if you are ready to end your current session.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Logout</button>
                </div>
            </div>
        </div>
    </div>



    <?php
    include("footer.php");
    ?>
    <?php
    include("chatbot.php");
    ?>





    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>
    <script>
        // Set the user ID from PHP session
        window.userId = <?= isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null' ?>;
    </script>
    <!-- Your cart script -->
    <script src="js/cart.js"></script>

    <!-- Search functionality (your script block) -->
    <script src="js/search.js"></script>




    <script src="js/profilesetting.js"></script>

    <script src="js/chatbot.js"></script>

</body>

</html>
</body>

</html>