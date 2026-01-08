<!DOCTYPE html>
<html lang="en">

<?php
include('profile_calling_admin.php');
include('connect.php'); // Include your database connection
?>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Orders Management</title>
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
                            Orders Management
                        </h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Orders</li>
                            </ol>
                        </nav>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">All Orders</h4>
                                    <div class="table-responsive">
                                        <table id="ordersTable" class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th>User</th>
                                                    <th>Order Date</th>
                                                    <th>Total Amount</th>
                                                    <th>Payment Method</th>
                                                    <th>Status</th>
                                                    <th>Invoice</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Fetch all orders with user information
                                                $stmt = $pdo->prepare("
                                                    SELECT o.*, u.Name as UserName 
                                                    FROM orders o
                                                    JOIN user u ON o.User_ID = u.User_ID
                                                    ORDER BY o.Order_Date DESC
                                                ");
                                                $stmt->execute();
                                                $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                foreach ($orders as $order) {
                                                    $statusClass = '';
                                                    if ($order['Order_Status'] == 'Completed') {
                                                        $statusClass = 'badge-success';
                                                    } elseif ($order['Order_Status'] == 'Pending') {
                                                        $statusClass = 'badge-warning';
                                                    } else {
                                                        $statusClass = 'badge-danger';
                                                    }

                                                    echo '<tr>
                                                        <td>' . $order['Order_ID'] . '</td>
                                                        <td>' . $order['UserName'] . '</td>
                                                        <td>' . date('M d, Y h:i A', strtotime($order['Order_Date'])) . '</td>
                                                        <td>$' . number_format($order['Total'], 2) . '</td>
                                                        <td>' . $order['Payment_Method'] . '</td>
                                                        <td><label class="badge ' . $statusClass . '">' . $order['Order_Status'] . '</label></td>
                                                        <td>' . $order['Invoice_Number'] . '</td>
                                                        <td>
                                                            <button class="btn btn-primary btn-sm view-order" data-id="' . $order['Order_ID'] . '">View</button>
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
                                            <h5>Billing Information</h5>
                                            <div id="billingInfo"></div>
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
                                    <button type="button" class="btn btn-info" id="editOrderStatus" data-order-id="">Edit Status</button>
                                    <button type="button" class="btn btn-danger" id="deleteOrder" data-order-id="">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Edit Status Modal -->
                    <div class="modal fade" id="editStatusModal" tabindex="-1" role="dialog" aria-labelledby="editStatusModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editStatusModalLabel">Edit Order Status</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="statusForm">
                                        <input type="hidden" id="editOrderId" name="order_id">
                                        <div class="form-group">
                                            <label for="orderStatus">Order Status</label>
                                            <select class="form-control" id="orderStatus" name="order_status">
                                                <option value="Pending">Pending</option>
                                                <option value="Completed">Completed</option>
                                                <option value="Cancelled">Cancelled</option>
                                            </select>
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
            $('#ordersTable').DataTable({
                "order": [
                    [0, "desc"]
                ]
            });

            // View order details
            $('.view-order').click(function() {
                var orderId = $(this).data('id');
                $('#printInvoice').data('order-id', orderId);
                $('#editOrderStatus').data('order-id', orderId);
                $('#deleteOrder').data('order-id', orderId);

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
                                <p><strong>User:</strong> ${order.UserName}</p>
                                <p><strong>Order Date:</strong> ${new Date(order.Order_Date).toLocaleString()}</p>
                                <p><strong>Subtotal:</strong> $${parseFloat(order.Subtotal).toFixed(2)}</p>
                                <p><strong>Tax:</strong> $${parseFloat(order.Tax).toFixed(2)}</p>
                                <p><strong>Total:</strong> $${parseFloat(order.Total).toFixed(2)}</p>
                                <p><strong>Payment Method:</strong> ${order.Payment_Method}</p>
                                <p><strong>Status:</strong> ${order.Order_Status}</p>
                                <p><strong>Invoice:</strong> ${order.Invoice_Number}</p>
                            `;
                            $('#orderInfo').html(orderInfoHtml);

                            // Populate billing info
                            var billingInfoHtml = `
                                <p><strong>Name:</strong> ${order.Billing_Name || 'N/A'}</p>
                                <p><strong>Address:</strong> ${order.Billing_Address || 'N/A'}</p>
                                <p><strong>City:</strong> ${order.Billing_City || 'N/A'}</p>
                                <p><strong>Country:</strong> ${order.Billing_Country || 'N/A'}</p>
                                <p><strong>ZIP:</strong> ${order.Billing_Zip || 'N/A'}</p>
                            `;
                            $('#billingInfo').html(billingInfoHtml);

                            // Populate order items
                            var itemsHtml = '';
                            response.items.forEach(function(item) {
                                var statusClass = '';
                                if (item.Access_Status == 'Active') {
                                    statusClass = 'badge-success';
                                } else if (item.Access_Status == 'Pending') {
                                    statusClass = 'badge-warning';
                                } else {
                                    statusClass = 'badge-danger';
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
            
            // Edit order status button click
            $(document).on('click', '#editOrderStatus', function() {
                var orderId = $(this).data('order-id');
                $('#editOrderId').val(orderId);
                
                // Fetch current status
                $.ajax({
                    url: 'get_order_status.php',
                    type: 'POST',
                    data: { order_id: orderId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#orderStatus').val(response.order_status);
                            $('#editStatusModal').modal('show');
                        } else {
                            Swal.fire('Error', 'Failed to fetch order status', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Error connecting to server', 'error');
                    }
                });
            });
            
            // Save status changes
            $(document).on('click', '#saveStatus', function() {
                var orderId = $('#editOrderId').val();
                var newStatus = $('#orderStatus').val();
                
                $.ajax({
                    url: 'update_order_status.php',
                    type: 'POST',
                    data: { 
                        order_id: orderId,
                        order_status: newStatus 
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'success') {
                            Swal.fire('Success', 'Order status updated successfully', 'success');
                            $('#editStatusModal').modal('hide');
                            $('#orderDetailsModal').modal('hide');
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
            
            // Delete order button click
            $(document).on('click', '#deleteOrder', function() {
                var orderId = $(this).data('order-id');
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'delete_order.php',
                            type: 'POST',
                            data: { order_id: orderId },
                            dataType: 'json',
                            success: function(response) {
                                if (response.status == 'success') {
                                    Swal.fire(
                                        'Deleted!',
                                        'The order has been deleted.',
                                        'success'
                                    );
                                    $('#orderDetailsModal').modal('hide');
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
                    }
                });
            });
        });
    </script>

</body>

</html>