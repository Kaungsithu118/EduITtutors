<?php
// generate_certificate.php

include('admin/connect.php');
session_start(); // Add session start to access user data

// Get user ID either from URL or session (more secure)
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

// More secure approach - get user ID from session if available
if (isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['User_ID'];
}

if (!$user_id || !$course_id) {
    die("Invalid request. User ID and Course ID are required.");
}

// Fetch user data
$user_stmt = $pdo->prepare("SELECT * FROM user WHERE User_ID = ?");
$user_stmt->execute([$user_id]);
$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// Verify the user has completed the course
$check_completion = $pdo->prepare("
    SELECT COUNT(*) as total_content, 
           (SELECT COUNT(*) FROM user_progress 
            WHERE User_ID = ? AND Course_ID = ?) as completed_content
    FROM course_content 
    WHERE Course_ID = ?
");
$check_completion->execute([$user_id, $course_id, $course_id]);
$completion_data = $check_completion->fetch(PDO::FETCH_ASSOC);

// Check if course is completed (100% progress)
$is_completed = ($completion_data['completed_content'] == $completion_data['total_content'] && $completion_data['total_content'] > 0);

if (!$is_completed) {
    die("Course not completed by this user.");
}

// Fetch course details
$course_stmt = $pdo->prepare("SELECT * FROM courses WHERE Course_ID = ?");
$course_stmt->execute([$course_id]);
$course = $course_stmt->fetch(PDO::FETCH_ASSOC);

// Fetch teacher details
$teacher_stmt = $pdo->prepare("SELECT * FROM teachers WHERE Teacher_ID = ?");
$teacher_stmt->execute([$course['Teacher_ID']]);
$teacher = $teacher_stmt->fetch(PDO::FETCH_ASSOC);

// Fetch completion date (most recent progress entry)
$completion_date_stmt = $pdo->prepare("
    SELECT MAX(Completed_At) as completion_date 
    FROM user_progress 
    WHERE User_ID = ? AND Course_ID = ?
");
$completion_date_stmt->execute([$user_id, $course_id]);
$completion_date = $completion_date_stmt->fetchColumn();

// Format dates
$completion_date_formatted = date('F j, Y', strtotime($completion_date));
$current_date_formatted = date('F j, Y');

// Generate a unique certificate ID
$certificate_id = 'EDUIT-' . strtoupper(substr(md5($user_id . $course_id . $completion_date), 0, 8));

// Check if certificate already exists in database
$certificate_check = $pdo->prepare("
    SELECT * FROM certificates 
    WHERE User_ID = ? AND Course_ID = ?
");
$certificate_check->execute([$user_id, $course_id]);
$existing_certificate = $certificate_check->fetch();

// If certificate doesn't exist, create it
if (!$existing_certificate) {
    $issue_date = date('Y-m-d');
    $certificate_number = $certificate_id;
    
    $insert_certificate = $pdo->prepare("
        INSERT INTO certificates (User_ID, Course_ID, Certificate_Number, Issue_Date)
        VALUES (?, ?, ?, ?)
    ");
    $insert_certificate->execute([
        $user_id,
        $course_id,
        $certificate_number,
        $issue_date
    ]);
} else {
    // Use existing certificate data
    $certificate_id = $existing_certificate['Certificate_Number'];
}

// Check if PDF download is requested
if (isset($_GET['download']) && $_GET['download'] == 'pdf') {
    require('vendor/autoload.php'); // Require TCPDF library
    require_once __DIR__ . '/tcpdf/tcpdf.php';

    // Create new PDF document (A4 landscape)
    $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator('EduITtutors');
    $pdf->SetAuthor('EduITtutors');
    $pdf->SetTitle('Certificate of Completion');
    $pdf->SetSubject('Course Completion Certificate');

    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // Set margins
    $pdf->SetMargins(0, 0, 0);

    // Add a page
    $pdf->AddPage();

    // Background image (certificate template)
    $pdf->Image('admin/uploads/certificate_template.jpg', 0, 0, 297, 210, '', '', '', false, 300, '', false, false, 0);

    // Set font
    $pdf->SetFont('helvetica', '', 24, '', 'default', true);

    // Certificate title
    $pdf->SetXY(0, 40);
    $pdf->Cell(0, 0, 'Certificate of Completion', 0, 1, 'C');

    // Presented to text
    $pdf->SetFont('helvetica', '', 16, '', 'default', true);
    $pdf->SetXY(0, 60);
    $pdf->Cell(0, 0, 'This certificate is presented to', 0, 1, 'C');

    // Student name
    $pdf->SetFont('helvetica', 'B', 28, '', 'default', true);
    $pdf->SetXY(0, 75);
    $pdf->Cell(0, 0, $user['Name'], 0, 1, 'C');

    // Completion text
    $pdf->SetFont('helvetica', '', 16, '', 'default', true);
    $pdf->SetXY(0, 95);
    $pdf->Cell(0, 0, 'has successfully completed the course', 0, 1, 'C');

    // Course name
    $pdf->SetFont('helvetica', 'B', 22, '', 'default', true);
    $pdf->SetXY(0, 110);
    $pdf->Cell(0, 0, $course['Course_Name'], 0, 1, 'C');

    // Completion date
    $pdf->SetFont('helvetica', '', 14, '', 'default', true);
    $pdf->SetXY(0, 130);
    $pdf->Cell(0, 0, 'Completed on: ' . $completion_date_formatted, 0, 1, 'C');

    // Certificate ID
    $pdf->SetFont('helvetica', '', 12, '', 'default', true);
    $pdf->SetXY(0, 140);
    $pdf->Cell(0, 0, 'Certificate ID: ' . $certificate_id, 0, 1, 'C');

    // Signatures
    $pdf->SetFont('helvetica', '', 12, '', 'default', true);

    // Instructor signature
    $pdf->SetXY(40, 160);
    $pdf->Cell(80, 0, $teacher['Teacher_Name'], 0, 1, 'C');
    $pdf->SetXY(40, 165);
    $pdf->Cell(80, 0, 'Course Instructor', 0, 1, 'C');

    // CEO/Head signature
    $pdf->SetXY(180, 160);
    $pdf->Cell(80, 0, 'Dr. Maria Susan', 0, 1, 'C');
    $pdf->SetXY(180, 165);
    $pdf->Cell(80, 0, 'Head of Education', 0, 1, 'C');

    // Verification text
    $pdf->SetFont('helvetica', '', 10, '', 'default', true);
    $pdf->SetXY(0, 190);
    $pdf->Cell(0, 0, 'This certificate can be verified at: https://www.eduittutors.com/verify-certificate', 0, 1, 'C');

    // Output the PDF as a download
    exit();
}

// HTML view of the certificate
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Completion - <?= htmlspecialchars($course['Course_Name']) ?></title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Roboto', sans-serif;
        }

        .certificate-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .certificate-paper {
            width: 100%;
            height: 0;
            padding-bottom: 70.7%;
            /* A4 landscape aspect ratio */
            position: relative;
            background-image: url('admin/uploads/certificate_template.jpg');
            background-size: cover;
            background-position: center;
            border: 1px solid #ddd;
        }

        .certificate-content {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            padding: 40px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .certificate-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
        }

        .presented-to {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .student-name {
            font-size: 36px;
            font-weight: bold;
            margin: 15px 0;
            color: #2c3e50;
        }

        .completion-text {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .course-name {
            font-size: 28px;
            font-weight: bold;
            margin: 15px 0;
            color: #2c3e50;
        }

        .completion-date {
            font-size: 16px;
            margin-bottom: 20px;
        }

        .certificate-id {
            font-size: 14px;
            margin-bottom: 30px;
        }

        .signatures {
            display: flex;
            justify-content: space-around;
            margin-top: 40px;
            width: 100%;
        }

        .signature {
            width: 200px;
            text-align: center;
        }

        .signature-name {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .signature-title {
            font-style: italic;
        }

        .verification {
            font-size: 12px;
            margin-top: 30px;
        }

        .action-buttons {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px dashed #ccc;
        }

        .action-buttons .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            padding: 12px 30px;
            border-width: 2px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .action-buttons .btn i {
            margin-right: 8px;
            font-size: 20px;
        }

        /* Primary Button Hover */
        .action-buttons .btn-outline-primary:hover {
            background-color: #0d6efd;
            color: white;
            border-color: #0d6efd;
            transform: translateY(-2px);
        }

        /* Secondary Button Hover */
        .action-buttons .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: white;
            border-color: #6c757d;
            transform: translateY(-2px);
        }

        .header {
            font-family: 'Georgia', serif;
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: center;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Add this to your existing CSS */
        @media print {
            body {
                margin: 0;
                padding: 0;
                background: none;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .certificate-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
                background: none;
                page-break-after: avoid;
                page-break-inside: avoid;
            }

            .certificate-paper {
                width: 100%;
                height: 100%;
                padding-bottom: 0;
                border: none;
                background-image: url('admin/uploads/certificate_template.jpg');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .certificate-content {
                position: relative;
                padding: 2cm;
                height: 100%;
                box-sizing: border-box;
            }

            .action-buttons,
            .container.py-5 h1 {
                display: none !important;
            }

            /* Ensure text colors print properly */
            .certificate-title,
            .student-name,
            .course-name {
                color: #000 !important;
            }

            /* Signature styling for print */
            .signatures {
                display: flex;
                justify-content: space-around;
                margin-top: 40px;
                width: 100%;
            }

            /* Force landscape orientation */
            @page {
                size: A4 landscape;
                margin: 0;
            }
        }

        @media (max-width: 768px) {
            .certificate-title {
                font-size: 22px;
            }

            .student-name,
            .course-name {
                font-size: 24px;
            }

            .signatures {
                flex-direction: column;
                align-items: center;
            }

            .signature {
                margin-bottom: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="certificate-container">
            <h1 class="header text-center mb-4">Course Completion Certificate</h1>

            <div class="certificate-paper">
                <div class="certificate-content">
                    <div class="certificate-title">Certificate of Completion</div>

                    <div class="presented-to">This certificate is presented to</div>

                    <div class="student-name"><?= htmlspecialchars($user['Name']) ?></div>

                    <div class="completion-text">has successfully completed the course</div>

                    <div class="course-name"><?= htmlspecialchars($course['Course_Name']) ?></div>

                    <div class="completion-date">Completed on: <?= htmlspecialchars($completion_date_formatted) ?></div>

                    <div class="certificate-id">Certificate ID: <?= htmlspecialchars($certificate_id) ?></div>

                    <div class="signatures">
                        <div class="signature">
                            <div class="signature-name"><?= htmlspecialchars($teacher['Teacher_Name']) ?></div>
                            <div class="signature-title">Course Instructor</div>
                        </div>

                        <div class="signature">
                            <div class="signature-name">Dr. Maria Susan</div>
                            <div class="signature-title">Head of Education</div>
                        </div>
                    </div>

                    <div class="verification">
                        This certificate can be verified at: https://www.eduittutors.com/verify-certificate
                    </div>
                </div>
            </div>

            <div class="action-buttons mt-4">
                <button onclick="window.print()" class="btn btn-outline-primary btn-lg me-3">
                    <i class="fas fa-print"></i> Print Certificate
                </button>
                <a href="javascript:history.back()" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>

</html>