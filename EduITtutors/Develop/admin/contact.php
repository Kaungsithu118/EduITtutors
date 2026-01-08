<?php
include ('profile_calling_admin.php');
include("connect.php");

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            $pdo->beginTransaction();
            
            switch ($_POST['action']) {
                case 'update_status':
                    $stmt = $pdo->prepare("UPDATE contacts SET status = ? WHERE contact_id = ?");
                    $stmt->execute([$_POST['status'], $_POST['contact_id']]);
                    break;
                    
                case 'send_response':
                    // Validate email
                    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                        throw new Exception("Invalid email address");
                    }
                    
                    // Get contact details for personalization
                    $stmt = $pdo->prepare("SELECT name FROM contacts WHERE contact_id = ?");
                    $stmt->execute([$_POST['contact_id']]);
                    $contact = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Update status
                    $stmt = $pdo->prepare("UPDATE contacts SET status = 'responded' WHERE contact_id = ?");
                    $stmt->execute([$_POST['contact_id']]);
                    
                    // Send email using PHPMailer
                    $mail = new PHPMailer(true);
                    
                    try {
                        // Server settings
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'kaungsithuzqja@gmail.com';
                        $mail->Password   = 'uopa ujhs gdev zrge';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587;

                        // Recipients
                        $mail->setFrom('kaungsithuzqja@gmail.com', 'EduITtutors');
                        $mail->addAddress($_POST['email']);
                        
                        // Content
                        $mail->isHTML(true);
                        $mail->Subject = $_POST['subject'];
                        
                        // HTML Email Template
                        $mail->Body = '
                        <!DOCTYPE html>
                        <html lang="en">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>EduITtutors Response</title>
                            <style>
                                body {
                                    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                                    line-height: 1.6;
                                    color: #333;
                                    background-color: #f5f5f5;
                                    margin: 0;
                                    padding: 0;
                                }
                                .email-container {
                                    max-width: 600px;
                                    margin: 0 auto;
                                    background: #ffffff;
                                    border-radius: 8px;
                                    overflow: hidden;
                                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                                }
                                .header {
                                    background-color: rgb(8, 35, 145);
                                    padding: 20px;
                                    text-align: center;
                                }
                                .header img {
                                    max-height: 60px;
                                }
                                .content {
                                    padding: 30px;
                                }
                                .footer {
                                    background-color: #f8f9fa;
                                    padding: 20px;
                                    text-align: center;
                                    font-size: 12px;
                                    color: #666;
                                }
                                .logo-text {
                                    color: #ffffff;
                                    font-size: 24px;
                                    font-weight: bold;
                                    margin-top: 10px;
                                }
                                .message-box {
                                    background-color: #f8f9fa;
                                    border-left: 4px solid rgb(8, 35, 145);
                                    padding: 15px;
                                    margin: 20px 0;
                                    border-radius: 0 4px 4px 0;
                                }
                                .btn-primary {
                                    background-color: rgb(8, 35, 145);
                                    color: white;
                                    padding: 10px 20px;
                                    text-decoration: none;
                                    border-radius: 4px;
                                    display: inline-block;
                                    margin: 10px 0;
                                }
                                .signature {
                                    margin-top: 30px;
                                    border-top: 1px solid #eee;
                                    padding-top: 20px;
                                }
                            </style>
                        </head>
                        <body>
                            <div class="email-container">
                                <div class="header">
                                    <div class="logo-text">EduITtutors</div>
                                    <p style="color: #fff; margin: 5px 0 0;">Empowering Future Tech Leaders</p>
                                </div>
                                
                                <div class="content">
                                    <h2 style="color: rgb(8, 35, 145); margin-top: 0;">'.htmlspecialchars($_POST['subject']).'</h2>
                                    <p>Dear '.htmlspecialchars($contact['name']).',</p>
                                    
                                    <div class="message-box">
                                        '.nl2br(htmlspecialchars($_POST['response_message'])).'
                                    </div>
                                    
                                    <p>Thank you for contacting EduITtutors. We appreciate your interest in our services.</p>
                                    
                                    <div class="signature">
                                        <p>Best regards,</p>
                                        <p><strong>The EduITtutors Team</strong></p>
                                        <p>
                                            <a href="https://www.eduitutors.com" style="color: rgb(8, 35, 145);">www.eduitutors.com</a> | 
                                            <a href="mailto:info@eduitutors.com" style="color: rgb(8, 35, 145);">info@eduitutors.com</a> | 
                                            Phone: +1 (123) 456-7890
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="footer">
                                    <p>© '.date('Y').' EduITtutors. All rights reserved.</p>
                                    <p>
                                        <a href="#" style="color: rgb(8, 35, 145);">Privacy Policy</a> | 
                                        <a href="#" style="color: rgb(8, 35, 145);">Terms of Service</a>
                                    </p>
                                    <p>
                                        123 Education Street, Knowledge City, 10101
                                    </p>
                                </div>
                            </div>
                        </body>
                        </html>
                        ';
                        
                        $mail->AltBody = "Dear ".$contact['name'].",\n\n".strip_tags($_POST['response_message'])."\n\nBest regards,\nThe EduITtutors Team\n\nwww.eduitutors.com\ninfo@eduitutors.com\nPhone: +1 (123) 456-7890";

                        $mail->send();
                    } catch (Exception $e) {
                        throw new Exception("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
                    }
                    break;
                    
                case 'delete_message':
                    $stmt = $pdo->prepare("DELETE FROM contacts WHERE contact_id = ?");
                    $stmt->execute([$_POST['contact_id']]);
                    break;
            }
            
            $pdo->commit();
            header("Location: contact.php?success=1");
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            header("Location: contact.php?error=" . urlencode($e->getMessage()));
            exit();
        }
    }
}

