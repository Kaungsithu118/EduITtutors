<?php
include('profile_calling_admin.php');
include('connect.php');

// First, update any overdue courses
$updateStmt = $pdo->prepare("
    UPDATE order_items 
    SET Access_Status = 'Overdue' 
    WHERE Access_Status = 'Active' AND End_Date < CURDATE()
");
$updateStmt->execute();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Course Orders & Deadlines</title>
    <!-- plugins:css -->
    <link rel="icon" type="image/png" href="../photo/logo/EduITtutors_Colorver_Logo (1).png" style="width: 250px; height: auto;">
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
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
                            Course Orders & Deadlines
                        </h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Course Orders & Deadlines</li>
                            </ol>
                        </nav>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">All Course Orders</h4>
                                    <div class="table-responsive">
                                        <table id="courseOrdersTable" class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th>User</th>
                                                    <th>Course</th>
                                                    <th>Instructor</th>
                                                    <th>Price</th>
                                                    <th>Start Date</th>
                                                    <th>End Date</th>
                                                    <th>Days Remaining</th>
                                                    <th>Status</th>
                                                    <th>View</th>
                                                    <th>Update</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Fetch all order items with course and user information
                                                $stmt = $pdo->prepare("
                                                    SELECT 
                                                        oi.Order_Item_ID,
                                                        oi.Order_ID,
                                                        oi.Course_ID,
                                                        oi.Course_Name,
                                                        oi.Teacher_Name,
                                                        oi.Price,
                                                        oi.Start_Date,
                                                        oi.End_Date,
                                                        oi.Access_Status,
                                                        u.Name as UserName,
                                                        u.User_ID,
                                                        o.Order_Date,
                                                        o.Order_Status
                                                    FROM order_items oi
                                                    JOIN orders o ON oi.Order_ID = o.Order_ID
                                                    JOIN user u ON o.User_ID = u.User_ID
                                                    ORDER BY oi.End_Date ASC
                                                ");
                                                $stmt->execute();
                                                $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                foreach ($orderItems as $item) {
                                                    // Calculate days remaining
                                                    $endDate = new DateTime($item['End_Date']);
                                                    $today = new DateTime();
                                                    $interval = $today->diff($endDate);
                                                    $daysRemaining = $interval->format('%r%a');
                                                    
                                                    // Determine status and badge class
                                                    $status = $item['Access_Status'];
                                                    $badgeClass = '';
                                                    $statusText = $status;
                                                    
                                                    if ($status == 'Active') {
                                                        if ($daysRemaining > 0) {
                                                            $statusText = 'Active (' . $daysRemaining . ' days left)';
                                                            $badgeClass = 'badge-success';
                                                        } else {
                                                            $statusText = 'Overdue';
                                                            $badgeClass = 'badge-danger';
                                                        }
                                                    } elseif ($status == 'Pending') {
                                                        $badgeClass = 'badge-warning';
                                                    } elseif ($status == 'Completed') {
                                                        $badgeClass = 'badge-info';
                                                    } elseif ($status == 'Overdue') {
                                                        $badgeClass = 'badge-danger';
                                                    } else {
                                                        $badgeClass = 'badge-secondary';
                                                    }

                                                    echo '<tr>
                                                        <td>' . $item['Order_ID'] . '</td>
                                                        <td>' . $item['UserName'] . '</td>
                                                        <td>' . $item['Course_Name'] . '</td>
                                                        <td>' . $item['Teacher_Name'] . '</td>
                                                        <td>$' . number_format($item['Price'], 2) . '</td>
                                                        <td>' . date('M d, Y', strtotime($item['Start_Date'])) . '</td>
                                                        <td>' . date('M d, Y', strtotime($item['End_Date'])) . '</td>
                                                        <td>' . ($daysRemaining > 0 ? $daysRemaining : '0') . '</td>
                                                        <td><label class="badge ' . $badgeClass . '">' . $statusText . '</label></td>
                                                        <td>
                                                            <button class="btn btn-primary view-order-details" data-id="' . $item['Order_ID'] . '">View Order</button>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-info update-status" data-item-id="' . $item['Order_Item_ID'] . '" data-current-status="' . $item['Access_Status'] . '">Update Status</button>
                                                        </td>
                                                    </tr>';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Details Modal -->
                    <div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Order Information</h5>
                                            <div id="orderInfo"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>User Information</h5>
                                            <div id="userInfo"></div>
                                        </div>
                                    </div>
                                    <hr>
                                    <h5>Order Items</h5>
                                    <div class="table-responsive">
                                        <table class="table" id="orderItemsTable">
                                            <thead>
                                                <tr>
                                                    <th>Course</th>
                                                    <th>Instructor</th>
                                                    <th>Price</th>
                                                    <th>Start Date</th>
                                                    <th>End Date</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="orderItemsBody">
                                                <!-- Will be populated by AJAX -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" id="printInvoice" data-order-id="">Print Invoice</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Update Status Modal -->
                    <div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="updateStatusModalLabel">Update Course Access Status</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="statusForm">
                                        <input type="hidden" id="orderItemId" name="order_item_id">
                                        <div class="form-group">
                                            <label for="accessStatus">Access Status</label>
                                            <select class="form-control" id="accessStatus" name="access_status">
                                                <option value="Pending">Pending</option>
                                                <option value="Active">Active</option>
                                                <option value="Completed">Completed</option>
                                                <option value="Cancelled">Cancelled</option>
                                                <option value="Overdue" disabled>Overdue (automatically set)</option>
                                            </select>
                                        </div>
                                        <div class="form-group" id="dateFields">
                                            <label for="startDate">Start Date</label>
                                            <input type="date" class="form-control" id="startDate" name="start_date">
                                            <label for="endDate">End Date</label>
                                            <input type="date" class="form-control" id="endDate" name="end_date">
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" id="saveStatus">Save Changes</button>
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
    <!-- DataTables JS -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#courseOrdersTable').DataTable({
                "order": [[6, "asc"]] // Sort by End Date by default
            });

            // View order details
            $(document).on('click', '.view-order-details', function() {
                var orderId = $(this).data('id');
                $('#printInvoice').data('order-id', orderId);

                // Fetch order details via AJAX
                $.ajax({
                    url: 'get_order_details.php',
                    type: 'POST',
                    data: {
                        order_id: orderId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'success') {
                            // Populate order info
                            var order = response.order;
                            var orderInfoHtml = `
                                <p><strong>Order ID:</strong> ${order.Order_ID}</p>
                                <p><strong>Order Date:</strong> ${new Date(order.Order_Date).toLocaleString()}</p>
                                <p><strong>Subtotal:</strong> $${parseFloat(order.Subtotal).toFixed(2)}</p>
                                <p><strong>Tax:</strong> $${parseFloat(order.Tax).toFixed(2)}</p>
                                <p><strong>Total:</strong> $${parseFloat(order.Total).toFixed(2)}</p>
                                <p><strong>Payment Method:</strong> ${order.Payment_Method}</p>
                                <p><strong>Status:</strong> ${order.Order_Status}</p>
                                <p><strong>Invoice:</strong> ${order.Invoice_Number}</p>
                            `;
                            $('#orderInfo').html(orderInfoHtml);

                            // Populate user info
                            var userInfoHtml = `
                                <p><strong>User ID:</strong> ${order.User_ID}</p>
                                <p><strong>Name:</strong> ${order.UserName}</p>
                                <p><strong>Email:</strong> ${order.Email || 'N/A'}</p>
                                <p><strong>Phone:</strong> ${order.Phone || 'N/A'}</p>
                            `;
                            $('#userInfo').html(userInfoHtml);

                            // Populate order items
                            var itemsHtml = '';
                            response.items.forEach(function(item) {
                                var statusClass = '';
                                if (item.Access_Status == 'Active') {
                                    statusClass = 'badge-success';
                                } else if (item.Access_Status == 'Pending') {
                                    statusClass = 'badge-warning';
                                } else if (item.Access_Status == 'Completed') {
                                    statusClass = 'badge-info';
                                } else if (item.Access_Status == 'Overdue') {
                                    statusClass = 'badge-danger';
                                } else {
                                    statusClass = 'badge-secondary';
                                }

                                itemsHtml += `
                                    <tr>
                                        <td>${item.Course_Name}</td>
                                        <td>${item.Teacher_Name}</td>
                                        <td>$${parseFloat(item.Price).toFixed(2)}</td>
                                        <td>${item.Start_Date}</td>
                                        <td>${item.End_Date}</td>
                                        <td><span class="badge ${statusClass}">${item.Access_Status}</span></td>
                                    </tr>
                                `;
                            });
                            $('#orderItemsBody').html(itemsHtml);

                            // Show modal
                            $('#orderDetailsModal').modal('show');
                        } else {
                            alert('Error loading order details');
                        }
                    },
                    error: function() {
                        alert('Error connecting to server');
                    }
                });
            });

            // Update status button click
            $(document).on('click', '.update-status', function() {
                var orderItemId = $(this).data('item-id');
                var currentStatus = $(this).data('current-status');
                
                $('#orderItemId').val(orderItemId);
                $('#accessStatus').val(currentStatus);
                
                // Disable status change if overdue
                if (currentStatus == 'Overdue') {
                    $('#accessStatus').prop('disabled', true);
                    $('#dateFields').hide();
                    $('#saveStatus').prop('disabled', true);
                } else {
                    $('#accessStatus').prop('disabled', false);
                    $('#saveStatus').prop('disabled', false);
                }
                
                // Fetch current dates for this order item
                $.ajax({
                    url: 'get_order_item_details.php',
                    type: 'POST',
                    data: { order_item_id: orderItemId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#startDate').val(response.start_date);
                            $('#endDate').val(response.end_date);
                            
                            // Disable date editing if status is overdue
                            if (currentStatus == 'Overdue') {
                                $('#startDate').prop('disabled', true);
                                $('#endDate').prop('disabled', true);
                            } else {
                                $('#startDate').prop('disabled', false);
                                $('#endDate').prop('disabled', false);
                            }
                            
                            // Show/hide date fields based on status
                            if ($('#accessStatus').val() == 'Active') {
                                $('#dateFields').show();
                            } else {
                                $('#dateFields').hide();
                            }
                            
                            $('#updateStatusModal').modal('show');
                        } else {
                            Swal.fire('Error', 'Failed to fetch order item details', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Error connecting to server', 'error');
                    }
                });
            });

            // Show/hide date fields based on status selection
            $('#accessStatus').change(function() {
                if ($(this).val() == 'Active') {
                    $('#dateFields').show();
                } else {
                    $('#dateFields').hide();
                }
            });

            // Save status changes
            $(document).on('click', '#saveStatus', function() {
                var orderItemId = $('#orderItemId').val();
                var newStatus = $('#accessStatus').val();
                var startDate = $('#startDate').val();
                var endDate = $('#endDate').val();
                
                // Validate dates if status is Active
                if (newStatus == 'Active') {
                    if (!startDate || !endDate) {
                        Swal.fire('Error', 'Please provide both start and end dates for active courses', 'error');
                        return;
                    }
                    
                    if (new Date(startDate) > new Date(endDate)) {
                        Swal.fire('Error', 'End date must be after start date', 'error');
                        return;
                    }
                }
                
                $.ajax({
                    url: 'update_order_item_status.php',
                    type: 'POST',
                    data: { 
                        order_item_id: orderItemId,
                        access_status: newStatus,
                        start_date: startDate,
                        end_date: endDate
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'success') {
                            Swal.fire('Success', 'Course access status updated successfully', 'success');
                            $('#updateStatusModal').modal('hide');
                            // Refresh the page to see changes
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Error connecting to server', 'error');
                    }
                });
            });

            // Print invoice
            $(document).on('click', '#printInvoice', function() {
                var orderId = $(this).data('order-id');

                if (!orderId) {
                    alert('Order ID missing. Please view the order again.');
                    return;
                }

                // Create a temporary form to submit
                var form = $('<form>', {
                    method: 'GET',
                    action: 'print_invoice.php',
                    target: '_blank'
                }).append($('<input>', {
                    type: 'hidden',
                    name: 'order_id',
                    value: orderId
                }));

                $('body').append(form);
                form.submit();
                form.remove();
            });
        });
    </script>
</body>
</html>