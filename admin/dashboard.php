<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include '../includes/db.php';
$stmt = $conn->prepare("SELECT id, message, status, order_id, created_at FROM notifications WHERE status = 'unread' ORDER BY created_at DESC LIMIT 5");
$stmt->execute();
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $conn->prepare("SELECT id, farmer_name, phone_number, product_name, price_per_kg, quantity, status FROM product_requests WHERE status = 'pending' ORDER BY id DESC");
$stmt->execute();
$product_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (isset($_GET['order_id']) && is_numeric($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);
    $stmt = $conn->prepare("UPDATE notifications SET status = 'read' WHERE order_id = ?");
    $stmt->execute([$order_id]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }
    .container {
        width: 80%;
        margin: 50px auto;
        background-color: #fff;
        padding: 30px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }
    h2 {
        text-align: center;
        color: #333;
    }
    nav {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 20px;
    }
    nav a {
        text-decoration: none;
        padding: 12px 25px;
        background-color: #4CAF50;
        color: white;
        font-size: 16px;
        border-radius: 4px;
        transition: background-color 0.3s ease;
    }
    nav a:hover {
        background-color: #45a049;
    }
    .logout {
        background-color: #f44336;
    }
    .logout:hover {
        background-color: #e53935;
    }
    footer {
        text-align: center;
        margin-top: 50px;
        font-size: 14px;
        color: #777;
    }
    .notifications, .product-requests {
        background-color: #fff;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
        border-radius: 8px;
        margin-top: 30px;
    }
    .notification-item, .product-item {
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 5px;
        border-left: 5px solid #4CAF50;
        transition: background-color 0.3s ease;
    }
    .notification-item.new {
        background-color: #e1f7d5;
    }
    .notification-item.read {
        background-color: #f1f1f1;
    }
    .notification-item strong, .product-item strong {
        font-weight: bold;
    }
    .notification-item a, .product-item a {
        text-decoration: none;
        color: #007bff;
    }
    .notification-item a:hover, .product-item a:hover {
        text-decoration: underline;
    }
    .order-summary {
        margin-top: 30px;
        padding: 10px;
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
        text-align: center; 
        padding: 12px; 
        font-weight: bold; 
    }
    td {
        text-align: center;
    }
    a {
        text-decoration: none;
        color: #007bff;
    }
    a:hover {
        text-decoration: underline;
    }
    hr {
        border: 0;
        height: 1px;
        background-color: #ddd;
        margin: 30px 0;
    }
    .action-btn-container {
        display: flex;
        gap: 10px;
        justify-content: center; 
    }

    .action-btn {
        padding: 8px 15px; 
        font-size: 14px;
        background-color: #4CAF50; 
        color: white;
        border: none;
        border-radius: 4px; 
        cursor: pointer;
        text-decoration: none;
        width: auto;
        min-width: 100px;
        text-align: center;
    }

    .action-btn:hover {
        background-color: #45a049;
    }

    .reject-btn {
        background-color: #f44336;
    }

    .reject-btn:hover {
        background-color: #e53935;
    }
</style>
</head>
<body>
    <div class="container">
        <h2>Admin Dashboard</h2>
        <nav>
            <a href="add_product.php">Add Product</a>
            <a href="manage_products.php">Manage Products</a>
            <a href="logout.php" class="logout">Logout</a>
        </nav>
        <hr>
        <div class="notifications">
            <h3>New Order Notifications</h3>
            <?php if (!empty($notifications)): ?>
                <?php foreach ($notifications as $notification): ?>
                    <div class="notification-item <?php echo ($notification['status'] === 'unread') ? 'new' : 'read'; ?>">
                        <strong><?php echo htmlspecialchars($notification['message']); ?></strong>
                        <br>
                        <a href="dashboard.php?order_id=<?php echo intval($notification['order_id']); ?>">View Order</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No new notifications.</p>
            <?php endif; ?>
        </div>
        <div class="product-requests">
            <h3>Product Addition Requests</h3>
            <?php if (!empty($product_requests)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Farmer Name</th>
                            <th>Phone</th>
                            <th>Price per Kg</th>
                            <th>Quantity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($product_requests as $request): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($request['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($request['farmer_name']); ?></td>
                                <td><?php echo htmlspecialchars($request['phone_number']); ?></td>
                                <td>₹<?php echo htmlspecialchars($request['price_per_kg']); ?></td>
                                <td><?php echo htmlspecialchars($request['quantity']); ?> Kg</td>
                                <td>
                                    <div class="action-btn-container">
                                        <a href="add_product.php?id=<?php echo $request['id']; ?>" class="action-btn">Approve</a>
                                        <a href="reject_product.php?id=<?php echo $request['id']; ?>" class="action-btn reject-btn">Reject</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No pending product requests.</p>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Admin Dashboard</p>
    </footer>
</body>
</html>
