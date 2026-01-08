<?php
include 'connect.php';
include 'profile_calling_admin.php';

$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

// Fetch user details
$user_stmt = $pdo->prepare("SELECT * FROM user WHERE User_ID = ?");
$user_stmt->execute([$user_id]);
$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

// Fetch course details
$course_stmt = $pdo->prepare("SELECT * FROM courses WHERE Course_ID = ?");
$course_stmt->execute([$course_id]);
$course = $course_stmt->fetch(PDO::FETCH_ASSOC);

// Fetch completed content
$completed_stmt = $pdo->prepare("
    SELECT cc.*, up.Completed_At 
    FROM user_progress up
    JOIN course_content cc ON up.Content_ID = cc.Content_ID
    WHERE up.User_ID = ? AND up.Course_ID = ?
    ORDER BY up.Completed_At DESC
");
$completed_stmt->execute([$user_id, $course_id]);
$completed_content = $completed_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all course content for progress calculation
$all_content_stmt = $pdo->prepare("SELECT * FROM course_content WHERE Course_ID = ?");
$all_content_stmt->execute([$course_id]);
$all_content = $all_content_stmt->fetchAll(PDO::FETCH_ASSOC);

$progress_percentage = count($completed_content) > 0 ? round((count($completed_content) / count($all_content)) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Progress Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <style>
        /* Base Styles */
        body {
            background-color: #f0f2f5;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }

        h2,
        h4 {
            color: #212529;
            font-weight: 600;
        }

        /* Container */
        .container {
            max-width: 900px;
            margin: auto;
            padding: 30px 20px;
        }

        /* Card Styling */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            background-color: #ffffff;
            overflow: hidden;
            transition: box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 25px 30px;
        }

        /* Progress */
        .progress-container {
            height: 14px;
            background-color: #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            margin: 10px 0;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #4caf50, #81c784);
            transition: width 0.5s ease;
            border-radius: 8px 0 0 8px;
        }

        /* Completed Content */
        .list-group-item {
            background-color: #fff;
            border: 1px solid #e0e0e0;
            margin-bottom: 10px;
            border-radius: 8px;
            padding: 15px 20px;
            transition: background-color 0.3s;
        }

        .list-group-item:hover {
            background-color: #f8f9fa;
        }

        .content-item.completed {
            border-left: 5px solid #28a745;
            color: #28a745;
            font-weight: 500;
            background-color: #e9f7ef;
        }

        .content-item.completed:before {
            content: "✓ ";
            font-weight: bold;
            color: #28a745;
            margin-right: 5px;
        }

        /* Date Styling */
        small.text-muted {
            font-size: 13px;
            color: #6c757d;
        }

        /* Button */
        .btn-secondary {
            background-color: #6c757d;
            border: none;
            padding: 10px 24px;
            font-weight: 500;
            border-radius: 6px;
            transition: background-color 0.3s;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            color: #fff;
        }

        /* Responsive Tweaks */
        @media (max-width: 576px) {
            .card-body {
                padding: 20px;
            }

            .list-group-item {
                padding: 12px 16px;
            }

            h2 {
                font-size: 22px;
            }

            h4 {
                font-size: 18px;
            }
        }
    </style>
</head>

<body>
    <div class="container py-4">
        <h2 class="mb-3">User Progress Details</h2>

        <div class="card mb-4">
            <div class="card-body">
                <h4><?= htmlspecialchars($user['Name']) ?> - <?= htmlspecialchars($course['Course_Name']) ?></h4>
                <div class="d-flex justify-content-between">
                    <span>Progress: <?= $progress_percentage ?>%</span>
                </div>
                <div class="progress-container">
                    <div class="progress-bar" style="width: <?= $progress_percentage ?>%"></div>
                </div>
                <small class="text-muted"><?= count($completed_content) ?> of <?= count($all_content) ?> items completed</small>
            </div>
        </div>

        <h4>Completed Content</h4>
        <div class="list-group">
            <?php foreach ($completed_content as $content): ?>
                <div class="list-group-item content-item completed my-3">
                    <div class="d-flex justify-content-between">
                        <span><?= htmlspecialchars($content['Title']) ?></span>
                        <small class="text-muted"><?= date('M j, Y H:i', strtotime($content['Completed_At'])) ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-4">
            <a href="user_progress.php" class="btn btn-secondary">Back to Progress Overview</a>
        </div>
    </div>
</body>

</html>