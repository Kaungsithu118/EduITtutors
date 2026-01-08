<!DOCTYPE html>
<html lang="en">

<?php
include("profilecalling.php");
?>

<?php
include("admin/connect.php");


$teacher_id = $_GET['id'] ?? 0; // Example to get from URL
// Use your DB connection here, e.g. PDO or MySQLi
$stmt = $pdo->prepare("SELECT * FROM teachers WHERE Teacher_ID = ?");
$stmt->execute([$teacher_id]);
$teacher = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$teacher) {
    echo "<p>Teacher not found.</p>";
    exit;
}

?>

<head>
    <meta charset="UTF-8">
    <title>Teacher Profile Section</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="photo/logo/EduITtutors_Colorver_Logo.png" style="width: 250px; height: auto;">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/teacherdetail.css">
    <link rel="stylesheet" href="css/cartslidebar.css">
    <link rel="stylesheet" href="css/search.css">

    <!-- Font Awesome for the icons (use CDN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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
                        <div class="breadcrumbs mb-2">
                            <ul>
                                <li><a href="index.html">Home</a></li>
                                <li style="color: #384158; font-size: 16px; font-weight: 700;">Teachers Details</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>









    <section class="teacher-profile-section" aria-label="Teacher details section">

        <!-- Left Side: Photo, Name, Title -->
        <div class="teacher-profile__side" style="display: flex; justify-content: center; align-items: center;">
            <div class="teacher-profile__image-card">
                <?php if (!empty($teacher['Teacher_Photo'])): ?>
                    <img src="admin/<?= htmlspecialchars($teacher['Teacher_Photo']) ?>" alt="Portrait of <?= htmlspecialchars($teacher['Teacher_Name']) ?>" class="teacher-profile__image" />
                <?php else: ?>
                    <img src="photo/default-teacher.jpg" alt="Portrait of <?= htmlspecialchars($teacher['Teacher_Name']) ?>" class="teacher-profile__image" />
                <?php endif; ?>
                <div class="teacher-profile__name"><?= htmlspecialchars($teacher['Teacher_Name']) ?></div>
                <div class="teacher-profile__role"><?= htmlspecialchars($teacher['Teacher_Role'] === 'main' ? 'Lead Instructor' : 'Instructor') ?></div>
            </div>
        </div>

        <!-- Right Side: Details -->
        <div class="teacher-profile__main">
            <div class="teacher-profile__title fw-bold fs-2">
                Meet Our Instructor
            </div>

            <div class="teacher-profile__bullets">

                <div class="bio">
                    <p class="my-4"><?= nl2br(htmlspecialchars($teacher['Teacher_Bio'] ?? "No biography available.")) ?></p>
                </div>

                <div class="teacher-profile__bullet">
                    <div class="teacher-profile__bullet-icon"><i class="fas fa-user-circle"></i></div>
                    <div class="teacher-profile__bullet-content mt-1 fw-bold">
                        Experienced Educators<br>
                        <div style="font-weight: 400; color: #888;" class="mt-3">
                            <?= nl2br(htmlspecialchars($teacher['Experience_Text'] ?? "Our instructors bring years of teaching and industry experience to support your learning journey.")) ?>
                        </div>
                    </div>
                </div>

                <div class="teacher-profile__bullet mt-4">
                    <div class="teacher-profile__bullet-icon"><i class="fas fa-briefcase"></i></div>
                    <div class="teacher-profile__bullet-content mt-1 fw-bold">
                        Real-World Expertise<br>
                        <div style="font-weight: 400; color: #888;" class="mt-3">
                            <?= nl2br(htmlspecialchars($teacher['Expertise_Text'] ?? "Tutors at EduITtutors apply practical knowledge gained from real-world IT projects and workplaces.")) ?>
                        </div>
                    </div>
                </div>

            </div>

            <div class="teacher-profile__skills-title py-3 fw-bold fs-5">Core Competencies</div>
            <ul class="teacher-profile__skills-list">
                <li class="teacher-profile__skill-row">
                    <div class="teacher-profile__skill-label">Curriculum</div>
                    <div class="teacher-profile__skill-bar-track" aria-label="Skill progress">
                        <div class="teacher-profile__skill-bar" style="width: <?= (int)$teacher['Curriculum_Percent'] ?: 0 ?>%;"></div>
                    </div>
                    <div class="teacher-profile__skill-value"><?= (int)$teacher['Curriculum_Percent'] ?: 0 ?>%</div>
                </li>
                <li class="teacher-profile__skill-row">
                    <div class="teacher-profile__skill-label">Knowledge</div>
                    <div class="teacher-profile__skill-bar-track" aria-label="Skill progress">
                        <div class="teacher-profile__skill-bar" style="width: <?= (int)$teacher['Knowledge_Percent'] ?: 0 ?>%;"></div>
                    </div>
                    <div class="teacher-profile__skill-value"><?= (int)$teacher['Knowledge_Percent'] ?: 0 ?>%</div>
                </li>
                <li class="teacher-profile__skill-row">
                    <div class="teacher-profile__skill-label">Communication</div>
                    <div class="teacher-profile__skill-bar-track" aria-label="Skill progress">
                        <div class="teacher-profile__skill-bar" style="width: <?= (int)$teacher['Communication_Percent'] ?: 0 ?>%;"></div>
                    </div>
                    <div class="teacher-profile__skill-value"><?= (int)$teacher['Communication_Percent'] ?: 0 ?>%</div>
                </li>
                <li class="teacher-profile__skill-row">
                    <div class="teacher-profile__skill-label">Proficiency</div>
                    <div class="teacher-profile__skill-bar-track" aria-label="Skill progress">
                        <div class="teacher-profile__skill-bar" style="width: <?= (int)$teacher['Proficiency_Percent'] ?: 0 ?>%;"></div>
                    </div>
                    <div class="teacher-profile__skill-value"><?= (int)$teacher['Proficiency_Percent'] ?: 0 ?>%</div>
                </li>
            </ul>

            <div class="icons mt-5">
                <h2 class="teacher-profile__title fw-bold fs-4">Social-Media Links</h2>
                <div class="d-flex mb-5" style="gap: 50px; font-size: 15px;">
                    <?php if (!empty($teacher['Facebook_Link'])): ?>
                        <a href="<?= htmlspecialchars($teacher['Facebook_Link']) ?>" target="_blank"><i class="fa-brands fa-facebook fa-2x"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($teacher['Instagram_Link'])): ?>
                        <a href="<?= htmlspecialchars($teacher['Instagram_Link']) ?>" target="_blank"><i class="fa-brands fa-instagram fa-2x"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($teacher['Codepen_Link'])): ?>
                        <a href="<?= htmlspecialchars($teacher['Codepen_Link']) ?>" target="_blank"><i class="fa-brands fa-codepen fa-2x"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($teacher['LinkedIn_Link'])): ?>
                        <a href="<?= htmlspecialchars($teacher['LinkedIn_Link']) ?>" target="_blank"><i class="fa-brands fa-linkedin fa-2x"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($teacher['Twitter_Link'])): ?>
                        <a href="<?= htmlspecialchars($teacher['Twitter_Link']) ?>" target="_blank"><i class="fa-brands fa-twitter fa-2x"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </section>

    <?php
    include("footer.php");
    ?>




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



</body>

</html>