<!DOCTYPE html>
<html lang="en">

<?php
include('profile_calling_admin.php');
?>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard</title>
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

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                            Dashboard
                        </h3>
                    </div>

                    <!-- Dashboard Content -->
                    <div class="row">
                        <!-- Summary Cards -->
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="card-title text-white">Total Revenue</h4>
                                            <?php
                                            $stmt = $pdo->query("SELECT SUM(Total) as total FROM orders WHERE Order_Status = 'Completed'");
                                            $totalRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                                            ?>
                                            <h2 class="mb-0">$<?php echo number_format($totalRevenue, 2); ?></h2>
                                        </div>
                                        <i class="fas fa-dollar-sign fa-3x"></i>
                                    </div>
                                    <p class="mt-3 mb-0">All-time earnings</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="card-title text-white">Total Students</h4>
                                            <?php
                                            $stmt = $pdo->query("SELECT COUNT(*) as total FROM user WHERE Role = 'User'");
                                            $totalStudents = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                                            ?>
                                            <h2 class="mb-0"><?php echo number_format($totalStudents); ?></h2>
                                        </div>
                                        <i class="fas fa-users fa-3x"></i>
                                    </div>
                                    <p class="mt-3 mb-0">Registered learners</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="card-title">Total Courses</h4>
                                            <?php
                                            $stmt = $pdo->query("SELECT COUNT(*) as total FROM courses");
                                            $totalCourses = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                                            ?>
                                            <h2 class="mb-0"><?php echo number_format($totalCourses); ?></h2>
                                        </div>
                                        <i class="fas fa-book fa-3x"></i>
                                    </div>
                                    <p class="mt-3 mb-0">Available courses</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 grid-margin stretch-card">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="card-title">Active Orders</h4>
                                            <?php
                                            $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE Order_Status = 'Completed'");
                                            $totalOrders = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                                            ?>
                                            <h2 class="mb-0"><?php echo number_format($totalOrders); ?></h2>
                                        </div>
                                        <i class="fas fa-shopping-cart fa-3x"></i>
                                    </div>
                                    <p class="mt-3 mb-0">Completed purchases</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Main Charts Row -->
                    <div class="row">
                        <!-- Revenue Chart -->
                        <div class="col-md-6 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Monthly Revenue</h4>
                                    <div class="chart-container">
                                        <canvas id="revenueChart" height="250"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Student Registrations Chart -->
                        <div class="col-md-6 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Student Registrations</h4>
                                    <div class="chart-container">
                                        <canvas id="studentRegistrationsChart" height="250"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Second Charts Row -->
                    <div class="row">
                        <!-- Top Courses Chart -->
                        <div class="col-md-6 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Top Selling Courses</h4>
                                    <div class="chart-container">
                                        <canvas id="topCoursesChart" height="250"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Department Courses Chart -->
                        <div class="col-md-6 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Courses by Department</h4>
                                    <div class="chart-container">
                                        <canvas id="departmentCoursesChart" height="250"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Orders and Blogs -->
                    <div class="row">
                        <!-- Recent Orders -->
                        <div class="col-md-6 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Recent Orders</h4>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Order #</th>
                                                    <th>Date</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $stmt = $pdo->query("SELECT * FROM orders ORDER BY Order_Date DESC LIMIT 5");
                                                $recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                
                                                foreach($recentOrders as $order) {
                                                    echo '<tr>
                                                            <td>'.$order['Invoice_Number'].'</td>
                                                            <td>'.date('M d, Y', strtotime($order['Order_Date'])).'</td>
                                                            <td>$'.number_format($order['Total'], 2).'</td>
                                                            <td><label class="badge badge-'.($order['Order_Status'] == 'Completed' ? 'success' : 'warning').'">'.$order['Order_Status'].'</label></td>
                                                        </tr>';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Recent Blogs -->
                        <div class="col-md-6 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Recent Blogs</h4>
                                    <div class="list-group">
                                        <?php
                                        $stmt = $pdo->query("SELECT * FROM blogs ORDER BY created_time DESC LIMIT 5");
                                        $recentBlogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        foreach($recentBlogs as $blog) {
                                            echo '<a href="#" class="list-group-item list-group-item-action flex-column align-items-start">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h6 class="mb-1">'.$blog['title'].'</h6>
                                                        <small>'.date('M d', strtotime($blog['created_time'])).'</small>
                                                    </div>
                                                    <p class="mb-1">'.substr($blog['intro_paragraph'], 0, 80).'...</p>
                                                    <small>By '.$blog['writer'].'</small>
                                                </a>';
                                        }
                                        ?>
                                    </div>
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

    <!-- Custom js for charts -->
    <script>
        // Revenue Chart
        <?php
        // Get monthly revenue data
        $stmt = $pdo->query("SELECT 
                                    DATE_FORMAT(Order_Date, '%Y-%m') as month, 
                                    SUM(Total) as revenue 
                                 FROM orders 
                                 WHERE Order_Status = 'Completed' 
                                 GROUP BY DATE_FORMAT(Order_Date, '%Y-%m') 
                                 ORDER BY month DESC LIMIT 6");
        $revenueData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $months = [];
        $revenues = [];
        foreach ($revenueData as $data) {
            $months[] = date('M Y', strtotime($data['month']));
            $revenues[] = $data['revenue'];
        }

        // Get top selling courses
        $stmt = $pdo->query("SELECT 
                                    c.Course_Name, 
                                    COUNT(oi.Order_ID) as sales 
                                 FROM order_items oi 
                                 JOIN courses c ON oi.Course_ID = c.Course_ID 
                                 GROUP BY c.Course_Name 
                                 ORDER BY sales DESC LIMIT 5");
        $topCourses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $courseNames = [];
        $courseSales = [];
        foreach ($topCourses as $course) {
            $courseNames[] = substr($course['Course_Name'], 0, 20) . "...";
            $courseSales[] = $course['sales'];
        }

        // Get courses by department
        $stmt = $pdo->query("SELECT 
                        d.Department_Name, 
                        COUNT(c.Course_ID) AS course_count 
                    FROM departments d
                    LEFT JOIN courses c ON d.Department_ID = c.Department_ID
                    GROUP BY d.Department_ID, d.Department_Name");
        $deptCourses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $deptNames = [];
        $deptCounts = [];
        foreach ($deptCourses as $dept) {
            $deptNames[] = $dept['Department_Name'];
            $deptCounts[] = $dept['course_count'];
        }

        // Get student registrations by month
        $stmt = $pdo->query("SELECT 
                                    DATE_FORMAT(Register_Date, '%Y-%m') as month, 
                                    COUNT(User_ID) as registrations 
                                 FROM user 
                                 WHERE Role = 'User' 
                                 GROUP BY DATE_FORMAT(Register_Date, '%Y-%m') 
                                 ORDER BY month DESC LIMIT 6");
        $regData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $regMonths = [];
        $regCounts = [];
        foreach ($regData as $reg) {
            $regMonths[] = date('M Y', strtotime($reg['month']));
            $regCounts[] = $reg['registrations'];
        }
        ?>

        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_reverse($months)); ?>,
                datasets: [{
                    label: 'Monthly Revenue ($)',
                    data: <?php echo json_encode(array_reverse($revenues)); ?>,
                    backgroundColor: 'rgba(75, 73, 172, 0.2)',
                    borderColor: 'rgba(75, 73, 172, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '$' + context.raw.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });

        // Top Courses Chart
        const topCoursesCtx = document.getElementById('topCoursesChart').getContext('2d');
        const topCoursesChart = new Chart(topCoursesCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($courseNames); ?>,
                datasets: [{
                    data: <?php echo json_encode($courseSales); ?>,
                    backgroundColor: [
                        'rgba(75, 73, 172, 0.7)',
                        'rgba(40, 167, 69, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(220, 53, 69, 0.7)',
                        'rgba(23, 162, 184, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.raw + ' sales';
                            }
                        }
                    }
                }
            }
        });

        // Department Courses Chart
        const deptCoursesCtx = document.getElementById('departmentCoursesChart').getContext('2d');
        const deptCoursesChart = new Chart(deptCoursesCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($deptNames); ?>,
                datasets: [{
                    label: 'Number of Courses',
                    data: <?php echo json_encode($deptCounts); ?>,
                    backgroundColor: 'rgba(75, 73, 172, 0.7)',
                    borderColor: 'rgba(75, 73, 172, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Student Registrations Chart
        const regCtx = document.getElementById('studentRegistrationsChart').getContext('2d');
        const regChart = new Chart(regCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_reverse($regMonths)); ?>,
                datasets: [{
                    label: 'Student Registrations',
                    data: <?php echo json_encode(array_reverse($regCounts)); ?>,
                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>