<!DOCTYPE html>
<html lang="en">

<?php
include('profile_calling_admin.php');
?>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Department</title>
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



    <link rel="stylesheet" href="../../vendors/iconfonts/font-awesome/css/all.min.css">
    <link rel="stylesheet" href="../../vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../../vendors/css/vendor.bundle.addons.css">
    <!-- endinject -->
    <!-- inject:css -->
    <link rel="stylesheet" href="../../css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="../../images/favicon.png" />

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
                            Events
                        </h3>
                    </div>

                    <div class="row">
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Create New Event</h4>
                                    <form class="forms-sample" id="eventForm" enctype="multipart/form-data" method="POST" action="process_event.php">
                                        <div class="form-group">
                                            <label for="eventName">Event Name</label>
                                            <input type="text" class="form-control" id="eventName" name="eventName" placeholder="Enter event name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="eventDescription">Event Description</label>
                                            <textarea class="form-control" id="eventDescription" name="eventDescription" rows="3" placeholder="Enter event description"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="bannerImage">Banner Image</label>
                                            <input type="file" class="form-control-file" id="bannerImage" name="bannerImage" accept="image/*">
                                        </div>
                                        <div class="form-group">
                                            <label for="discountPercentage">Discount Percentage</label>
                                            <input type="number" class="form-control" id="discountPercentage" name="discountPercentage" min="0" max="100" placeholder="Enter discount percentage" required>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="startDatetime">Start Date & Time</label>
                                                    <input type="datetime-local" class="form-control" id="startDatetime" name="startDatetime" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="endDatetime">End Date & Time</label>
                                                    <input type="datetime-local" class="form-control" id="endDatetime" name="endDatetime" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="maxUses">Maximum Uses (Leave empty for unlimited)</label>
                                            <input type="number" class="form-control" id="maxUses" name="maxUses" min="1" placeholder="Enter maximum uses">
                                        </div>
                                        <div class="form-group">
                                            <label for="isActive">Status</label>
                                            <select class="form-control" id="isActive" name="isActive" required>
                                                <option value="1" selected>Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Select Courses for Discount</label>
                                            <div class="row">
                                                <?php
                                                // Fetch courses from database
                                                include('connect.php');
                                                $stmt = $pdo->query("SELECT Course_ID, Course_Name FROM courses");
                                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                    echo '<div class="col-md-4">';
                                                    echo '<div class="form-check">';
                                                    echo '<label class="form-check-label">';
                                                    echo '<input type="checkbox" class="form-check-input" name="selectedCourses[]" value="' . $row['Course_ID'] . '">';
                                                    echo $row['Course_Name'];
                                                    echo '</label>';
                                                    echo '</div>';
                                                    echo '</div>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary mr-2">Submit</button>
                                        <button type="reset" class="btn btn-light">Cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Event List Section -->
                    <div class="row">
                        <div class="col-lg-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Current Events</h4>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Event ID</th>
                                                    <th>Event Name</th>
                                                    <th>Discount</th>
                                                    <th>Start Date</th>
                                                    <th>End Date</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Fetch events from database
                                                $stmt = $pdo->query("SELECT * FROM event_discounts ORDER BY created_at DESC");
                                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                    echo '<tr>';
                                                    echo '<td>' . $row['event_id'] . '</td>';
                                                    echo '<td>' . $row['event_name'] . '</td>';
                                                    echo '<td>' . $row['discount_percentage'] . '%</td>';
                                                    echo '<td>' . date('M d, Y H:i', strtotime($row['start_datetime'])) . '</td>';
                                                    echo '<td>' . date('M d, Y H:i', strtotime($row['end_datetime'])) . '</td>';
                                                    echo '<td>';
                                                    echo $row['is_active'] ? '<label class="badge badge-success">Active</label>' : '<label class="badge badge-danger">Inactive</label>';
                                                    echo '</td>';
                                                    echo '<td>';
                                                    echo '<button class="btn btn-info btn-sm view-event" data-id="' . $row['event_id'] . '">View</button>';
                                                    echo '<button class="btn btn-warning btn-sm edit-event" data-id="' . $row['event_id'] . '">Edit</button>';
                                                    echo '<button class="btn btn-danger btn-sm delete-event" data-id="' . $row['event_id'] . '">Delete</button>';
                                                    echo '</td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Event Details Modal -->
                    <div class="modal fade" id="eventDetailsModal" tabindex="-1" role="dialog" aria-labelledby="eventDetailsModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="eventDetailsModalLabel">Event Details</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body" id="eventDetailsContent">
                                    <!-- Content will be loaded via AJAX -->
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
    <script src=".js/file-upload.js"></script>
    <script src="js/typeahead.js"></script>
    <script src="js/select2.js"></script>

    <!-- JavaScript for Event Management -->
    <script>
        $(document).ready(function() {
            // View Event Details
            $('.view-event').click(function() {
                var eventId = $(this).data('id');
                $.ajax({
                    url: 'get_event_details.php',
                    type: 'POST',
                    data: {
                        event_id: eventId
                    },
                    success: function(response) {
                        $('#eventDetailsContent').html(response);
                        $('#eventDetailsModal').modal('show');
                    }
                });
            });

            // Edit Event (you would implement this similarly)
            $('.edit-event').click(function() {
                var eventId = $(this).data('id');
                // Redirect to edit page or load form via AJAX
                window.location.href = 'edit_event.php?id=' + eventId;
            });

            // Delete Event
            $('.delete-event').click(function() {
                if (confirm('Are you sure you want to delete this event?')) {
                    var eventId = $(this).data('id');
                    var row = $(this).closest('tr');
                    $.ajax({
                        url: 'delete_event.php',
                        type: 'POST',
                        data: {
                            event_id: eventId
                        },
                        success: function(response) {
                            if (response.success) {
                                row.fadeOut(300, function() {
                                    $(this).remove();
                                });
                            } else {
                                alert('Error deleting event: ' + response.message);
                            }
                        }
                    });
                }
            });
        });
    </script>
    <!-- End custom js for this page-->
</body>


</html>