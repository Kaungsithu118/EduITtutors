<?php
include('connect.php');

// Get order ID from URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Fetch order details
$stmt = $pdo->prepare("
    SELECT o.*, u.Name as UserName, u.Email as UserEmail
    FROM orders o
    JOIN user u ON o.User_ID = u.User_ID
    WHERE o.Order_ID = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch order items
$stmt = $pdo->prepare("
    SELECT oi.*, c.Course_Name, t.Teacher_Name
    FROM order_items oi
    JOIN courses c ON oi.Course_ID = c.Course_ID
    JOIN teachers t ON c.Teacher_ID = t.Teacher_ID
    WHERE oi.Order_ID = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$subtotal = $order['Subtotal'];
$tax = $order['Tax'];
$total = $order['Total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo $order['Invoice_Number']; ?></title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }
        .logo {
            max-width: 150px;
            height: auto;
        }
        .invoice-info {
            text-align: right;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .invoice-number {
            font-size: 18px;
            color: #555;
        }
        .billing-info {
            margin-bottom: 30px;
        }
        .billing-info h3 {
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background-color: #f5f5f5;
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .totals {
            margin-left: auto;
            width: 300px;
        }
        .totals table {
            width: 100%;
        }
        .totals td {
            text-align: right;
        }
        .totals .total {
            font-weight: bold;
            font-size: 18px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        @media print {
            body {
                padding: 0;
            }
            .invoice-container {
                border: none;
                box-shadow: none;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <div class="company-info">
                <!-- Replace with your actual logo path -->
                <img src="../photo/logo/EduITtutors_Colorver_Logo (1).png" alt="Company Logo" class="logo">
                <h2>EduIT Tutors</h2>
                <p>123 Education Street</p>
                <p>Knowledge City, 12345</p>
                <p>Phone: (123) 456-7890</p>
                <p>Email: info@eduitutors.com</p>
            </div>
            <div class="invoice-info">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-number">#<?php echo $order['Invoice_Number']; ?></div>
                <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($order['Order_Date'])); ?></p>
                <p><strong>Status:</strong> <?php echo $order['Order_Status']; ?></p>
            </div>
        </div>

        <div class="billing-info">
            <h3>Bill To:</h3>
            <p><?php echo $order['UserName']; ?></p>
            <p><?php echo $order['Billing_Address'] ?? 'N/A'; ?></p>
            <p><?php echo ($order['Billing_City'] ?? '') . ', ' . ($order['Billing_Country'] ?? ''); ?></p>
            <p>ZIP: <?php echo $order['Billing_Zip'] ?? 'N/A'; ?></p>
            <p>Email: <?php echo $order['UserEmail']; ?></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Instructor</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo $item['Course_Name']; ?></td>
                    <td><?php echo $item['Teacher_Name']; ?></td>
                    <td><?php echo $item['Start_Date']; ?></td>
                    <td><?php echo $item['End_Date']; ?></td>
                    <td><?php echo $item['Access_Status']; ?></td>
                    <td>$<?php echo number_format($item['Price'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal:</td>
                    <td>$<?php echo number_format($subtotal, 2); ?></td>
                </tr>
                <tr>
                    <td>Tax:</td>
                    <td>$<?php echo number_format($tax, 2); ?></td>
                </tr>
                <tr class="total">
                    <td>Total:</td>
                    <td>$<?php echo number_format($total, 2); ?></td>
                </tr>
                <tr>
                    <td>Payment Method:</td>
                    <td><?php echo $order['Payment_Method']; ?></td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>Thank you for your business!</p>
            <p>If you have any questions about this invoice, please contact</p>
            <p>support@eduitutors.com or call (123) 456-7890</p>
        </div>

        <div class="no-print" style="margin-top: 20px; text-align: center;">
            <button onclick="window.print()" style="padding: 10px 20px; background: #4CAF50; color: white; border: none; cursor: pointer;">Print Invoice</button>
        </div>
    </div>

    
</body>
</html>