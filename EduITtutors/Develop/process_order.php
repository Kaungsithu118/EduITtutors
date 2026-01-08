<?php
session_start();
include("admin/connect.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user information
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM user WHERE User_ID = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get cart items from session
if (!isset($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}
$cart = $_SESSION['cart'];

// Calculate totals
$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += $item['price'];
}
$tax = $subtotal * 0.07; // 7% tax
$total = $subtotal + $tax;

// Get payment method from form
$payment_method = $_POST['payment_method'] ?? 'Credit Card';
$card_name = $_POST['card_name'] ?? '';
$card_number = $_POST['card_number'] ?? '';
$card_expiry = $_POST['card_expiry'] ?? '';
$card_cvv = $_POST['card_cvv'] ?? '';
$card_zip = $_POST['card_zip'] ?? '';

// Extract card type and last 4 digits
$card_type = 'Unknown';
if (preg_match('/^4/', $card_number)) {
    $card_type = 'Visa';
} elseif (preg_match('/^5[1-5]/', $card_number)) {
    $card_type = 'Mastercard';
} elseif (preg_match('/^3[47]/', $card_number)) {
    $card_type = 'American Express';
} elseif (preg_match('/^6(?:011|5)/', $card_number)) {
    $card_type = 'Discover';
}
$card_last4 = substr(preg_replace('/[^0-9]/', '', $card_number), -4);

// Generate invoice number
$invoice_number = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

try {
    $pdo->beginTransaction();

    // Insert order record
    $order_stmt = $pdo->prepare("
        INSERT INTO orders (
            User_ID, Subtotal, Tax, Total, 
            Payment_Method, Card_Type, Card_Last4, Card_Expiry,
            Billing_Name, Billing_Address, Billing_City, 
            Billing_Country, Billing_Zip, Invoice_Number
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $order_stmt->execute([
        $user_id,
        $subtotal,
        $tax,
        $total,
        $payment_method,
        $card_type,
        $card_last4,
        $card_expiry,
        $card_name,
        $user['address'] ?? '',
        $user['city'] ?? '',
        $user['country'] ?? '',
        $card_zip,
        $invoice_number
    ]);
    
    $order_id = $pdo->lastInsertId();

    // Insert order items
    $item_stmt = $pdo->prepare("
        INSERT INTO order_items (
            Order_ID, Course_ID, Course_Name, Teacher_Name, Price, 
            Start_Date, End_Date
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($cart as $item) {
        $start_date = new DateTime(); // use DateTime object
        $duration = $item['duration'] ?? '12 weeks';

        $duration_parts = explode(' ', $duration);
        $duration_value = (int)$duration_parts[0];
        $duration_unit = strtolower($duration_parts[1] ?? 'weeks');

        $end_date = clone $start_date;
        if (strpos($duration_unit, 'month') !== false) {
            $end_date->add(new DateInterval("P{$duration_value}M"));
        } else {
            $end_date->add(new DateInterval("P{$duration_value}W"));
        }

        $item_stmt->execute([
            $order_id,
            $item['id'],
            $item['name'],
            $item['teacher'],
            $item['price'],
            $start_date->format('Y-m-d'),
            $end_date->format('Y-m-d')
        ]);
    }

    $pdo->commit();

    // Clear the cart
    unset($_SESSION['cart']);
    
    // Store order ID in session for invoice display
    $_SESSION['last_order_id'] = $order_id;

    // Redirect to invoice page
    header("Location: invoice.php");
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = "There was an error processing your order. Please try again.";
    header("Location: cart.php");
    exit();
}
?>
