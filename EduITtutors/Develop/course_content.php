<?PHP
include("profilecalling.php");
include('admin/connect.php');

// Get course ID from URL
$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch course details
$course_stmt = $pdo->prepare("SELECT * FROM courses WHERE Course_ID = ?");
$course_stmt->execute([$course_id]);
$course = $course_stmt->fetch(PDO::FETCH_ASSOC);

// After fetching the course details, fetch the related description and curriculum data
$description_stmt = $pdo->prepare("SELECT * FROM course_descriptions WHERE Description_ID = ?");
$description_stmt->execute([$course['Description_ID']]);
$course_description = $description_stmt->fetch(PDO::FETCH_ASSOC);

$curriculum_stmt = $pdo->prepare("SELECT * FROM curriculum WHERE Curriculum_ID = ?");
$curriculum_stmt->execute([$course['Curriculum_ID']]);
$course_curriculum = $curriculum_stmt->fetch(PDO::FETCH_ASSOC);

// Now merge these into the course array for easier access
$course = array_merge($course, $course_description, $course_curriculum);

if (!$course) {
    die("Course not found");
}

// Fetch teacher details
$teacher_stmt = $pdo->prepare("SELECT * FROM teachers WHERE Teacher_ID = ?");
$teacher_stmt->execute([$course['Teacher_ID']]);
$teacher = $teacher_stmt->fetch(PDO::FETCH_ASSOC);