// Get filter status
$filter_status = isset($_GET['status']) ? $_GET['status'] : 'all';

// Build query
$query = "SELECT * FROM contacts";
$params = [];

if ($filter_status !== 'all') {
    $query .= " WHERE status = ?";
    $params[] = $filter_status;
}

$query .= " ORDER BY submission_date DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count messages by status
$status_counts = [
    'all' => 0,
    'unread' => 0,
    'read' => 0,
    'responded' => 0
];

$count_stmt = $pdo->query("SELECT status, COUNT(*) as count FROM contacts GROUP BY status");
$counts = $count_stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($counts as $count) {
    $status_counts[$count['status']] = $count['count'];
}

$status_counts['all'] = array_sum($status_counts);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Contact Messages</title>
    <!-- plugins:css -->
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
        .message-card {
            border-left: 4px solid #4B49AC;
            margin-bottom: 1rem;
            transition: all 0.3s;
        }
        
        .message-card.unread {
            border-left-color: #FF4747;
            background-color: #f8f9fa;
        }
        
        .message-card:hover {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .status-unread {
            background-color: #FFE5E5;
            color: #FF4747;
        }
        
        .status-read {
            background-color: #E5F6FF;
            color: #4B49AC;
        }
        
        .status-responded {
            background-color: #E5FFE7;
            color: #28A745;
        }
        
        .filter-btn {
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        .response-form {
            display: none;
            margin-top: 1rem;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 0.25rem;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
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
                            Contact Messages
                        </h3>
                    </div>
                    
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success">
                            Action completed successfully!
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($_GET['error']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-12 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Manage Messages</h4>
                                    
                                    <div class="mb-4">
                                        <h5>Filter by status:</h5>
                                        <a href="contact.php?status=all" class="btn btn-outline-primary filter-btn <?php echo $filter_status === 'all' ? 'active' : ''; ?>">
                                            All <span class="badge badge-light"><?php echo $status_counts['all']; ?></span>
                                        </a>
                                        <a href="contact.php?status=unread" class="btn btn-outline-danger filter-btn <?php echo $filter_status === 'unread' ? 'active' : ''; ?>">
                                            Unread <span class="badge badge-light"><?php echo $status_counts['unread']; ?></span>
                                        </a>
                                        <a href="contact.php?status=read" class="btn btn-outline-info filter-btn <?php echo $filter_status === 'read' ? 'active' : ''; ?>">
                                            Read <span class="badge badge-light"><?php echo $status_counts['read']; ?></span>
                                        </a>
                                        <a href="contact.php?status=responded" class="btn btn-outline-success filter-btn <?php echo $filter_status === 'responded' ? 'active' : ''; ?>">
                                            Responded <span class="badge badge-light"><?php echo $status_counts['responded']; ?></span>
                                        </a>
                                    </div>
                                    
                                    <?php if (empty($contacts)): ?>
                                        <div class="alert alert-info">
                                            No messages found with the selected filter.
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <?php foreach ($contacts as $contact): ?>
                                                <div class="card message-card <?php echo $contact['status'] === 'unread' ? 'unread' : ''; ?>">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div>
                                                                <h5><?php echo htmlspecialchars($contact['name']); ?></h5>
                                                                <p class="text-muted mb-1"><?php echo htmlspecialchars($contact['email']); ?></p>
                                                                <p class="text-muted"><?php echo date('M j, Y g:i A', strtotime($contact['submission_date'])); ?></p>
                                                            </div>
                                                            <span class="status-badge status-<?php echo $contact['status']; ?>">
                                                                <?php echo ucfirst($contact['status']); ?>
                                                            </span>
                                                        </div>
                                                        
                                                        <div class="mt-3">
                                                            <p><?php echo nl2br(htmlspecialchars($contact['message'])); ?></p>
                                                        </div>
                                                        
                                                        <div class="mt-3 action-buttons">
                                                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                                                <input type="hidden" name="contact_id" value="<?php echo $contact['contact_id']; ?>">
                                                                <input type="hidden" name="action" value="delete_message">
                                                                <button type="submit" class="btn btn-sm btn-danger">
                                                                    <i class="fas fa-trash"></i> Delete
                                                                </button>
                                                            </form>
                                                            <form method="POST" class="mr-2">
                                                                <input type="hidden" name="contact_id" value="<?php echo $contact['contact_id']; ?>">
                                                                <input type="hidden" name="action" value="update_status">
                                                                <?php if ($contact['status'] !== 'responded'): ?>
                                                                    <?php if ($contact['status'] !== 'read'): ?>
                                                                        <input type="hidden" name="status" value="read">
                                                                        <button type="submit" class="btn btn-sm btn-info">
                                                                            <i class="fas fa-eye"></i> Mark as Read
                                                                        </button>
                                                                    <?php else: ?>
                                                                        <input type="hidden" name="status" value="unread">
                                                                        <button type="submit" class="btn btn-sm btn-secondary">
                                                                            <i class="fas fa-eye-slash"></i> Mark as Unread
                                                                        </button>
                                                                    <?php endif; ?>
                                                                <?php endif; ?>
                                                            </form>
                                                            
                                                            <?php if ($contact['status'] !== 'responded'): ?>
                                                                <?php if ($contact['status'] == 'read'): ?>
                                                                    <button class="btn btn-sm btn-primary toggle-response" data-contact-id="<?php echo $contact['contact_id']; ?>">
                                                                        <i class="fas fa-reply"></i> Respond
                                                                    </button>
                                                                <?php endif; ?>
                                                            <?php endif; ?>
                                                        </div>
                                                        
                                                        <?php if ($contact['status'] !== 'responded'): ?>
                                                            <div class="response-form" id="response-form-<?php echo $contact['contact_id']; ?>">
                                                                <form method="POST">
                                                                    <input type="hidden" name="contact_id" value="<?php echo $contact['contact_id']; ?>">
                                                                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($contact['email']); ?>">
                                                                    <input type="hidden" name="action" value="send_response">
                                                                    
                                                                    <div class="form-group">
                                                                        <label for="subject-<?php echo $contact['contact_id']; ?>">Subject</label>
                                                                        <input type="text" class="form-control" id="subject-<?php echo $contact['contact_id']; ?>" name="subject" required>
                                                                    </div>
                                                                    
                                                                    <div class="form-group">
                                                                        <label for="message-<?php echo $contact['contact_id']; ?>">Response Message</label>
                                                                        <textarea class="form-control" id="message-<?php echo $contact['contact_id']; ?>" name="response_message" rows="5" required></textarea>
                                                                    </div>
                                                                    
                                                                    <button type="submit" class="btn btn-success">
                                                                        <i class="fas fa-paper-plane"></i> Send Response
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- content-wrapper ends -->
                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2023 EduITtutors. All rights reserved.</span>
                        <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Hand-crafted & made with <i class="far fa-heart text-danger"></i></span>
                    </div>
                </footer>
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
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle response forms
            document.querySelectorAll('.toggle-response').forEach(button => {
                button.addEventListener('click', function() {
                    const contactId = this.getAttribute('data-contact-id');
                    const form = document.getElementById(`response-form-${contactId}`);
                    
                    if (form.style.display === 'block') {
                        form.style.display = 'none';
                        this.innerHTML = '<i class="fas fa-reply"></i> Respond';
                    } else {
                        form.style.display = 'block';
                        this.innerHTML = '<i class="fas fa-times"></i> Cancel';
                    }
                });
            });
            
            // Auto-focus the first form field when response form is shown
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('toggle-response')) {
                    const contactId = e.target.getAttribute('data-contact-id');
                    const form = document.getElementById(`response-form-${contactId}`);
                    if (form.style.display === 'block') {
                        form.querySelector('input[type="text"]').focus();
                    }
                }
            });
        });
    </script>
</body>
</html>