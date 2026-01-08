<!DOCTYPE html>
<html lang="en">

<?php
include('profile_calling_admin.php');
include 'connect.php'; // Include your connection file

// Fetch all user progress data with user and course information
try {
    $progressQuery = "
    SELECT 
        up.User_ID, 
        u.Name AS User_Name,
        up.Course_ID,
        c.Course_Name,
        COUNT(up.Content_ID) AS Completed_Content,
        (SELECT COUNT(*) FROM course_content WHERE Course_ID = up.Course_ID) AS Total_Content,
        ROUND((COUNT(up.Content_ID) / (SELECT COUNT(*) FROM course_content WHERE Course_ID = up.Course_ID)) * 100) AS Progress_Percentage,
        MAX(up.Completed_At) AS Last_Activity
    FROM 
        user_progress up
    JOIN 
        user u ON up.User_ID = u.User_ID AND u.Role = 'User'  -- Only students
    JOIN 
        courses c ON up.Course_ID = c.Course_ID
    GROUP BY 
        up.User_ID, up.Course_ID
    ORDER BY 
        Last_Activity DESC
    ";

    $progressStmt = $pdo->query($progressQuery);
    $userProgress = $progressStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching user progress: " . $e->getMessage());
}

// Fetch all certificates
try {
    $certQuery = "
        SELECT 
            c.Certificate_ID,
            c.User_ID,
            u.Name AS User_Name,
            c.Course_ID,
            co.Course_Name,
            c.Certificate_Number,
            c.Issue_Date,
            c.Created_At
        FROM 
            certificates c
        JOIN 
            user u ON c.User_ID = u.User_ID
        JOIN 
            courses co ON c.Course_ID = co.Course_ID
        ORDER BY 
            c.Created_At DESC
    ";

    $certStmt = $pdo->query($certQuery);
    $certificates = $certStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching certificates: " . $e->getMessage());
}
?>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>User Progress & Certificates</title>
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

    <style>
        .white-box {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 25px;
            padding: 2rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
        }

        .progress-container {
            height: 10px;
            background-color: #f0f0f0;
            border-radius: 5px;
            margin-top: 5px;
        }

        .progress-bar {
            height: 100%;
            border-radius: 5px;
            background-color: #4CAF50;
        }

        .badge-certified {
            background-color: #28a745;
            color: white;
        }

        .badge-in-progress {
            background-color: #ffc107;
            color: black;
        }

        .badge-not-started {
            background-color: #6c757d;
            color: white;
        }

        .nav-tabs .nav-link.active {
            font-weight: bold;
            border-bottom: 3px solid #0d6efd;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .certificate-link {
            color: #0d6efd;
            text-decoration: none;
            font-weight: bold;
        }

        .certificate-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        <?php include('header.php'); ?>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <?php include('choosesidebar.php'); ?>
            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header">
                        <h3 class="page-title">
                            User Progress and Certificates
                        </h3>
                    </div>

                    <div class="row">
                        <div class="col-12 grid-margin">
                            <div class="card white-box">
                                <div class="card-body">
                                    <ul class="nav nav-tabs" id="progressTabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="progress-tab" data-bs-toggle="tab" data-bs-target="#progress" type="button" role="tab">Learning Progress</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="certificates-tab" data-bs-toggle="tab" data-bs-target="#certificates" type="button" role="tab">Certificates Issued</button>
                                        </li>
                                    </ul>

                                    <div class="tab-content pt-3" id="progressTabsContent">
                                        <!-- Learning Progress Tab -->
                                        <div class="tab-pane fade show active" id="progress" role="tabpanel">
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>User</th>
                                                            <th>Course</th>
                                                            <th>Progress</th>
                                                            <th>Status</th>
                                                            <th>Last Activity</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($userProgress as $progress):
                                                            $progressPercent = $progress['Progress_Percentage'];
                                                            $isCertified = $progressPercent == 100;
                                                        ?>
                                                            <tr>
                                                                <td><?= htmlspecialchars($progress['User_Name']) ?></td>
                                                                <td><?= htmlspecialchars($progress['Course_Name']) ?></td>
                                                                <td>
                                                                    <div class="d-flex justify-content-between">
                                                                        <span><?= $progressPercent ?>%</span>
                                                                    </div>
                                                                    <div class="progress-container">
                                                                        <div class="progress-bar" style="width: <?= $progressPercent ?>%"></div>
                                                                    </div>
                                                                    <small class="text-muted"><?= $progress['Completed_Content'] ?> of <?= $progress['Total_Content'] ?> items completed</small>
                                                                </td>
                                                                <td>
                                                                    <?php if ($isCertified): ?>
                                                                        <span class="badge badge-certified">Certified</span>
                                                                    <?php elseif ($progressPercent > 0): ?>
                                                                        <span class="badge badge-in-progress">In Progress</span>
                                                                    <?php else: ?>
                                                                        <span class="badge badge-not-started">Not Started</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td><?= date('M j, Y', strtotime($progress['Last_Activity'])) ?></td>
                                                                <td>
                                                                    <a href="user_progress_details.php?user_id=<?= $progress['User_ID'] ?>&course_id=<?= $progress['Course_ID'] ?>" class="btn btn-sm btn-info">Details</a>
                                                                    <?php if ($isCertified): ?>
                                                                        <a href="../generate_certificate.php?user_id=<?= $progress['User_ID'] ?>&course_id=<?= $progress['Course_ID'] ?>" class="btn btn-sm btn-success" target="_blank">View Cert</a>
                                                                    <?php endif; ?>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Certificates Tab -->
                                        <div class="tab-pane fade" id="certificates" role="tabpanel">
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Certificate Number</th>
                                                            <th>User</th>
                                                            <th>Course</th>
                                                            <th>Issue Date</th>
                                                            <th>Created At</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($certificates as $cert): ?>
                                                            <tr>
                                                                <td>
                                                                    <a href="../generate_certificate.php?user_id=<?= $cert['User_ID'] ?>&course_id=<?= $cert['Course_ID'] ?>" class="certificate-link" target="_blank">
                                                                        <?= htmlspecialchars($cert['Certificate_Number']) ?>
                                                                    </a>
                                                                </td>
                                                                <td><?= htmlspecialchars($cert['User_Name']) ?></td>
                                                                <td><?= htmlspecialchars($cert['Course_Name']) ?></td>
                                                                <td><?= date('M j, Y', strtotime($cert['Issue_Date'])) ?></td>
                                                                <td><?= date('M j, Y H:i', strtotime($cert['Created_At'])) ?></td>
                                                                <td>
                                                                    <a href="../generate_certificate.php?user_id=<?= $cert['User_ID'] ?>&course_id=<?= $cert['Course_ID'] ?>" class="btn btn-sm btn-primary" target="_blank">View</a>
                                                                    <button class="btn btn-sm btn-danger delete-certificate" data-id="<?= $cert['Certificate_ID'] ?>">Delete</button>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle certificate deletion
        document.querySelectorAll('.delete-certificate').forEach(button => {
            button.addEventListener('click', function() {
                const certId = this.getAttribute('data-id');
                if (confirm('Are you sure you want to delete this certificate? This action cannot be undone.')) {
                    fetch('delete_certificate.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'certificate_id=' + certId
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Certificate deleted successfully');
                                this.closest('tr').remove();
                            } else {
                                alert('Error deleting certificate: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while deleting the certificate');
                        });
                }
            });
        });
    </script>
</body>

</html>