// Fetch curriculum modules
$modules_stmt = $pdo->prepare("
    SELECT * FROM curriculum_modules 
    WHERE Curriculum_ID = ?
    ORDER BY Module_Order ASC
");
$modules_stmt->execute([$course['Curriculum_ID']]);
$modules = $modules_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all lessons and content
$lessons = [];
$content_items = [];
foreach ($modules as $module) {
    $lessons_stmt = $pdo->prepare("
        SELECT * FROM curriculum_lessons 
        WHERE Module_ID = ?
        ORDER BY Lesson_Order ASC
    ");
    $lessons_stmt->execute([$module['Module_ID']]);
    $module_lessons = $lessons_stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($module_lessons as $lesson) {
        $content_stmt = $pdo->prepare("
            SELECT * FROM course_content 
            WHERE Course_ID = ? AND Lesson_ID = ?
            ORDER BY Display_Order ASC
        ");
        $content_stmt->execute([$course_id, $lesson['Lesson_ID']]);
        $lesson_content = $content_stmt->fetchAll(PDO::FETCH_ASSOC);

        $lesson['content'] = $lesson_content;
        $lessons[] = $lesson;

        foreach ($lesson_content as $content) {
            $content_items[$content['Content_ID']] = $content;
        }
    }
}

// Get current content ID from URL or default to first content
$current_content_id = isset($_GET['content_id']) ? (int)$_GET['content_id'] : 0;
if ($current_content_id === 0 && !empty($content_items)) {
    $first_content = reset($content_items);
    $current_content_id = $first_content['Content_ID'];
}

// Mark content as completed if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_content']) && $user) {
    $content_id = (int)$_POST['content_id'];

    // Check if already completed
    $check_stmt = $pdo->prepare("
        SELECT * FROM user_progress 
        WHERE User_ID = ? AND Content_ID = ?
    ");
    $check_stmt->execute([$user['User_ID'], $content_id]);

    if (!$check_stmt->fetch()) {
        $insert_stmt = $pdo->prepare("
            INSERT INTO user_progress (User_ID, Content_ID, Course_ID)
            VALUES (?, ?, ?)
        ");
        $insert_stmt->execute([$user['User_ID'], $content_id, $course_id]);
    }

    // Redirect to prevent form resubmission
    header("Location: course_content.php?id=$course_id&content_id=$content_id");
    exit();
}

// Calculate progress
$total_content = count($content_items);
$completed_content = 0;
$progress_percentage = 0;

if ($user && $total_content > 0) {
    $progress_stmt = $pdo->prepare("
        SELECT COUNT(*) FROM user_progress 
        WHERE User_ID = ? AND Course_ID = ?
    ");
    $progress_stmt->execute([$user['User_ID'], $course_id]);
    $completed_content = $progress_stmt->fetchColumn();

    $progress_percentage = round(($completed_content / $total_content) * 100);
}

// Check if course is completed (for certificate)
$is_course_completed = ($progress_percentage == 100);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($course['Course_Name']) ?> | Online Course</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="photo/logo/EduITtutors_Colorver_Logo.png" style="width: 250px; height: auto;">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/videolesson.css">
    <link rel="stylesheet" href="css/cartslidebar.css">
    <link rel="stylesheet" href="css/search.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/chatbot.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,700|Roboto:300,400,500,700,900" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">

    <!-- Fancybox CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />
    <!-- Font Awesome for play icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .course-content-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            margin-top: 30px;
        }

        .left-column {
            flex: 1;
            min-width: 300px;
        }

        .right-column {
            width: 350px;
        }

        .video-container {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%;
            margin-bottom: 2rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .video-container iframe,
        .video-container video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 20px;
        }

        /* Fallback content for non-video types */
        .video-container .d-flex {
            position: absolute;
            inset: 0;
            padding: 2rem;
            background: linear-gradient(145deg, rgba(30, 30, 30, 0.85), rgba(20, 20, 20, 0.85));
            color: #ffffff;
            text-align: center;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            transition: background 0.3s;
        }

        .video-container i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #00d4ff;
            opacity: 0.8;
        }

        .video-container h3 {
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #ffffff;
        }

        .video-container p {
            font-size: 1rem;
            margin-bottom: 1rem;
            color: #dddddd;
        }

        .video-container .btn {
            background: linear-gradient(135deg, #00d4ff, #0078ff);
            border: none;
            padding: 0.6rem 1.3rem;
            font-size: 0.95rem;
            font-weight: 500;
            color: white;
            border-radius: 10px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .video-container .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 122, 255, 0.3);
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .video-container {
                padding-bottom: 65%;
            }

            .video-container h3 {
                font-size: 1.4rem;
            }

            .video-container p {
                font-size: 0.95rem;
            }
        }

        @media (max-width: 480px) {
            .video-container {
                padding-bottom: 75%;
            }

            .video-container h3 {
                font-size: 1.2rem;
            }

            .video-container p {
                font-size: 0.9rem;
            }

            .video-container .btn {
                font-size: 0.85rem;
                padding: 0.5rem 1rem;
            }
        }


        .content-card {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 2rem;
            margin-top: 1.5rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            transition: box-shadow 0.3s ease, transform 0.2s ease;
        }

        .content-card:hover {
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .content-card h3 {
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .content-card p.text-muted {
            font-size: 0.95rem;
            color: #666;
            margin-bottom: 1rem;
        }

        .content-card i.far.fa-clock {
            margin-right: 6px;
            color: #0d6efd;
        }

        /* Description Text */
        .content-card .mt-3 {
            font-size: 1rem;
            color: #444;
            line-height: 1.6;
            white-space: pre-line;
        }

        /* Completion Button Styling */
        .content-card form {
            margin-top: 1.5rem;
        }

        .content-card .btn-success {
            background-color: #28a745;
            border: none;
            padding: 0.55rem 1.2rem;
            font-size: 0.95rem;
            font-weight: 500;
            border-radius: 8px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .content-card .btn-success:hover {
            background-color: #218838;
            transform: translateY(-1px);
        }

        .content-card .btn-outline-success {
            padding: 0.55rem 1.2rem;
            font-size: 0.95rem;
            border-radius: 8px;
            font-weight: 500;
            color: #28a745;
            border: 1px solid #28a745;
            background-color: #f8f9fa;
        }

        .content-card .btn-outline-success:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        /* Responsive Styling */
        @media (max-width: 576px) {
            .content-card {
                padding: 1.5rem;
            }

            .content-card h3 {
                font-size: 1.4rem;
            }

            .content-card .mt-3 {
                font-size: 0.95rem;
            }

            .content-card .btn-success,
            .content-card .btn-outline-success {
                width: 100%;
                text-align: center;
            }
        }


        /* Instructor profile container */
        .instructor-profile {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            gap: 1rem;
        }

        /* Instructor image styling */
        .instructor-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #0d6efd;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
            transition: transform 0.3s ease;
        }

        .instructor-img:hover {
            transform: scale(1.05);
        }

        /* Instructor name */
        .instructor-profile h5 {
            font-size: 1.1rem;
            margin: 0;
            font-weight: 600;
            color: #222;
        }

        /* Role (Head Mentor / Instructor) */
        .instructor-profile .text-muted {
            font-size: 0.9rem;
            color: #777 !important;
        }

        /* Instructor bio paragraph */
        .content-card p {
            font-size: 1rem;
            color: #444;
            line-height: 1.6;
            white-space: pre-line;
        }

        /* Responsive behavior */
        @media (max-width: 576px) {
            .instructor-profile {
                flex-direction: column;
                align-items: flex-start;
            }

            .instructor-img {
                width: 70px;
                height: 70px;
            }

            .instructor-profile h5 {
                font-size: 1rem;
            }
        }

        /* Tab navigation styling */
        .nav-tabs {
            border-bottom: 2px solid #eaeaea;
        }

        .nav-tabs .nav-link {
            font-weight: 500;
            color: #555;
            border: none;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            padding: 0.6rem 1.2rem;
        }

        .nav-tabs .nav-link:hover {
            color: #0d6efd;
            background-color: #f9f9f9;
        }

        .nav-tabs .nav-link.active {
            color: #0d6efd;
            border-bottom: 3px solid #0d6efd;
            background-color: #fff;
        }

        /* Tab content area */
        .tab-content {
            margin-top: 1rem;
        }

        /* Section headings */
        .tab-content h5 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: #333;
        }

        /* Overview text & lists */
        .tab-content p {
            font-size: 1rem;
            color: #444;
            line-height: 1.6;
        }

        .tab-content ul {
            padding-left: 1.5rem;
        }

        .tab-content ul li {
            margin-bottom: 0.5rem;
            font-size: 1rem;
            color: #333;
        }

        /* Accordion styles */
        .accordion-button {
            font-weight: 600;
            background-color: #f8f9fa;
            color: #212529;
            transition: background-color 0.3s ease;
        }

        .accordion-button:not(.collapsed) {
            background-color: #e9f2ff;
            color: #0d6efd;
            box-shadow: none;
        }

        .accordion-button:focus {
            box-shadow: none;
            border-color: transparent;
        }

        .accordion-item {
            border: none;
            border-bottom: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .accordion-body {
            background-color: #ffffff;
            padding: 1rem 1.25rem;
        }

        /* Nested lesson list styles */
        .accordion-body ul {
            padding-left: 1.25rem;
        }

        .accordion-body ul li {
            font-size: 0.96rem;
            color: #555;
        }

        .accordion-body a {
            color: #0d6efd;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .accordion-body a:hover {
            color: #0b5ed7;
            text-decoration: underline;
        }

        .accordion-body i {
            margin-right: 0.5rem;
        }

        .accordion-body .fa-check-circle {
            color: #28a745;
            font-size: 0.95rem;
        }

        /* Responsive tweaks */
        @media (max-width: 576px) {
            .nav-tabs .nav-link {
                padding: 0.5rem 0.8rem;
                font-size: 0.95rem;
            }

            .tab-content h5 {
                font-size: 1.1rem;
            }

            .accordion-body ul li {
                font-size: 0.9rem;
            }
        }

        /* Style specifically for course detail card */
        .coursecard {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
            transition: box-shadow 0.3s ease;
        }

        .coursecard:hover {
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.1);
        }

        .coursecard h4 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1.25rem;
        }

        /* List Styling */
        .coursecard ul {
            padding-left: 0;
            margin: 0;
        }

        .coursecard li {
            display: flex;
            align-items: center;
            font-size: 1rem;
            color: #444;
            background: #f8f9fa;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            margin-bottom: 0.75rem;
            transition: background-color 0.2s ease;
        }

        .coursecard li:hover {
            background-color: #eef4ff;
        }

        /* Icons */
        .coursecard i {
            color: #0d6efd;
            font-size: 1.1rem;
            margin-right: 0.6rem;
            width: 20px;
            text-align: center;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .coursecard {
                padding: 1.5rem;
            }

            .coursecard h4 {
                font-size: 1.15rem;
            }

            .coursecard li {
                font-size: 0.95rem;
                padding: 0.6rem 0.9rem;
            }

            .coursecard i {
                font-size: 1rem;
                margin-right: 0.5rem;
            }
        }

        /* Base card styling inherited from .content-card */
        .progress-card {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
            position: relative;
            transition: box-shadow 0.3s ease, transform 0.2s ease;
        }

        .progress-card:hover {
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .progress-card h4 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1.2rem;
        }

        /* Progress bar */
        .progress {
            height: 12px;
            background-color: #e9ecef;
            border-radius: 6px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(to right, #28a745, #45d06f);
            transition: width 0.6s ease-in-out;
        }

        /* Completion stats */
        .progress-card p {
            margin-top: 1rem;
            font-size: 0.9rem;
            color: #666;
        }

        /* Certificate Button */
        .certificate-btn {
            margin-top: 1rem;
            display: inline-block;
            background-color: #0d6efd;
            color: #fff;
            padding: 0.6rem 1.2rem;
            font-size: 0.95rem;
            font-weight: 500;
            border-radius: 8px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .certificate-btn i {
            margin-right: 6px;
        }

        .certificate-btn:hover {
            background-color: #0b5ed7;
            transform: translateY(-1px);
        }

        /* Celebrate completed course */
        .course-complete {
            border: 2px solid #28a745;
            background: #f3fdf6;
        }

        .course-complete h4 {
            color: #28a745;
        }

        .course-complete .progress-bar {
            background: linear-gradient(to right, #28a745, #45d06f);
        }

        .course-complete .certificate-btn {
            background: linear-gradient(to right, #1e90ff, #00c6ff);
            border: none;
            color: #fff;
        }

        .course-complete .certificate-btn:hover {
            background: linear-gradient(to right, #0b78e3, #00b5e2);
        }

        /* Responsive */
        @media (max-width: 576px) {
            .progress-card {
                padding: 1.5rem;
            }

            .certificate-btn {
                width: 100%;
                text-align: center;
            }
        }


        .lessoncard {
            background-color: #fff;
            border-radius: 12px;
            padding: 1.8rem 2rem;
            margin-top: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            font-family: system-ui, sans-serif;
        }

        .lessoncard h4 {
            font-weight: 600;
            font-size: 1.25rem;
            color: #222;
            margin-bottom: 1.5rem;
        }

        /* Module Title */
        .lessoncard .fw-bold {
            color: #0b5ed7;
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 0.6rem;
            font-size: 1.05rem;
        }

        /* Lesson Item */
        .lesson-item {
            background: #f9f9f9;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: background-color 0.15s ease;
        }

        .lesson-item:hover {
            background-color: #e9efff;
        }

        .lesson-item.completed {
            border-left: 4px solid #198754;
            /* green */
            background-color: #f0f6f0;
        }

        .lesson-item.active {
            background-color: #e7f1ff;
            border-left: 4px solid #0d6efd;
            /* blue */
        }

        /* Lesson Title & Chevron */
        .lesson-item .lesson-title {
            font-weight: 500;
            color: #333;
            font-size: 1rem;
        }

        .lesson-item i.fas.fa-chevron-down {
            color: #666;
            font-size: 0.9rem;
            transition: transform 0.2s ease;
        }

        .lesson-item.active i.fas.fa-chevron-down {
            transform: rotate(180deg);
        }

        /* Content List */
        .content-list {
            padding-left: 1.4rem;
            margin-top: 0.5rem;
            border-left: 1px solid #ddd;
        }

        /* Content Item */
        .content-item {
            padding: 0.3rem 0;
            font-size: 0.95rem;
            color: #444;
            display: flex;
            align-items: center;
        }

        .content-item a {
            display: flex;
            align-items: center;
            color: #0d6efd;
            text-decoration: none;
            width: 100%;
        }

        .content-item a:hover {
            text-decoration: underline;
        }

        .content-item.active a {
            font-weight: 600;
        }

        .content-item.completed a::after {
            content: "\f058";
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            color: #198754;
            margin-left: auto;
            font-size: 0.9rem;
        }

        /* Icon next to content */
        .content-icon i {
            width: 18px;
            text-align: center;
            margin-right: 0.5rem;
            color: #555;
            font-size: 0.9rem;
        }

        /* Responsive tweaks */
        @media (max-width: 576px) {
            .lessoncard {
                padding: 1.5rem 1.8rem;
            }

            .lesson-item .lesson-title {
                font-size: 0.95rem;
            }

            .content-item {
                font-size: 0.9rem;
            }
        }


        @media (max-width: 992px) {
            .right-column {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <?php include("header.php"); ?>

    <div class="container py-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="courses.php">Courses</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($course['Course_Name']) ?></li>
            </ol>
        </nav>

        <h1 class="mb-4"><?= htmlspecialchars($course['Course_Name']) ?></h1>

        <div class="course-content-container">
            <!-- Left Column -->
            <div class="left-column">
                <!-- Video Player Section -->
                <?php if (isset($content_items[$current_content_id])):
                    $current_content = $content_items[$current_content_id];
                ?>
                    <div class="video-container">
                        <?php if ($current_content['Content_Type'] === 'video' && !empty($current_content['Video_URL'])):
                            $video_url = $current_content['Video_URL'];
                            $embed_code = '';

                            // Check for YouTube
                            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $video_url, $matches)) {
                                $video_id = $matches[1];
                                $embed_code = '<iframe id="course-video" src="https://www.youtube.com/embed/' . $video_id . '?enablejsapi=1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                            }
                            // Check for Vimeo
                            elseif (preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|)(\d+)(?:|\/\?)/', $video_url, $matches)) {
                                $video_id = $matches[2];
                                $embed_code = '<iframe id="course-video" src="https://player.vimeo.com/video/' . $video_id . '" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
                            }
                            // Check for Dailymotion
                            elseif (preg_match('/^https?:\/\/(www\.)?dailymotion\.com\/video\/([^_]+)/', $video_url, $matches)) {
                                $video_id = $matches[2];
                                $embed_code = '<iframe id="course-video" src="https://www.dailymotion.com/embed/video/' . $video_id . '" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
                            }
                            // Check for direct video files (mp4, webm, ogg)
                            elseif (preg_match('/\.(mp4|webm|ogg)$/i', $video_url)) {
                                $embed_code = '
                                <video id="course-video" controls style="width:100%; height:100%;">
                                    <source src="admin/uploads/video/' . htmlspecialchars($video_url) . '" type="video/' . pathinfo($video_url, PATHINFO_EXTENSION) . '">
                                    Your browser does not support the video tag.
                                </video>';
                            }

                            if (!empty($embed_code)): ?>
                                <?= $embed_code ?>
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center bg-dark text-white" style="height: 500px;">
                                    <div class="text-center p-4">
                                        <i class="fas fa-video-slash fa-3x mb-3"></i>
                                        <h3>Video Content</h3>
                                        <p>This content is not available for playback.</p>
                                        <a href="<?= htmlspecialchars($video_url) ?>" target="_blank" class="btn btn-primary">Open Video</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center bg-dark text-white" style="height: 500px;">
                                <div class="text-center p-4">
                                    <?php if ($current_content['Content_Type'] === 'pdf'): ?>
                                        <i class="fas fa-file-pdf fa-3x mb-3"></i>
                                        <h3>PDF Document</h3>
                                        <a href="admin/<?= htmlspecialchars($current_content['File_Path']) ?>" target="_blank" class="btn btn-primary">View PDF</a>
                                    <?php elseif ($current_content['Content_Type'] === 'quiz'): ?>
                                        <i class="fas fa-question-circle fa-3x mb-3"></i>
                                        <h3>Quiz</h3>
                                        <a href="<?= htmlspecialchars($current_content['google_form_link']) ?>" target="_blank" class="btn btn-primary">Take Quiz</a>
                                    <?php elseif ($current_content['Content_Type'] === 'assignment'): ?>
                                        <i class="fas fa-tasks fa-3x mb-3"></i>
                                        <h3>Assignment</h3>
                                        <a href="admin/<?= htmlspecialchars($current_content['File_Path']) ?>" target="_blank" class="btn btn-primary">View Assignment</a>
                                    <?php elseif ($current_content['Content_Type'] === 'reading'): ?>
                                        <i class="fas fa-book fa-3x mb-3"></i>
                                        <h3>Reading Material</h3>
                                        <a href="admin/<?= htmlspecialchars($current_content['File_Path']) ?>" target="_blank" class="btn btn-primary">Read Material</a>
                                    <?php elseif ($current_content['Content_Type'] === 'download'): ?>
                                        <i class="fas fa-download fa-3x mb-3"></i>
                                        <h3>Downloadable Resource</h3>
                                        <a href="admin/<?= htmlspecialchars($current_content['File_Path']) ?>" class="btn btn-primary" download>Download</a>
                                    <?php else: ?>
                                        <i class="fas fa-file-alt fa-3x mb-3"></i>
                                        <h3>Course Content</h3>
                                        <p>This content type is not configured for display.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="content-card">
                        <h3><?= htmlspecialchars($current_content['Title']) ?></h3>
                        <?php if (!empty($current_content['Duration'])): ?>
                            <p class="text-muted"><i class="far fa-clock"></i> Duration: <?= htmlspecialchars($current_content['Duration']) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($current_content['Description'])): ?>
                            <div>
                                <?= nl2br(htmlspecialchars($current_content['Description'])) ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($user): ?>
                            <form method="post" class="mt-4">
                                <input type="hidden" name="content_id" value="<?= $current_content_id ?>">
                                <?php
                                // Check if content is already completed
                                $completed_stmt = $pdo->prepare("
                                    SELECT * FROM user_progress 
                                    WHERE User_ID = ? AND Content_ID = ?
                                ");
                                $completed_stmt->execute([$user['User_ID'], $current_content_id]);
                                $is_completed = $completed_stmt->fetch();
                                ?>

                                <?php if (!$is_completed): ?>
                                    <button type="submit" name="complete_content" class="btn btn-success">
                                        <i class="fas fa-check-circle"></i> Mark as Complete
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-outline-success" disabled>
                                        <i class="fas fa-check-circle"></i> Completed
                                    </button>
                                <?php endif; ?>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        No content available for this course yet.
                    </div>
                <?php endif; ?>

                <!-- Instructor Profile -->
                <div class="content-card">
                    <h4 class="mb-4">About the Instructor</h4>
                    <div class="instructor-profile">
                        <img src="admin/<?= htmlspecialchars($teacher['Teacher_Photo']) ?>" alt="<?= htmlspecialchars($teacher['Teacher_Name']) ?>" class="instructor-img">
                        <div>
                            <h5><?= htmlspecialchars($teacher['Teacher_Name']) ?></h5>
                            <p class="text-muted mb-1"><?= htmlspecialchars($teacher['Teacher_Role']) === 'main' ? 'Head Mentor' : 'Instructor' ?></p>
                        </div>
                    </div>
                    <p><?= nl2br(htmlspecialchars($teacher['Teacher_Bio'])) ?></p>
                </div>

                <!-- Tabbed Info -->
                <div class="content-card">
                    <ul class="nav nav-tabs" id="courseTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">Overview</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="curriculum-tab" data-bs-toggle="tab" data-bs-target="#curriculum" type="button" role="tab">Curriculum</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="requirements-tab" data-bs-toggle="tab" data-bs-target="#requirements" type="button" role="tab">Requirements</button>
                        </li>
                    </ul>

                    <div class="tab-content pt-3" id="courseTabsContent">
                        <div class="tab-pane fade show active" id="overview" role="tabpanel">
                            <h5>Course Description</h5>
                            <p><?= nl2br(htmlspecialchars($course['Introduction_Text'])) ?></p>

                            <h5 class="mt-4">What You'll Learn</h5>
                            <ul>
                                <?php
                                $learn_items = explode("\n", $course['Curriculum_Description']);
                                foreach ($learn_items as $item):
                                    if (trim($item)): ?>
                                        <li><?= htmlspecialchars(trim($item)) ?></li>
                                <?php endif;
                                endforeach; ?>
                            </ul>
                        </div>


                        <div class="tab-pane fade" id="curriculum" role="tabpanel">
                            <div class="accordion" id="curriculumAccordion">
                                <?php foreach ($modules as $module): ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="module-<?= $module['Module_ID'] ?>-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#module-<?= $module['Module_ID'] ?>">
                                                <?= htmlspecialchars($module['Module_Title']) ?>
                                            </button>
                                        </h2>
                                        <div id="module-<?= $module['Module_ID'] ?>" class="accordion-collapse collapse" aria-labelledby="module-<?= $module['Module_ID'] ?>-header" data-bs-parent="#curriculumAccordion">
                                            <div class="accordion-body">
                                                <p><?= nl2br(htmlspecialchars($module['Module_Description'])) ?></p>

                                                <ul class="list-unstyled">
                                                    <?php
                                                    $module_lessons = array_filter($lessons, function ($lesson) use ($module) {
                                                        return $lesson['Module_ID'] == $module['Module_ID'];
                                                    });

                                                    foreach ($module_lessons as $lesson): ?>
                                                        <li class="mb-2">
                                                            <strong><?= htmlspecialchars($lesson['Lesson_Title']) ?></strong>
                                                            <ul class="list-unstyled ms-3 mt-2">
                                                                <?php foreach ($lesson['content'] as $content):
                                                                    $is_completed = false;
                                                                    if ($user) {
                                                                        $stmt = $pdo->prepare("
                                                                            SELECT COUNT(*) FROM user_progress 
                                                                            WHERE User_ID = ? AND Content_ID = ?
                                                                        ");
                                                                        $stmt->execute([$user['User_ID'], $content['Content_ID']]);
                                                                        $is_completed = $stmt->fetchColumn();
                                                                    }
                                                                ?>
                                                                    <li class="mb-1">
                                                                        <a href="course_content.php?id=<?= $course_id ?>&content_id=<?= $content['Content_ID'] ?>" class="text-decoration-none">
                                                                            <i class="fas fa-<?=
                                                                                                $content['Content_Type'] === 'video' ? 'play-circle' : ($content['Content_Type'] === 'pdf' ? 'file-pdf' : ($content['Content_Type'] === 'quiz' ? 'question-circle' : ($content['Content_Type'] === 'assignment' ? 'tasks' : ($content['Content_Type'] === 'reading' ? 'book' : ($content['Content_Type'] === 'download' ? 'download' : 'file-alt')))))
                                                                                                ?> me-2"></i>
                                                                            <?= htmlspecialchars($content['Title']) ?>
                                                                            <?php if ($is_completed): ?>
                                                                                <i class="fas fa-check-circle text-success ms-2"></i>
                                                                            <?php endif; ?>
                                                                        </a>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="requirements" role="tabpanel">
                            <h5>Requirements</h5>
                            <p><?= nl2br(htmlspecialchars($course['Requirements'])) ?></p>

                            <h5 class="mt-4">Who This Course Is For</h5>
                            <p><?= nl2br(htmlspecialchars($course['Target_Audience'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="right-column">
                <!-- Course Details Card -->
                <div class="coursecard">
                    <h4>Course Details</h4>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="far fa-clock me-2"></i> Duration: <?= htmlspecialchars($course['Duration']) ?></li>
                        <li class="mb-2"><i class="fas fa-chart-line me-2"></i> Level: Intermediate</li>
                        <li class="mb-2"><i class="fas fa-language me-2"></i> Language: English</li>
                        <li class="mb-2"><i class="fas fa-certificate me-2"></i> Certificate: Yes</li>
                    </ul>
                </div>

                <!-- Progress Card -->
                <div class="content-card progress-card <?= $is_course_completed ? 'course-complete' : '' ?>">
                    <h4>Your Progress</h4>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Course Completion</span>
                            <span><?= $progress_percentage ?>%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $progress_percentage ?>%" aria-valuenow="<?= $progress_percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <p class="small text-muted">
                        <?= $completed_content ?> of <?= $total_content ?> items completed
                    </p>

                    <?php if ($is_course_completed): ?>
                        <a href="generate_certificate.php?course_id=<?= $course_id ?>&user_id=<?= $user['User_ID'] ?>" class="btn btn-primary certificate-btn">
                            <i class="fas fa-certificate"></i> Get Your Certificate
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Lessons Card -->
                <div class="content-card lessoncard">
                    <h4>Course Content</h4>
                    <div class="list-group list-group-flush">
                        <?php foreach ($modules as $module):
                            $module_lessons = array_filter($lessons, function ($lesson) use ($module) {
                                return $lesson['Module_ID'] == $module['Module_ID'];
                            });

                            if (empty($module_lessons)) continue;
                        ?>
                            <div class="fw-bold mt-3 mb-2 text-primary"><?= htmlspecialchars($module['Module_Title']) ?></div>

                            <?php foreach ($module_lessons as $lesson):
                                $lesson_completed = true;
                                foreach ($lesson['content'] as $content) {
                                    if ($user) {
                                        $stmt = $pdo->prepare("
                                            SELECT COUNT(*) FROM user_progress 
                                            WHERE User_ID = ? AND Content_ID = ?
                                        ");
                                        $stmt->execute([$user['User_ID'], $content['Content_ID']]);
                                        if (!$stmt->fetchColumn()) {
                                            $lesson_completed = false;
                                            break;
                                        }
                                    } else {
                                        $lesson_completed = false;
                                        break;
                                    }
                                }
                            ?>
                                <div class="lesson-item <?= $lesson_completed ? 'completed' : '' ?> <?= in_array($current_content_id, array_column($lesson['content'], 'Content_ID')) ? 'active' : '' ?>">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="lesson-title"><?= htmlspecialchars($lesson['Lesson_Title']) ?></div>
                                        <i class="fas fa-chevron-down"></i>
                                    </div>

                                    <div class="content-list mt-2" style="display: <?= in_array($current_content_id, array_column($lesson['content'], 'Content_ID')) ? 'block' : 'none' ?>">
                                        <?php foreach ($lesson['content'] as $content):
                                            $is_completed = false;
                                            if ($user) {
                                                $stmt = $pdo->prepare("
                                                    SELECT COUNT(*) FROM user_progress 
                                                    WHERE User_ID = ? AND Content_ID = ?
                                                ");
                                                $stmt->execute([$user['User_ID'], $content['Content_ID']]);
                                                $is_completed = $stmt->fetchColumn();
                                            }
                                        ?>

                                            <div class="content-item <?= $content['Content_ID'] == $current_content_id ? 'active' : '' ?> <?= $is_completed ? 'completed' : '' ?>">
                                                <a href="course_content.php?id=<?= $course_id ?>&content_id=<?= $content['Content_ID'] ?>" class="text-decoration-none d-block">
                                                    <span class="content-icon">
                                                        <i class="fas fa-<?=
                                                                            $content['Content_Type'] === 'video' ? 'play' : ($content['Content_Type'] === 'pdf' ? 'file-pdf' : ($content['Content_Type'] === 'quiz' ? 'question-circle' : ($content['Content_Type'] === 'assignment' ? 'tasks' : ($content['Content_Type'] === 'reading' ? 'book' : ($content['Content_Type'] === 'download' ? 'download' : 'file-alt')))))
                                                                            ?>"></i>
                                                    </span>
                                                    <?= htmlspecialchars($content['Title']) ?>
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include("footer.php"); ?>
    <?php include("chatbot.php"); ?>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>
    <script src="js/cart.js"></script>


    <!-- Search functionality (your script block) -->
    <script src="js/search.js"></script>
    <script src="js/chatbot.js"></script>
    <script>
        // Toggle content list visibility when clicking on lesson items
        document.querySelectorAll('.lesson-item').forEach(item => {
            item.querySelector('.lesson-title, .d-flex').addEventListener('click', function(e) {
                if (e.target.tagName !== 'A') {
                    const contentList = item.querySelector('.content-list');
                    const icon = item.querySelector('.fa-chevron-down');

                    if (contentList.style.display === 'none') {
                        contentList.style.display = 'block';
                        icon.classList.remove('fa-chevron-down');
                        icon.classList.add('fa-chevron-up');
                    } else {
                        contentList.style.display = 'none';
                        icon.classList.remove('fa-chevron-up');
                        icon.classList.add('fa-chevron-down');
                    }
                }
            });
        });

        // YouTube API for video tracking
        let player;
        let videoStartTime = 0;
        let videoInterval;

        function onYouTubeIframeAPIReady() {
            if (document.getElementById('course-video')) {
                player = new YT.Player('course-video', {
                    events: {
                        'onReady': onPlayerReady,
                        'onStateChange': onPlayerStateChange
                    }
                });
            }
        }

        function onPlayerReady(event) {
            // Player is ready
        }

        function onPlayerStateChange(event) {
            if (event.data == YT.PlayerState.PLAYING) {
                videoStartTime = Math.floor(Date.now() / 1000);
                startTrackingVideoProgress();
            } else if (event.data == YT.PlayerState.PAUSED ||
                event.data == YT.PlayerState.ENDED) {
                clearInterval(videoInterval);

                // Send progress to server if video was watched for at least 30 seconds
                const durationWatched = Math.floor(Date.now() / 1000) - videoStartTime;
                if (durationWatched >= 30) {
                    trackVideoProgress(durationWatched);
                }
            }
        }

        function startTrackingVideoProgress() {
            clearInterval(videoInterval);
            videoInterval = setInterval(() => {
                const currentTime = Math.floor(Date.now() / 1000);
                const durationWatched = currentTime - videoStartTime;

                // Every 30 seconds, send progress to server
                if (durationWatched % 30 === 0) {
                    trackVideoProgress(durationWatched);
                }
            }, 1000);
        }

        function trackVideoProgress(duration) {
            // In a real implementation, you would send this to your server
            console.log(`Tracked ${duration} seconds of video watched`);

            // Example AJAX call (uncomment and implement your endpoint)
            /*
            fetch('track_video_progress.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    content_id: <?= $current_content_id ?>,
                    duration_watched: duration,
                    user_id: <?= $user ? $user['User_ID'] : 'null' ?>
                }),
            });
            */
        }

        // Load YouTube API
        const tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        const firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

        // Save video progress when leaving page
        window.addEventListener('beforeunload', function() {
            if (player && player.getPlayerState && player.getPlayerState() === YT.PlayerState.PLAYING) {
                const currentTime = Math.floor(Date.now() / 1000);
                const durationWatched = currentTime - videoStartTime;

                if (durationWatched >= 30) {
                    trackVideoProgress(durationWatched);
                }
            }
        });
    </script>
</body>

</html>