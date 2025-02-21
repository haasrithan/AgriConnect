<?php 
include('../includes/db.php');
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the user's role
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: login.php");
    exit();
}

$user_role = $user['role']; // Assuming 'role' is 'farmer', 'user', or 'organization'

// Check if the profile already exists
$stmt = $conn->prepare("SELECT * FROM profiles WHERE user_id = ?");
$stmt->execute([$user_id]);
$existingProfile = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existingProfile) {
    header("Location: farmer_dashboard.php");
    exit();
}

// Handle form submission
if (isset($_POST['submit'])) {
    // Capture the form data
    $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $phone_number = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    $profile_image = '';
    $agricultural_land = $farm_size = $organization_name = ''; // Add $organization_name here

    // For organizations, capture the organization name
    if ($user_role == 'organization') {
        $organization_name = isset($_POST['organization_name']) ? trim($_POST['organization_name']) : '';
    }

    // Check if required fields are not empty
    if (empty($full_name) || empty($phone_number) || empty($address) || ($user_role == 'organization' && empty($organization_name))) {
        echo "Please fill out all the required fields.";
        exit(); // Stop execution if fields are empty
    }

    // Handle profile image upload
    if (!empty($_FILES['profile_image']['name'])) {
        $target_dir = "uploads/";
        $image_name = time() . "_" . basename($_FILES["profile_image"]["name"]);
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $profile_image = $target_file;
        }
    }

    // For farmers, we need additional fields like agricultural_land and farm_size
    if ($user_role == 'farmer') {
        $agricultural_land = isset($_POST['agricultural_land']) ? trim($_POST['agricultural_land']) : '';
        $farm_size = isset($_POST['farm_size']) ? trim($_POST['farm_size']) : '';
    }

    // Insert into database
    try {
        $stmt = $conn->prepare("INSERT INTO profiles (user_id, full_name, phone_number, address, profile_image, agricultural_land, farm_size, organization_name) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $full_name, $phone_number, $address, $profile_image, $agricultural_land, $farm_size, $organization_name]);

        // Redirect based on user role
        if ($user_role == 'farmer') {
            header("Location: farmer_dashboard.php");
        } elseif ($user_role == 'organization') {
            header("Location: organization.php");
        } else {
            header("Location: ../index.php");
        }
        exit();
    } catch (PDOException $e) {
        // Handle any SQL exceptions
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 50%;
            margin: 50px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        label {
            font-size: 14px;
            color: #555;
            display: block;
            margin: 10px 0 5px;
        }

        input, textarea, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }

        input[type="file"] {
            padding: 5px;
        }

        button {
            background-color: #5cb85c;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #4cae4c;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group input[type="text"], 
        .form-group input[type="number"], 
        .form-group textarea {
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Create Your Profile</h1>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="full_name">Full Name:</label>
                <input type="text" name="full_name" id="full_name" required>
            </div>

            <div class="form-group">
                <label for="phone_number">Phone Number:</label>
                <input type="text" name="phone_number" id="phone_number" required>
            </div>

            <div class="form-group">
                <label for="address">Address:</label>
                <textarea name="address" id="address" rows="3" required></textarea>
            </div>

            <?php if ($user_role == 'farmer'): ?>
                <div class="form-group">
                    <label for="agricultural_land">Agricultural Land (in acres):</label>
                    <input type="number" name="agricultural_land" id="agricultural_land" required>
                </div>

                <div class="form-group">
                    <label for="farm_size">Farm Size:</label>
                    <input type="text" name="farm_size" id="farm_size" required>
                </div>
            <?php endif; ?>

            <?php if ($user_role == 'organization'): ?>
                <div class="form-group">
                    <label for="organization_name">Organization Name:</label>
                    <input type="text" name="organization_name" id="organization_name" required>
                </div>
            <?php endif; ?>

            <?php if ($user_role == 'farmer' || $user_role == 'organization'): ?>
                <div class="form-group">
                    <label for="profile_image">Profile Image:</label>
                    <input type="file" name="profile_image" id="profile_image">
                </div>
            <?php endif; ?>

            <button type="submit" name="submit">Submit Profile</button>
        </form>
    </div>

</body>
</html>
