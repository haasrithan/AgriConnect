<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

$user_id = $_SESSION['user_id'];

// Fetch user's cart items along with product details (price)
$stmt = $conn->prepare("SELECT c.*, p.price, p.name, p.image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate the total cost
$total_cost = 0;
foreach ($cart_items as $cart_item) {
    $total_cost += $cart_item['price'] * $cart_item['quantity'];
}

// Handle the order placement when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    // Get user details from the form and sanitize input
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $phone_number = trim($_POST['phone_number']);
    $payment_method = $_POST['payment_method'];

    // Basic input validation
    if (empty($name) || empty($address) || empty($phone_number) || empty($payment_method)) {
        die("All fields are required.");
    }

    if (!preg_match("/^[0-9]+$/", $phone_number)) {
        die("Invalid phone number format.");
    }

    // Insert the order into the 'orders' table
    $stmt = $conn->prepare("INSERT INTO orders (user_id, name, address, phone_number, total_cost, status, payment_method, created_at) VALUES (?, ?, ?, ?, ?, 'pending', ?, NOW())");
    $stmt->execute([$user_id, $name, $address, $phone_number, $total_cost, $payment_method]);

    // Get the last inserted order ID
    $order_id = $conn->lastInsertId();

    // Insert cart items into the 'order_items' table
    foreach ($cart_items as $cart_item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$order_id, $cart_item['product_id'], $cart_item['quantity']]);
    }

    // Insert notification for admin (without foreign key constraint)
    $message = "New order placed by $name. Order ID: $order_id, Total Cost: $$total_cost";
    $stmt = $conn->prepare("INSERT INTO notifications (message, status, order_id, created_at) VALUES (?, 'unread', ?, NOW())");
    $stmt->execute([$message, $order_id]);

    // Clear cart after order placement
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);

    // Redirect to order confirmation page
    header("Location: order_confirmation.php?order_id=$order_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        /* Add your styles here */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h2 {
            text-align: center;
            font-size: 2em;
            margin-bottom: 20px;
        }
        .checkout-summary {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #f1f1f1;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        .checkout-summary div {
            font-size: 1.2em;
            color: #343a40;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            font-size: 1.1em;
            margin-bottom: 5px;
            display: block;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            font-size: 1.1em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #218838;
        }
        .order-summary {
            font-size: 1.4em;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Checkout</h2>

    <!-- Cart Summary -->
    <div class="checkout-summary">
        <div>Total Cost: $<?= number_format($total_cost, 2); ?></div>
    </div>

    <!-- Shipping & Payment Form -->
    <form method="POST">
        <div class="form-group">
            <label for="name">Full Name:</label>
            <input type="text" name="name" id="name" required>
        </div>

        <div class="form-group">
            <label for="address">Shipping Address:</label>
            <textarea name="address" id="address" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label for="phone_number">Phone Number:</label>
            <input type="text" name="phone_number" id="phone_number" required>
        </div>

        <div class="form-group">
            <label for="payment_method">Payment Method:</label>
            <select name="payment_method" id="payment_method" required>
                <option value="Credit Card">Credit Card</option>
                <option value="PayPal">PayPal</option>
                <option value="Bank Transfer">Bank Transfer</option>
            </select>
        </div>

        <button type="submit" name="place_order">Place Order</button>
    </form>

    <!-- Order Summary -->
    <div class="order-summary">
        <h3>Order Summary</h3>
        <p>Review your items and proceed with payment.</p>
    </div>
</div>

</body>
</html>