<?php
// edit_event.php
include('connect.php');

// Check if event ID is provided
if (!isset($_GET['id'])) {
    header("Location: event.php");
    exit();
}

$event_id = $_GET['id'];

// Fetch event details
$stmt = $pdo->prepare("SELECT * FROM event_discounts WHERE event_id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    header("Location: event.php");
    exit();
}

// Fetch associated courses
$stmt = $pdo->prepare("SELECT course_id FROM event_discount_courses WHERE event_id = ?");
$stmt->execute([$event_id]);
$selected_courses = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        
        // Update event details
        $stmt = $pdo->prepare("UPDATE event_discounts SET 
            event_name = ?, 
            event_description = ?, 
            discount_percentage = ?, 
            start_datetime = ?, 
            end_datetime = ?, 
            max_uses = ?, 
            is_active = ? 
            WHERE event_id = ?");
        
        $stmt->execute([
            $_POST['eventName'],
            $_POST['eventDescription'],
            $_POST['discountPercentage'],
            $_POST['startDatetime'],
            $_POST['endDatetime'],
            empty($_POST['maxUses']) ? NULL : $_POST['maxUses'],
            $_POST['isActive'],
            $event_id
        ]);
        
        // Handle banner image upload
        if (!empty($_FILES['bannerImage']['name'])) {
            $target_dir = "uploads/events/";
            $target_file = $target_dir . basename($_FILES["bannerImage"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            
            // Check if image file is a actual image
            $check = getimagesize($_FILES["bannerImage"]["tmp_name"]);
            if ($check !== false) {
                // Generate unique filename
                $new_filename = uniqid() . '.' . $imageFileType;
                $target_path = $target_dir . $new_filename;
                
                if (move_uploaded_file($_FILES["bannerImage"]["tmp_name"], $target_path)) {
                    // Delete old banner if exists
                    if (!empty($event['banner_image'])) {
                        @unlink($event['banner_image']);
                    }
                    
                    // Update banner path in database
                    $stmt = $pdo->prepare("UPDATE event_discounts SET banner_image = ? WHERE event_id = ?");
                    $stmt->execute([$target_path, $event_id]);
                }
            }
        }
        
        // Update selected courses
        $pdo->prepare("DELETE FROM event_discount_courses WHERE event_id = ?")->execute([$event_id]);
        
        if (!empty($_POST['selectedCourses'])) {
            $stmt = $pdo->prepare("INSERT INTO event_discount_courses (event_id, course_id) VALUES (?, ?)");
            foreach ($_POST['selectedCourses'] as $course_id) {
                $stmt->execute([$event_id, $course_id]);
            }
        }
        
        $pdo->commit();
        $_SESSION['success_message'] = "Event updated successfully!";
        header("Location: event.php");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_message = "Error updating event: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<?php
include('profile_calling_admin.php');
?>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Edit Event</title>
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
                            Edit Event
                        </h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="event.php">Events</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Event</li>
                            </ol>
                        </nav>
                    </div>

                    <div class="row">
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <?php if (isset($error_message)): ?>
                                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                                    <?php endif; ?>
                                    
                                    <h4 class="card-title">Edit Event: <?php echo htmlspecialchars($event['event_name']); ?></h4>
                                    <form class="forms-sample" id="eventForm" enctype="multipart/form-data" method="POST" action="">
                                        <div class="form-group">
                                            <label for="eventName">Event Name</label>
                                            <input type="text" class="form-control" id="eventName" name="eventName" 
                                                placeholder="Enter event name" value="<?php echo htmlspecialchars($event['event_name']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="eventDescription">Event Description</label>
                                            <textarea class="form-control" id="eventDescription" name="eventDescription" 
                                                rows="3" placeholder="Enter event description"><?php echo htmlspecialchars($event['event_description']); ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="bannerImage">Banner Image</label>
                                            <?php if (!empty($event['banner_image'])): ?>
                                                <div class="mb-2">
                                                    <img src="<?php echo $event['banner_image']; ?>" style="max-height: 150px;" class="img-thumbnail">
                                                    <input type="hidden" name="current_banner" value="<?php echo $event['banner_image']; ?>">
                                                </div>
                                            <?php endif; ?>
                                            <input type="file" class="form-control-file" id="bannerImage" name="bannerImage" accept="image/*">
                                            <small class="text-muted">Leave blank to keep current image</small>
                                        </div>
                                        <div class="form-group">
                                            <label for="discountPercentage">Discount Percentage</label>
                                            <input type="number" class="form-control" id="discountPercentage" name="discountPercentage" 
                                                min="0" max="100" placeholder="Enter discount percentage" 
                                                value="<?php echo htmlspecialchars($event['discount_percentage']); ?>" required>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="startDatetime">Start Date & Time</label>
                                                    <input type="datetime-local" class="form-control" id="startDatetime" name="startDatetime" 
                                                        value="<?php echo date('Y-m-d\TH:i', strtotime($event['start_datetime'])); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="endDatetime">End Date & Time</label>
                                                    <input type="datetime-local" class="form-control" id="endDatetime" name="endDatetime" 
                                                        value="<?php echo date('Y-m-d\TH:i', strtotime($event['end_datetime'])); ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="maxUses">Maximum Uses (Leave empty for unlimited)</label>
                                            <input type="number" class="form-control" id="maxUses" name="maxUses" min="1" 
                                                placeholder="Enter maximum uses" value="<?php echo htmlspecialchars($event['max_uses']); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="isActive">Status</label>
                                            <select class="form-control" id="isActive" name="isActive" required>
                                                <option value="1" <?php echo $event['is_active'] ? 'selected' : ''; ?>>Active</option>
                                                <option value="0" <?php echo !$event['is_active'] ? 'selected' : ''; ?>>Inactive</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Select Courses for Discount</label>
                                            <div class="row">
                                                <?php
                                                // Fetch all courses from database
                                                $stmt = $pdo->query("SELECT Course_ID, Course_Name FROM courses");
                                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                    echo '<div class="col-md-4">';
                                                    echo '<div class="form-check">';
                                                    echo '<label class="form-check-label">';
                                                    echo '<input type="checkbox" class="form-check-input" name="selectedCourses[]" value="' . $row['Course_ID'] . '"';
                                                    echo in_array($row['Course_ID'], $selected_courses) ? ' checked' : '';
                                                    echo '>';
                                                    echo $row['Course_Name'];
                                                    echo '</label>';
                                                    echo '</div>';
                                                    echo '</div>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary mr-2">Update Event</button>
                                        <a href="event.php" class="btn btn-light">Cancel</a>
                                    </form>
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
    <script src="js/file-upload.js"></script>
    <script src="js/typeahead.js"></script>
    <script src="js/select2.js"></script>
    
    <script>
        // Form validation
        $(document).ready(function() {
            $('#eventForm').on('submit', function(e) {
                // Validate end date is after start date
                var startDate = new Date($('#startDatetime').val());
                var endDate = new Date($('#endDatetime').val());
                
                if (endDate <= startDate) {
                    alert('End date must be after start date');
                    e.preventDefault();
                    return false;
                }
                
                return true;
            });
        });
    </script>
</body>
</html>