<?php
include '../includes/db.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch product details before deleting
    $stmt = $conn->prepare("SELECT farmer_id, product_name FROM product_requests WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $farmer_id = $product['farmer_id'];
        $product_name = $product['product_name'];

        // Insert notification for the farmer
        $message = "Your request to add '$product_name' has been rejected.";
        $stmt = $conn->prepare("INSERT INTO notifications (farmer_id, message, status) VALUES (?, ?, 'unread')");
        $stmt->execute([$farmer_id, $message]);

        // Delete the product request
        $stmt = $conn->prepare("DELETE FROM product_requests WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: dashboard.php?message=Product request rejected");
        exit();
    }
}
?>
