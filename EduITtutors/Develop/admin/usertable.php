<!DOCTYPE html>
<html lang="en">
<?php
include('profile_calling_admin.php');
include("connect.php"); ?>


<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Melody Admin</title>
    <!-- plugins:css -->
    <link rel="icon" type="image/png" href="../photo/logo/EduITtutors_Colorver_Logo.png" style="width: 250px; height: auto;">
    <link rel="stylesheet" href="vendors/iconfonts/font-awesome/css/all.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.addons.css">

    <!-- endinject -->
    <!-- plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="http://www.urbanui.com/" />
</head>

<body>
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        <?php

        include('header.php');
        ?>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <?php
            include('choosesidebar.php');
            ?>
            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header">
                        <h3 class="page-title">
                            Users Profiles
                        </h3>
                    </div>
                    <!-- Table Start -->
                    <div class="container-fluid pt-4 px-4">
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="bg-light rounded h-100 p-4">
                                    <h6 class="mb-4">User Table</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>User ID</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Role</th>
                                                    <th>Register Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                try {
                                                    $stmt = $pdo->query("SELECT * FROM user");
                                                    $admin_count_sql = "SELECT COUNT(*) as admin_count FROM user WHERE Role = 'Admin'";
                                                    $admin_count_stmt = $pdo->query($admin_count_sql);
                                                    $admin_count = $admin_count_stmt->fetch(PDO::FETCH_ASSOC)['admin_count'];
                                                    $count = 1;
                                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                        echo "<tr>";
                                                        echo "<td>" . $count++ . "</td>";
                                                        echo "<td>" . $row['User_ID'] . "</td>";
                                                        echo "<td>" . htmlspecialchars($row['Name']) . "</td>";
                                                        echo "<td>" . htmlspecialchars($row['Email']) . "</td>";
                                                        echo "<td>" . htmlspecialchars($row['Role']) . "</td>";
                                                        echo "<td>" . htmlspecialchars($row['Register_Date']) . "</td>";
                                                        echo "<td>
                                                            <a href='user_view.php?id=" . $row['User_ID'] . "' class='btn btn-info btn-sm'>View</a>
                                                            <a href='user_edit.php?id=" . $row['User_ID'] . "' class='btn btn-primary btn-sm'>Update</a>
                                                            <a href='user_delete.php?id=" . $row['User_ID'] . "' class='btn btn-danger btn-sm' 
                                                            data-role='" . htmlspecialchars($row['Role']) . "' 
                                                            data-admin-count='" . ($row['Role'] === 'Admin' ? $admin_count : '0') . "'>Delete</a>
                                                        </td>";
                                                        echo "</tr>";
                                                    }
                                                } catch (Exception $e) {
                                                    echo "<tr><td colspan='7'>Error loading users: " . $e->getMessage() . "</td></tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Table End -->

                </div>
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->

    <!-- plugins:js -->
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <script src="vendors/js/vendor.bundle.addons.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page-->
    <!-- End plugin js for this page-->
    <!-- inject:js -->
    <script src="js/off-canvas.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/misc.js"></script>
    <script src="js/settings.js"></script>
    <script src="js/todolist.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="js/dashboard.js"></script>
    <!-- End custom js for this page-->
    <script>
        // Check for error messages in URL
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const error = urlParams.get('error');

            if (error === 'last_admin') {
                alert('You cannot delete the last remaining admin!');
                // Remove the error parameter from URL
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });

        // Enhance the existing delete confirmation to check for last admin before submitting
        document.querySelectorAll('.btn-danger').forEach(button => {
            button.addEventListener('click', function(e) {
                // Check if this is the last admin (we'll need to add a data attribute)
                if (this.dataset.role === 'Admin' && this.dataset.adminCount === '1') {
                    e.preventDefault();
                    alert('You cannot delete the last remaining admin!');
                    return false;
                }

                // Proceed with normal confirmation
                if (!confirm('Are you sure you want to delete this user?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>


</html>