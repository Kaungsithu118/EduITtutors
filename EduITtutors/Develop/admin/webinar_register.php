<?php
include('profile_calling_admin.php');
include('connect.php');

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$where = '';
if (!empty($search)) {
    $where = "WHERE wr.name LIKE :search OR wr.email LIKE :search OR w.title LIKE :search";
}

// Get total records for pagination
$total_stmt = $pdo->prepare("SELECT COUNT(*) FROM webinar_registrations wr JOIN webinars w ON wr.webinar_id = w.webinar_id $where");
if (!empty($search)) {
    $total_stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
$total_stmt->execute();
$total_records = $total_stmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

// Get webinar registrations with pagination
$stmt = $pdo->prepare("
    SELECT wr.*, w.title as webinar_title, w.webinar_date 
    FROM webinar_registrations wr 
    JOIN webinars w ON wr.webinar_id = w.webinar_id 
    $where
    ORDER BY wr.registration_date DESC 
    LIMIT :start, :limit
");

if (!empty($search)) {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle registration deletion
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $delete_stmt = $pdo->prepare("DELETE FROM webinar_registrations WHERE registration_id = ?");
    if ($delete_stmt->execute([$delete_id])) {
        $success_message = "Registration deleted successfully!";
        // Refresh the page to show updated list
        header("Location: webinar_register.php?page=$page&search=$search");
        exit();
    } else {
        $error_message = "Failed to delete registration.";
    }
}
?>
<?php
// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// Handle sending webinar link
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_webinar_link') {
    try {
        // Validate inputs
        if (empty($_POST['webinar_link'])) {
            throw new Exception("Webinar link is required");
        }

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email address");
        }

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
            $mail->Subject = 'Your Webinar Access: ' . $_POST['webinar_title'];

            // HTML Email Template
            $custom_message = !empty($_POST['custom_message']) ? '<p>' . nl2br(htmlspecialchars($_POST['custom_message'])) . '</p>' : '';

            $mail->Body = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Webinar Access</title>
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
                    .webinar-box {
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
                        <h2 style="color: rgb(8, 35, 145); margin-top: 0;">Your Webinar Access: ' . htmlspecialchars($_POST['webinar_title']) . '</h2>
                        <p>Dear ' . htmlspecialchars($_POST['name']) . ',</p>
                        
                        ' . $custom_message . '
                        
                        <div class="webinar-box">
                            <h3 style="margin-top: 0;">Webinar Details</h3>
                            <p><strong>Title:</strong> ' . htmlspecialchars($_POST['webinar_title']) . '</p>
                            <p><strong>Date:</strong> ' . htmlspecialchars($_POST['webinar_date']) . '</p>
                            <p><strong>Link:</strong> <a href="' . htmlspecialchars($_POST['webinar_link']) . '">Click here to join the webinar</a></p>
                            
                            <a href="' . htmlspecialchars($_POST['webinar_link']) . '" class="btn-primary" style="color: white;">Join Webinar Now</a>
                        </div>
                        
                        <p>We look forward to your participation in this exciting event!</p>
                        
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
                        <p>© ' . date('Y') . ' EduITtutors. All rights reserved.</p>
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

            $mail->AltBody = "Dear " . $_POST['name'] . ",\n\n" .
                (!empty($_POST['custom_message']) ? strip_tags($_POST['custom_message']) . "\n\n" : "") .
                "Webinar Details:\n" .
                "Title: " . $_POST['webinar_title'] . "\n" .
                "Date: " . $_POST['webinar_date'] . "\n" .
                "Link: " . $_POST['webinar_link'] . "\n\n" .
                "Best regards,\nThe EduITtutors Team\n\nwww.eduitutors.com\ninfo@eduitutors.com\nPhone: +1 (123) 456-7890";

            $mail->send();

            // Show success message
            $_SESSION['success_message'] = "Webinar link sent successfully to " . $_POST['email'];
            header("Location: webinar_register.php?page=$page&search=$search");
            exit();
        } catch (Exception $e) {
            throw new Exception("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: webinar_register.php?page=$page&search=$search");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Webinar Registrations</title>
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
        .registration-card {
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .registration-header {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-bottom: 1px solid #dee2e6;
            font-weight: bold;
        }

        .registration-body {
            padding: 15px;
        }

        .badge-webinar {
            background-color: #4e73df;
        }

        .search-container {
            margin-bottom: 20px;
        }

        .pagination {
            justify-content: center;
        }

        /* General button spacing inside the table cell */
        td .btn {
            margin: 5px 3px;
            min-width: 100px;
            /* optional for uniform button width */
            white-space: nowrap;
        }

        /* Improve spacing for small screens */
        @media (max-width: 768px) {
            td .btn {
                display: block;
                width: 100%;
                margin-bottom: 5px;
            }
        }

        /* Optional: Make icons align nicely with text */
        td .btn i {
            margin-right: 5px;
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
                            Webinar Registrations
                        </h3>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <?php if (isset($success_message)): ?>
                                <div class="alert alert-success"><?= $success_message ?></div>
                            <?php endif; ?>
                            <?php if (isset($error_message)): ?>
                                <div class="alert alert-danger"><?= $error_message ?></div>
                            <?php endif; ?>

                            <div class="card">
                                <div class="card-body">
                                    <!-- Search Form -->
                                    <div class="search-container">
                                        <form method="GET" class="form-inline">
                                            <div class="form-group mr-2">
                                                <input type="text" name="search" class="form-control" placeholder="Search by name or email" value="<?= htmlspecialchars($search) ?>">
                                            </div>
                                            <button type="submit" class="btn btn-primary">Search</button>
                                            <?php if (!empty($search)): ?>
                                                <a href="webinar_register.php" class="btn btn-secondary ml-2">Clear</a>
                                            <?php endif; ?>
                                        </form>
                                    </div>

                                    <!-- Registrations List -->
                                    <?php if (empty($registrations)): ?>
                                        <div class="alert alert-info">No webinar registrations found.</div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Webinar</th>
                                                        <th>Date</th>
                                                        <th>Country</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($registrations as $reg): ?>
                                                        <tr>
                                                            <td><?= $reg['registration_id'] ?></td>
                                                            <td><?= htmlspecialchars($reg['name']) ?></td>
                                                            <td><?= htmlspecialchars($reg['email']) ?></td>
                                                            <td><?= htmlspecialchars($reg['webinar_title']) ?></td>
                                                            <td><?= date('M j, Y', strtotime($reg['registration_date'])) ?></td>
                                                            <td><?= htmlspecialchars($reg['country']) ?></td>
                                                            <td>
                                                                <button class="btn btn-info btn-sm view-details"
                                                                    data-id="<?= $reg['registration_id'] ?>"
                                                                    data-name="<?= htmlspecialchars($reg['name']) ?>"
                                                                    data-email="<?= htmlspecialchars($reg['email']) ?>"
                                                                    data-webinar="<?= htmlspecialchars($reg['webinar_title']) ?>"
                                                                    data-date="<?= date('M j, Y', strtotime($reg['webinar_date'])) ?>"
                                                                    data-country="<?= htmlspecialchars($reg['country']) ?>"
                                                                    data-qualification="<?= htmlspecialchars($reg['qualification']) ?>"
                                                                    data-phone="<?= htmlspecialchars($reg['phone']) ?>"
                                                                    data-organization="<?= htmlspecialchars($reg['organization']) ?>"
                                                                    data-industry="<?= htmlspecialchars($reg['industry']) ?>"
                                                                    data-regdate="<?= date('M j, Y', strtotime($reg['registration_date'])) ?>">
                                                                    <i class="fas fa-eye"></i> View
                                                                </button>
                                                                <button class="btn btn-success btn-sm send-link"
                                                                    data-id="<?= $reg['registration_id'] ?>"
                                                                    data-email="<?= htmlspecialchars($reg['email']) ?>"
                                                                    data-name="<?= htmlspecialchars($reg['name']) ?>"
                                                                    data-webinar="<?= htmlspecialchars($reg['webinar_title']) ?>"
                                                                    data-date="<?= date('M j, Y', strtotime($reg['webinar_date'])) ?>">
                                                                    <i class="fas fa-paper-plane"></i> Send Link
                                                                </button>
                                                                <a href="webinar_register.php?delete_id=<?= $reg['registration_id'] ?>&page=<?= $page ?>&search=<?= urlencode($search) ?>"
                                                                    class="btn btn-danger btn-sm"
                                                                    onclick="return confirm('Are you sure you want to delete this registration?');">
                                                                    <i class="fas fa-trash"></i> Delete
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Pagination -->
                                        <nav aria-label="Page navigation">
                                            <ul class="pagination">
                                                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                                    <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>" aria-label="Previous">
                                                        <span aria-hidden="true">&laquo;</span>
                                                    </a>
                                                </li>
                                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                                        <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                                                    </li>
                                                <?php endfor; ?>
                                                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                                    <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>" aria-label="Next">
                                                        <span aria-hidden="true">&raquo;</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </nav>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registration Details Modal -->
                <div class="modal fade" id="registrationModal" tabindex="-1" role="dialog" aria-labelledby="registrationModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="registrationModalLabel">Registration Details</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Name:</strong> <span id="detail-name"></span></p>
                                        <p><strong>Email:</strong> <span id="detail-email"></span></p>
                                        <p><strong>Phone:</strong> <span id="detail-phone"></span></p>
                                        <p><strong>Country:</strong> <span id="detail-country"></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Webinar:</strong> <span id="detail-webinar"></span></p>
                                        <p><strong>Webinar Date:</strong> <span id="detail-date"></span></p>
                                        <p><strong>Qualification:</strong> <span id="detail-qualification"></span></p>
                                        <p><strong>Industry:</strong> <span id="detail-industry"></span></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <p><strong>Organization:</strong> <span id="detail-organization"></span></p>
                                        <p><strong>Registration Date:</strong> <span id="detail-regdate"></span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Send Webinar Link Modal -->
                <div class="modal fade" id="sendLinkModal" tabindex="-1" role="dialog" aria-labelledby="sendLinkModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="sendLinkModalLabel">Send Webinar Link</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form id="sendLinkForm" method="POST">
                                <div class="modal-body">
                                    <input type="hidden" name="action" value="send_webinar_link">
                                    <input type="hidden" id="link_registration_id" name="registration_id">
                                    <input type="hidden" id="link_email" name="email">
                                    <input type="hidden" id="link_name" name="name">
                                    <input type="hidden" id="link_webinar" name="webinar_title">
                                    <input type="hidden" id="link_date" name="webinar_date">

                                    <div class="form-group">
                                        <label for="webinar_link">Webinar Link</label>
                                        <input type="url" class="form-control" id="webinar_link" name="webinar_link" required placeholder="https://example.com/webinar">
                                    </div>

                                    <div class="form-group">
                                        <label for="custom_message">Custom Message (Optional)</label>
                                        <textarea class="form-control" id="custom_message" name="custom_message" rows="4" placeholder="Add a personalized message..."></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Send Link</button>
                                </div>
                            </form>
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


    <script>
        // View details modal
        $(document).ready(function() {
            $('.view-details').click(function() {
                $('#detail-name').text($(this).data('name'));
                $('#detail-email').text($(this).data('email'));
                $('#detail-webinar').text($(this).data('webinar'));
                $('#detail-date').text($(this).data('date'));
                $('#detail-country').text($(this).data('country'));
                $('#detail-qualification').text($(this).data('qualification'));
                $('#detail-phone').text($(this).data('phone') || 'N/A');
                $('#detail-organization').text($(this).data('organization') || 'N/A');
                $('#detail-industry').text($(this).data('industry') === 'yes' ? 'Yes' : 'No');
                $('#detail-regdate').text($(this).data('regdate'));

                $('#registrationModal').modal('show');
            });
        });
        // Send webinar link modal
        $(document).ready(function() {
            $('.send-link').click(function() {
                $('#link_registration_id').val($(this).data('id'));
                $('#link_email').val($(this).data('email'));
                $('#link_name').val($(this).data('name'));
                $('#link_webinar').val($(this).data('webinar'));
                $('#link_date').val($(this).data('date'));

                $('#sendLinkModal').modal('show');
            });
        });
    </script>
</body>

</html>