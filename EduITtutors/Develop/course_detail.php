<?php
session_start();

include("admin/connect.php");

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
if ($user_id) {
    $stmt = $pdo->prepare("SELECT * FROM user WHERE User_ID = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get current date
$currentDate = date('Y-m-d H:i:s');

// Query active event discounts for this course
$discountQuery = "
    SELECT e.discount_percentage 
    FROM event_discounts e
    JOIN event_discount_courses ec ON e.event_id = ec.event_id
    WHERE ec.course_id = ? 
    AND e.is_active = 1 
    AND ? BETWEEN e.start_datetime AND e.end_datetime
    LIMIT 1
";

// Get course ID from URL
$course_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Prepare discount statement
$discountStmt = $pdo->prepare($discountQuery);
$discountStmt->execute([$course_id, $currentDate]);
$discount = $discountStmt->fetch(PDO::FETCH_ASSOC);

// Calculate discounted price if applicable
$hasDiscount = !empty($discount);
$discountPercent = $hasDiscount ? $discount['discount_percentage'] : 0;
$originalPrice = 0; // Will be set when fetching course details
$discountedPrice = 0; // Will be calculated

// Fetch course details
$course_query = "
    SELECT 
        c.*,
        t.Teacher_Name, t.Teacher_Photo, t.Teacher_Bio, t.Email, t.Phone, t.Location,
        d.Department_Name,
        cd.Introduction_Text, cd.Requirements, cd.Target_Audience,
        cu.Curriculum_Description,

        -- Accurate module count
        (
            SELECT COUNT(DISTINCT cm.Module_ID)
            FROM Curriculum_Modules cm
            WHERE cm.Curriculum_ID = c.Curriculum_ID
        ) as module_count,

        -- Accurate lesson count
        (
            SELECT COUNT(cl.Lesson_ID)
            FROM Curriculum_Modules cm
            JOIN Curriculum_Lessons cl ON cm.Module_ID = cl.Module_ID
            WHERE cm.Curriculum_ID = c.Curriculum_ID
        ) as lesson_count

    FROM Courses c
    JOIN Teachers t ON c.Teacher_ID = t.Teacher_ID
    JOIN Departments d ON c.Department_ID = d.Department_ID
    JOIN Course_Descriptions cd ON c.Description_ID = cd.Description_ID
    JOIN Curriculum cu ON c.Curriculum_ID = cu.Curriculum_ID
    WHERE c.Course_ID = ?
";

$stmt = $pdo->prepare($course_query);
$stmt->execute([$course_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    die("Course not found");
}

// Calculate prices
$originalPrice = $course['Course_Fees'];
$discountedPrice = $hasDiscount ?
    $originalPrice * (1 - $discountPercent / 100) :
    $originalPrice;

// Fetch curriculum modules and lessons
$modules_query = "
    SELECT DISTINCT cm.Module_ID, cm.Module_Title, cm.Module_Description, cm.Module_Order
    FROM Curriculum_Modules cm
    WHERE cm.Curriculum_ID = ?
    ORDER BY cm.Module_Order
";
$stmt = $pdo->prepare($modules_query);
$stmt->execute([$course['Curriculum_ID']]);
$modules = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($modules as &$module) {
    $lessons_query = "
        SELECT Lesson_ID, Lesson_Title, Lesson_Description, Lesson_Order 
        FROM Curriculum_Lessons
        WHERE Module_ID = ?
        ORDER BY Lesson_Order
    ";
    $stmt = $pdo->prepare($lessons_query);
    $stmt->execute([$module['Module_ID']]);
    $module['lessons'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
unset($module);

// Fetch latest courses for sidebar
$latest_courses = $pdo->query("
    SELECT Course_ID, Course_Name, Course_Photo, Course_Fees 
    FROM Courses 
    ORDER BY updated_at DESC 
    LIMIT 3
")->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subject</title>

    <link rel="icon" type="image/png" href="photo/logo/EduITtutors_Colorver_Logo.png" style="width: 250px; height: auto;">
    <link rel="stylesheet" href="css/subject.css">
    <link rel="stylesheet" href="css/subject_responsive.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/cartslidebar.css">
    <link rel="stylesheet" href="css/search.css">
    <link rel="stylesheet" href="css/social_button.css">
    <link rel="stylesheet" href="css/chatbot.css">
    <link rel="stylesheet" href="css/footer.css">

    <!-- Font Awesome for play icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,700|Roboto:300,400,500,700,900"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">

    <style>
        .custom-btn {
            width: 130px;
            height: 40px;
            color: #fff;
            border-radius: 5px;
            padding: 10px 25px;
            font-family: 'Lato', sans-serif;
            font-weight: 500;
            background: transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            display: inline-block;
            box-shadow: inset 2px 2px 2px 0px rgba(255, 255, 255, .5),
                7px 7px 20px 0px rgba(0, 0, 0, .1),
                4px 4px 5px 0px rgba(0, 0, 0, .1);
            outline: none;
        }

        /* 12 */
        .btn-12 {
            position: relative;
            right: 20px;
            bottom: 20px;
            border: none;
            box-shadow: none;
            width: 130px;
            height: 40px;
            line-height: 42px;
            -webkit-perspective: 230px;
            perspective: 230px;
        }

        .btn-12 span {
            background: rgb(0, 172, 238);
            background: linear-gradient(0deg, rgba(0, 172, 238, 1) 0%, rgba(2, 126, 251, 1) 100%);
            display: block;
            position: absolute;
            width: 130px;
            height: 40px;
            box-shadow: inset 2px 2px 2px 0px rgba(255, 255, 255, .5),
                7px 7px 20px 0px rgba(0, 0, 0, .1),
                4px 4px 5px 0px rgba(0, 0, 0, .1);
            border-radius: 5px;
            margin: 0;
            text-align: center;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            -webkit-transition: all .3s;
            transition: all .3s;
        }

        .btn-12 span:nth-child(1) {
            box-shadow:
                -7px -7px 20px 0px #fff9,
                -4px -4px 5px 0px #fff9,
                7px 7px 20px 0px #0002,
                4px 4px 5px 0px #0001;
            -webkit-transform: rotateX(90deg);
            -moz-transform: rotateX(90deg);
            transform: rotateX(90deg);
            -webkit-transform-origin: 50% 50% -20px;
            -moz-transform-origin: 50% 50% -20px;
            transform-origin: 50% 50% -20px;
        }

        .btn-12 span:nth-child(2) {
            -webkit-transform: rotateX(0deg);
            -moz-transform: rotateX(0deg);
            transform: rotateX(0deg);
            -webkit-transform-origin: 50% 50% -20px;
            -moz-transform-origin: 50% 50% -20px;
            transform-origin: 50% 50% -20px;
        }

        .btn-12:hover span:nth-child(1) {
            box-shadow: inset 2px 2px 2px 0px rgba(255, 255, 255, .5),
                7px 7px 20px 0px rgba(0, 0, 0, .1),
                4px 4px 5px 0px rgba(0, 0, 0, .1);
            -webkit-transform: rotateX(0deg);
            -moz-transform: rotateX(0deg);
            transform: rotateX(0deg);
        }

        .btn-12:hover span:nth-child(2) {
            box-shadow: inset 2px 2px 2px 0px rgba(255, 255, 255, .5),
                7px 7px 20px 0px rgba(0, 0, 0, .1),
                4px 4px 5px 0px rgba(0, 0, 0, .1);
            color: transparent;
            -webkit-transform: rotateX(-90deg);
            -moz-transform: rotateX(-90deg);
            transform: rotateX(-90deg);
        }
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
                                <li><a href="course.php">Course</a></li>
                                <li style="color: #384158; font-size: 16px; font-weight: 700;">
                                    Course_Detail
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="course">
        <div class="container">
            <div class="row">
                <!-- Course Main Content -->
                <div class="col-lg-8">
                    <div class="course_container">
                        <div class="course_title"><?= htmlspecialchars($course['Course_Name']) ?></div>
                        <div class="course_info d-flex flex-lg-row flex-column align-items-lg-center align-items-start justify-content-start">
                            <!-- Teacher Info -->
                            <div class="course_info_item">
                                <div class="course_info_title">Teacher:</div>
                                <div class="course_info_text">
                                    <a href="teacherdetail.php?id=<?= $course['Teacher_ID'] ?>">
                                        <?= htmlspecialchars($course['Teacher_Name']) ?>
                                    </a>
                                </div>
                            </div>

                            <!-- Update Date -->
                            <div class="course_info_item">
                                <div class="course_info_title">Updated:</div>
                                <div class="course_info_text"><?= date('Y-m-d', strtotime($course['Updated_At'])) ?></div>
                            </div>

                            <!-- Category -->
                            <div class="course_info_item">
                                <div class="course_info_title">Category:</div>
                                <div class="course_info_text">
                                    <a href="department.php?id=<?= $course['Department_ID'] ?>"><?= htmlspecialchars($course['Department_Name']) ?></a>
                                </div>
                            </div>
                        </div>

                        <!-- Course Image -->
                        <div class="course_image">
                            <img src="admin/<?= htmlspecialchars($course['Course_Photo']) ?>" alt="<?= htmlspecialchars($course['Course_Name']) ?>" style="width:100%;">
                        </div>

                        <!-- Course Tabs -->
                        <div class="course_tabs_container">
                            <div class="tabs d-flex flex-row align-items-center justify-content-start">
                                <div class="tab active" data-tab="description">Description</div>
                                <div class="tab" data-tab="curriculum">Curriculum</div>
                            </div>


                            <div class="tab_panels">
                                <!-- Description Tab -->
                                <div class="tab_panel active" id="description_panel">

                                    <div class="tab_panel_title"><?= htmlspecialchars($course['Course_Name']) ?></div>
                                    <div class="tab_panel_content">
                                        <div class="tab_panel_text">
                                            <p><?= nl2br(htmlspecialchars($course['Introduction_Text'])) ?></p>
                                        </div>

                                        <?php if (!empty($course['Requirements'])): ?>
                                            <div class="tab_panel_section my-5">
                                                <div class="tab_panel_subtitle">Requirements</div>
                                                <div class="tab_panel_text">
                                                    <p><?= nl2br(htmlspecialchars($course['Requirements'])) ?></p>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($course['Target_Audience'])): ?>
                                            <div class="tab_panel_section">
                                                <div class="tab_panel_subtitle">Who is this course for?</div>
                                                <div class="tab_panel_text">
                                                    <p><?= nl2br(htmlspecialchars($course['Target_Audience'])) ?></p>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <div class="tab_panel_faq">
                                            <div class="tab_panel_title">FAQ</div>
                                            <div class="accordions">
                                                <div class="elements_accordions">
                                                    <div class="accordion_container">
                                                        <div class="accordion d-flex flex-row align-items-center">
                                                            <div>How long does the course take to complete?</div>
                                                        </div>
                                                        <div class="accordion_panel">
                                                            <p>The course is designed to be completed in <?= htmlspecialchars($course['Duration']) ?> with about 10 hours of study per week. However, you can go at your own pace.</p>
                                                        </div>
                                                    </div>

                                                    <div class="accordion_container">
                                                        <div class="accordion d-flex flex-row align-items-center">
                                                            <div>Will I get a certificate after completion?</div>
                                                        </div>
                                                        <div class="accordion_panel">
                                                            <p>Yes! You'll receive a certificate of completion that you can add to your LinkedIn profile or resume.</p>
                                                        </div>
                                                    </div>

                                                    <div class="accordion_container">
                                                        <div class="accordion d-flex flex-row align-items-center">
                                                            <div>What is the refund policy?</div>
                                                        </div>
                                                        <div class="accordion_panel">
                                                            <p>We offer a 14-day money-back guarantee if you're not satisfied with the course.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Curriculum Tab -->
                                <div class="tab_panel" id="curriculum_panel">
                                    <div class="tab_panel_content">
                                        <div class="tab_panel_title"><?= htmlspecialchars($course['Course_Name']) ?> Curriculum</div>
                                        <div class="tab_panel_text">
                                            <p><?= nl2br(htmlspecialchars($course['Curriculum_Description'])) ?></p>
                                        </div>

                                        <div class="curriculum_container">
                                            <?php foreach ($modules as $module): ?>
                                                <div class="module_item">
                                                    <div class="module_header">
                                                        <div class="module_title">
                                                            <span>Module <?= $module['Module_Order'] ?>:</span>
                                                            <?= htmlspecialchars($module['Module_Title']) ?>
                                                        </div>
                                                        <div class="module_toggle">+</div>
                                                    </div>

                                                    <?php if (!empty($module['Module_Description'])): ?>
                                                        <div class="module_description">
                                                            <p><?= htmlspecialchars($module['Module_Description']) ?></p>
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="lessons_container">
                                                        <?php foreach ($module['lessons'] as $lesson): ?>
                                                            <div class="lesson_item">
                                                                <div class="lesson_title">
                                                                    <span>Lesson <?= $lesson['Lesson_Order'] ?>:</span>
                                                                    <?= htmlspecialchars($lesson['Lesson_Title']) ?>
                                                                </div>
                                                                <?php if (!empty($lesson['Lesson_Description'])): ?>
                                                                    <div class="lesson_description">
                                                                        <p><?= htmlspecialchars($lesson['Lesson_Description']) ?></p>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>





                            </div>
                        </div>
                    </div>
                </div>

                <!-- Course Sidebar -->
                <div class="col-lg-4">
                    <div class="sidebar">
                        <!-- Course Features -->
                        <div class="sidebar_section">
                            <div class="sidebar_section_title">Course Features</div>
                            <div class="sidebar_feature">
                                <div class="course_price">
                                    <?php if ($hasDiscount): ?>
                                        <span class="original-price" style="text-decoration: line-through; color: #888; margin-right: 10px;">
                                            $<?= number_format($originalPrice, 2) ?>
                                        </span>
                                        <span class="discounted-price" style="font-weight: bold;">
                                            $<?= number_format($discountedPrice, 2) ?>
                                        </span>
                                        <span class="discount-badge" style="background-color:rgb(0, 11, 92); color: white; padding: 2px 8px; border-radius: 4px; font-size: 12px; margin-left: 10px;">
                                            <?= $discountPercent ?>% OFF
                                        </span>
                                    <?php else: ?>
                                        $<?= number_format($originalPrice, 2) ?>
                                    <?php endif; ?>
                                </div>

                                <div class="feature_list mt-4">
                                    <div class="feature d-flex flex-row align-items-center justify-content-start">
                                        <div class="feature_title">
                                            <i class="fa fa-clock" aria-hidden="true"></i>
                                            <span>Duration:</span>
                                        </div>
                                        <div class="feature_text ml-auto"><?= htmlspecialchars($course['Duration']) ?></div>
                                    </div>

                                    <div class="feature d-flex flex-row align-items-center justify-content-start">
                                        <div class="feature_title">
                                            <i class="fa fa-book" aria-hidden="true"></i>
                                            <span>Modules:</span>
                                        </div>
                                        <div class="feature_text ml-auto"><?= $course['module_count'] ?></div>
                                    </div>

                                    <div class="feature d-flex flex-row align-items-center justify-content-start">
                                        <div class="feature_title">
                                            <i class="fa fa-file" aria-hidden="true"></i>
                                            <span>Lessons:</span>
                                        </div>
                                        <div class="feature_text ml-auto"><?= $course['lesson_count'] ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Teacher Info -->
                        <div class="sidebar_section">
                            <div class="sidebar_section_title">Instructor</div>
                            <div class="sidebar_teacher">
                                <div class="teacher_title_container d-flex flex-row align-items-center justify-content-start">
                                    <div class="teacher_image mb-5">
                                        <img src="admin/<?= htmlspecialchars($course['Teacher_Photo']) ?>" alt="<?= htmlspecialchars($course['Teacher_Name']) ?>">
                                    </div>
                                    <div class="teacher_title">
                                        <div class="teacher_name">
                                            <a href="teacherdetail.php?id=<?= $course['Teacher_ID'] ?>">
                                                <?= htmlspecialchars($course['Teacher_Name']) ?>
                                            </a>
                                        </div>
                                        <div class="teacher_position"><?= htmlspecialchars($course['Department_Name']) ?></div>
                                    </div>
                                </div>

                                <div class="teacher_meta_container">
                                    <div class="teacher_meta d-flex flex-row align-items-center justify-content-start">
                                        <div class="teacher_meta_title">Email:</div>
                                        <div class="teacher_meta_text ml-auto mx-2">
                                            <span><?= htmlspecialchars($course['Email']) ?></span>
                                            <i class="fa fa-envelope" aria-hidden="true" style="margin-left: 5px;"></i>
                                        </div>
                                    </div>

                                    <div class="teacher_meta d-flex flex-row align-items-center justify-content-start">
                                        <div class="teacher_meta_title">Phone:</div>
                                        <div class="teacher_meta_text ml-auto mx-2">
                                            <span><?= htmlspecialchars($course['Phone']) ?></span>
                                            <i class="fa fa-phone" aria-hidden="true" style="margin-left: 5px;"></i>
                                        </div>
                                    </div>

                                    <div class="teacher_meta d-flex flex-row align-items-center justify-content-start">
                                        <div class="teacher_meta_title">Location:</div>
                                        <div class="teacher_meta_text ml-auto mx-2">
                                            <span><?= htmlspecialchars($course['Location']) ?></span>
                                            <i class="fa fa-map-marker" aria-hidden="true" style="margin-left: 5px;"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="teacher_info">
                                    <p><?= nl2br(htmlspecialchars($course['Teacher_Bio'])) ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Latest Courses -->
                        <div class="sidebar_section">
                            <div class="sidebar_section_title">Latest Courses</div>
                            <div class="sidebar_latest">
                                <?php foreach ($latest_courses as $latest): ?>
                                    <div class="latest d-flex flex-row align-items-start justify-content-start">
                                        <div class="latest_image">
                                            <div><img src="admin/<?= htmlspecialchars($latest['Course_Photo']) ?>" alt="<?= htmlspecialchars($latest['Course_Name']) ?>"></div>
                                        </div>
                                        <div class="latest_content">
                                            <div class="latest_title">
                                                <a href="course_detail.php?id=<?= $latest['Course_ID'] ?>">
                                                    <?= htmlspecialchars($latest['Course_Name']) ?>
                                                </a>
                                            </div>
                                            <div class="latest_price">
                                                <?php
                                                // Check if this course has a discount in the event
                                                $latestDiscountStmt = $pdo->prepare($discountQuery);
                                                $latestDiscountStmt->execute([$latest['Course_ID'], $currentDate]);
                                                $latestDiscount = $latestDiscountStmt->fetch(PDO::FETCH_ASSOC);
                                                $hasLatestDiscount = !empty($latestDiscount);

                                                if ($hasLatestDiscount):
                                                    $latestDiscountedPrice = $latest['Course_Fees'] * (1 - $latestDiscount['discount_percentage'] / 100);
                                                ?>
                                                    <span style="text-decoration: line-through; color: #888; margin-right: 5px;">
                                                        $<?= number_format($latest['Course_Fees'], 2) ?>
                                                    </span>
                                                    <span style="font-weight: bold;">
                                                        $<?= number_format($latestDiscountedPrice, 2) ?>
                                                    </span>
                                                <?php else: ?>
                                                    $<?= number_format($latest['Course_Fees'], 2) ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="sidebar_section">
                            <?php if (($_SESSION['role'] ?? '') !== 'Admin'): ?>
                                <div class="sidebar_section_button frame">
                                    <!-- Add to Cart Button -->
                                    <!-- Replace the Add to Cart form with this -->
                                    <form method="post" style="display: inline-block; margin-right: 30px;">
                                        <input type="hidden" name="course_id" value="<?= $course['Course_ID'] ?>">
                                        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                                            <button type="submit" class="custom-btn btn-12"
                                                data-course-id="<?= $course['Course_ID'] ?>"
                                                data-course-name="<?= htmlspecialchars($course['Course_Name']) ?>"
                                                data-course-teacher="<?= htmlspecialchars($course['Teacher_Name']) ?>"
                                                data-course-price="<?= $hasDiscount ? $discountedPrice : $course['Course_Fees'] ?>"
                                                data-course-image="admin/<?= htmlspecialchars($course['Course_Photo']) ?>">
                                                <span>Click!</span>
                                                <span>Add To Cart</span>
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="custom-btn btn-12" onclick="window.location.href='login.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>'">
                                                <span>Click!</span>
                                                <span>Login to Add</span>
                                            </button>
                                        <?php endif; ?>
                                    </form>

                                    <!-- Buy Now Button -->
                                    <form action="cart.php" method="post" style="display: inline-block;">
                                        <input type="hidden" name="course_id" value="<?= $course['Course_ID'] ?>">
                                        <input type="hidden" name="action" value="buy_now">
                                        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                                            <button type="submit" class="custom-btn btn-12"
                                                data-course-id="<?= $course['Course_ID'] ?>"
                                                data-course-name="<?= htmlspecialchars($course['Course_Name']) ?>"
                                                data-course-teacher="<?= htmlspecialchars($course['Teacher_Name']) ?>"
                                                data-course-price="<?= $hasDiscount ? $discountedPrice : $course['Course_Fees'] ?>"
                                                data-course-image="admin/<?= htmlspecialchars($course['Course_Photo']) ?>">
                                                <span>Click!</span>
                                                <span>Buy Now</span>
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="custom-btn btn-12" onclick="window.location.href='login.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>'">
                                                <span>Click!</span>
                                                <span>Login to Buy</span>
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Newsletter -->

    <?php
    include("footer.php");
    ?>

    <?php
    include("chatbot.php");
    ?>





    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>



    <script>
        // Set the user ID from PHP session
        window.userId = <?= isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null' ?>;
    </script>
    <script>
        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log("DOM fully loaded - toggle.js executing"); // Debug line

            // 1. Tab switching functionality
            const tabs = document.querySelectorAll('.tab');
            const tabPanels = document.querySelectorAll('.tab_panel');

            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    tabs.forEach(t => t.classList.remove('active'));
                    tabPanels.forEach(p => p.classList.remove('active'));

                    this.classList.add('active');
                    const panelId = this.getAttribute('data-tab') + '_panel';
                    document.getElementById(panelId).classList.add('active');
                });
            });

            // 2. FAQ Accordion functionality
            const accordions = document.querySelectorAll('.accordion');
            accordions.forEach(accordion => {
                accordion.addEventListener('click', function() {
                    this.classList.toggle('active');
                    const panel = this.nextElementSibling;
                    panel.style.maxHeight = panel.style.maxHeight ? null : panel.scrollHeight + 'px';
                });
            });

            // 3. Curriculum dropdown functionality (NEW version)
            const moduleHeaders = document.querySelectorAll('.module_header');
            moduleHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    const moduleItem = this.closest('.module_item');
                    moduleItem.classList.toggle('active');

                    const lessonsContainer = moduleItem.querySelector('.lessons_container');
                    lessonsContainer.style.maxHeight =
                        lessonsContainer.style.maxHeight ? null : lessonsContainer.scrollHeight + 'px';
                });
            });
        });
    </script>

    <!-- Your cart script -->
    <script src="js/cart.js"></script>


    <!-- Search functionality (your script block) -->
    <script src="js/search.js"></script>

    <script src="js/chatbot.js"></script>



</body>

</html>