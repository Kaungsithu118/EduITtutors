<!DOCTYPE html>
<html lang="en">

<?php
include ('profile_calling_admin.php');
include("connect.php");

// Fetch all teachers from database
$stmt = $pdo->prepare("SELECT * FROM Teachers");
$stmt->execute();
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Display success/error messages
if (isset($_GET['delete_success']) && $_GET['delete_success'] == 1) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Teacher deleted successfully!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
          </div>';
}

if (isset($_GET['error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            Error: ' . htmlspecialchars($_GET['error']) . '
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
          </div>';
}
?>



<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>TeacherCards</title>
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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">

    <link rel="stylesheet" href="vendors/iconfonts/font-awesome/css/all.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.addons.css">
    <!-- endinject -->
    <!-- inject:css -->
    <link rel="stylesheet" href="css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="images/favicon.png" />

    <link rel="stylesheet" href="css/teachercard.css">

    <!-- Font Awesome for play icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />



    <style>
        .white-box {
            background: #ffffff;
            /* solid white background */
            border: 1px solid #e5e7eb;
            /* thin light border (optional) */
            border-radius: 25px;
            padding: 2rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
            /* soft shadow */

        }

        .card {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .card-main {
            flex: 1;
        }

        /* Skill bars styling */
        .skill-bars {
            margin-top: 15px;
        }

        .skill-item {
            margin-bottom: 10px;
        }

        .skill-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        .progress {
            height: 8px;
            background-color: #f0f0f0;
            border-radius: 4px;
        }

        .progress-bar {
            background-color: #4b7bec;
            border-radius: 4px;
        }

        /* Card footer buttons */
        .card-footer {
            padding: 15px;
            background: #f8f9fa;
            border-top: 1px solid #eee;
        }
    </style>
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
                            TeacherCards
                        </h3>
                    </div>
                    <section id="teachercards" class="py-4">
                        <div class="row">
                            <?php foreach ($teachers as $teacher): ?>
                                <div class="col-lg-3 col-md-6 mb-4">
                                    <div class="card" data-state="#about-<?= $teacher['Teacher_ID'] ?>">
                                        <div class="card-header">
                                            <div class="card-cover" style="background-image: url('images/business-card-background.jpg')"></div>
                                            <img class="card-avatar" src="<?= htmlspecialchars($teacher['Teacher_Photo']) ?>" alt="<?= htmlspecialchars($teacher['Teacher_Name']) ?>" />
                                            <h1 class="card-fullname"><?= htmlspecialchars($teacher['Teacher_Name']) ?></h1>
                                            <h2 class="card-jobtitle">
                                                <?= $teacher['Teacher_Role'] === 'main' ? 'Main Teacher' : 'Regular Teacher' ?>
                                            </h2>
                                        </div>
                                        <div class="card-main">
                                            <div class="card-section is-active" id="about-<?= $teacher['Teacher_ID'] ?>">
                                                <div class="card-content">
                                                    <div class="card-subtitle">Bio</div>
                                                    <p class="card-desc"><?= htmlspecialchars($teacher['Teacher_Bio']) ?></p>
                                                </div>
                                                <div class="card-social">
                                                    <?php if (!empty($teacher['Facebook_Link'])): ?>
                                                        <a href="<?= htmlspecialchars($teacher['Facebook_Link']) ?>" target="_blank"><i class="fa-brands fa-facebook fa-2x"></i></a>
                                                    <?php endif; ?>
                                                    <?php if (!empty($teacher['Instagram_Link'])): ?>
                                                        <a href="<?= htmlspecialchars($teacher['Instagram_Link']) ?>" target="_blank"><i class="fa-brands fa-instagram fa-2x ml-3"></i></a>
                                                    <?php endif; ?>
                                                    <?php if (!empty($teacher['Codepen_Link'])): ?>
                                                        <a href="<?= htmlspecialchars($teacher['Codepen_Link']) ?>" target="_blank"><i class="fa-brands fa-codepen fa-2x ml-3"></i></a>
                                                    <?php endif; ?>
                                                    <?php if (!empty($teacher['Twitter_Link'])): ?>
                                                        <a href="<?= htmlspecialchars($teacher['Twitter_Link']) ?>" target="_blank"><i class="fa-brands fa-twitter fa-2x ml-3"></i></a>
                                                    <?php endif; ?>
                                                    <?php if (!empty($teacher['LinkedIn_Link'])): ?>
                                                        <a href="<?= htmlspecialchars($teacher['LinkedIn_Link']) ?>" target="_blank"><i class="fa-brands fa-linkedin fa-2x ml-3"></i></a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="card-section" id="experience-<?= $teacher['Teacher_ID'] ?>">
                                                <div class="card-content">
                                                    <div class="card-subtitle">WORK EXPERIENCE</div>
                                                    <div class="card-timeline">
                                                        <div class="card-item">
                                                            <div class="card-item-desc"><?= htmlspecialchars($teacher['Experience_Text']) ?></div>
                                                        </div>
                                                    </div>
                                                    <div class="card-subtitle mt-3">AREAS OF EXPERTISE</div>
                                                    <div class="card-timeline">
                                                        <div class="card-item">
                                                            <div class="card-item-desc"><?= htmlspecialchars($teacher['Expertise_Text']) ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-section" id="contact-<?= $teacher['Teacher_ID'] ?>">
                                                <div class="card-content">
                                                    <div class="card-subtitle">CONTACT</div>
                                                    <div class="card-contact-wrapper">
                                                        <div class="card-contact">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" />
                                                                <circle cx="12" cy="10" r="3" />
                                                            </svg>
                                                            <?= htmlspecialchars($teacher['Location'] ?? 'Yangon') ?>
                                                        </div>
                                                        <div class="card-contact">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z" />
                                                            </svg>
                                                            <?= htmlspecialchars($teacher['Phone'] ?? '(123) 456-7890') ?>
                                                        </div>
                                                        <div class="card-contact">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                                                <path d="M22 6l-10 7L2 6" />
                                                            </svg>
                                                            <?= htmlspecialchars($teacher['Email'] ?? strtolower(str_replace(' ', '', ($teacher['Teacher_Name'] ?? ''))) . '@university.edu') ?>
                                                        </div>
                                                    </div>
                                                    <div class="skill-bars mt-3">
                                                        <div class="skill-item">
                                                            <div class="skill-info">
                                                                <span>Curriculum Design</span>
                                                                <span><?= htmlspecialchars($teacher['Curriculum_Percent']) ?>%</span>
                                                            </div>
                                                            <div class="progress">
                                                                <div class="progress-bar" role="progressbar" style="width: <?= htmlspecialchars($teacher['Curriculum_Percent']) ?>%"
                                                                    aria-valuenow="<?= htmlspecialchars($teacher['Curriculum_Percent']) ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                        <div class="skill-item">
                                                            <div class="skill-info">
                                                                <span>Subject Knowledge</span>
                                                                <span><?= htmlspecialchars($teacher['Knowledge_Percent']) ?>%</span>
                                                            </div>
                                                            <div class="progress">
                                                                <div class="progress-bar" role="progressbar" style="width: <?= htmlspecialchars($teacher['Knowledge_Percent']) ?>%"
                                                                    aria-valuenow="<?= htmlspecialchars($teacher['Knowledge_Percent']) ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                        <div class="skill-item">
                                                            <div class="skill-info">
                                                                <span>Communication</span>
                                                                <span><?= htmlspecialchars($teacher['Communication_Percent']) ?>%</span>
                                                            </div>
                                                            <div class="progress">
                                                                <div class="progress-bar" role="progressbar" style="width: <?= htmlspecialchars($teacher['Communication_Percent']) ?>%"
                                                                    aria-valuenow="<?= htmlspecialchars($teacher['Communication_Percent']) ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                        <div class="skill-item">
                                                            <div class="skill-info">
                                                                <span>Technical Proficiency</span>
                                                                <span><?= htmlspecialchars($teacher['Proficiency_Percent']) ?>%</span>
                                                            </div>
                                                            <div class="progress">
                                                                <div class="progress-bar" role="progressbar" style="width: <?= htmlspecialchars($teacher['Proficiency_Percent']) ?>%"
                                                                    aria-valuenow="<?= htmlspecialchars($teacher['Proficiency_Percent']) ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-buttons">
                                                <button data-section="#about-<?= $teacher['Teacher_ID'] ?>" class="is-active">ABOUT</button>
                                                <button data-section="#experience-<?= $teacher['Teacher_ID'] ?>">EXPERIENCE</button>
                                                <button data-section="#contact-<?= $teacher['Teacher_ID'] ?>">CONTACT</button>
                                            </div>
                                        </div>
                                        <div class="card-footer d-flex justify-content-between">
                                            <a href="teacheredit.php?id=<?= $teacher['Teacher_ID'] ?>" class="btn btn-primary btn-sm">Edit</a>
                                            <button class="btn btn-danger btn-sm delete-teacher" data-id="<?= $teacher['Teacher_ID'] ?>">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                </div>
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
    <script src="js/teachercards.js"> </script>



</body>


</html>