<?php
session_start();
include '../includes/db.php';

// Check if the organization is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organization') {
    header("Location: login.php");
    exit();
}

$organization_id = $_SESSION['user_id'];

// Fetch organization profile details securely
$profile_stmt = $conn->prepare("SELECT * FROM profiles WHERE user_id = ?");
$profile_stmt->execute([$organization_id]);
$profile = $profile_stmt->fetch(PDO::FETCH_ASSOC);

// Fetch stubble burning data along with request status
$sql = "SELECT sb.*, cr.status 
        FROM stubbleburning sb
        LEFT JOIN collection_requests cr ON sb.id = cr.stubble_id AND cr.organization_id = ?
        ORDER BY sb.id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute([$organization_id]);
$stubble_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle Accept/Reject Action for Collection Request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && isset($_POST['stubble_id'])) {
    $stubble_id = $_POST['stubble_id'];
    $action = $_POST['action'];

    // Set status based on action
    if ($action == 'accept') {
        $status = 'accepted';
    } else {
        $status = 'rejected';
    }

    // Check if a request already exists
    $check_stmt = $conn->prepare("SELECT * FROM collection_requests WHERE stubble_id = ? AND organization_id = ?");
    $check_stmt->execute([$stubble_id, $organization_id]);
    $existing_request = $check_stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing_request) {
        // Update the existing request
        $update_stmt = $conn->prepare("UPDATE collection_requests SET status = ? WHERE stubble_id = ? AND organization_id = ?");
        $update_stmt->execute([$status, $stubble_id, $organization_id]);
    } else {
        // Insert a new request
        $insert_stmt = $conn->prepare("INSERT INTO collection_requests (stubble_id, organization_id, status) VALUES (?, ?, ?)");
        $insert_stmt->execute([$stubble_id, $organization_id, $status]);
    }

    // Return JSON response
    echo json_encode(['status' => $status, 'stubble_id' => $stubble_id]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organization Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 1000px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .profile-section {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .profile-section h3 {
            color: #007bff;
        }
        .profile-details p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #28a745;
            color: white;
        }
        .status {
            font-weight: bold;
        }
        .status-requested {
            color: #ffc107;
        }
        .status-accepted {
            color: #28a745;
        }
        .status-rejected {
            color: #dc3545;
        }
        .action-btn:hover
         {
            background-color: #0056b3;
        }
        .logout-btn {
            float: right;
            background-color: #dc3545;
            color: white;
            padding: 8px 12px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin-top: -40px;
        }
        .logout-btn:hover {
            background-color: #b02a37;
        }
        td {
    vertical-align: middle;
    text-align: center;
}

.action-btn {
    display: block;
    width: 100%;
    margin: 5px 0;
    padding: 10px;
    font-size: 14px;
    text-align: center;
}

.action-container {
    display: flex;
    flex-direction: column;
    align-items: center;
}

    </style>
</head>
<body>
    <div class="container">
        <h2>Organization Dashboard</h2>
        <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>

        <!-- Organization Profile Section -->
        <div class="profile-section">
            <h3>Organization Profile</h3>
            <div class="profile-details">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($profile['organization_name'] ?? 'Not Available'); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($profile['phone_number'] ?? 'Not Available'); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($profile['address'] ?? 'Not Available'); ?></p>
            </div>
        </div>

        <!-- Available Stubble for Collection -->
        <h2>Available Stubble for Collection</h2>
        <table>
            <tr>
                <th>Farmer Name</th>
                <th>Phone</th>
                <th>Location</th>
                <th>Acres</th>
                <th>Stubble Quantity (Tons)</th>
                <th>Est. Collection Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php foreach ($stubble_data as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['farmer_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                    <td><?php echo htmlspecialchars($row['acres_of_land']); ?></td>
                    <td><?php echo htmlspecialchars($row['stubble_quantity']); ?></td>
                    <td><?php echo htmlspecialchars($row['estimation_of_stubble']); ?></td>
                    <td id="status-<?php echo $row['id']; ?>" class="status status-<?php echo $row['status']; ?>">
                        <?php echo ucfirst($row['status'] ?? 'Requested'); ?>
                    </td>
                    <td>
                <div class="action-container">
                    <button class="action-btn" onclick="handleRequestCollection(<?php echo $row['id']; ?>, 'accept')">Accept</button>
                    <button class="action-btn" onclick="handleRequestCollection(<?php echo $row['id']; ?>, 'reject')">Reject</button>
                </div>
                   </td>

                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <script>
        function handleRequestCollection(stubbleId, action) {
            fetch("", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "action=" + action + "&stubble_id=" + stubbleId
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById("status-" + stubbleId).textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
            });
        }
    </script>
</body>
</html>  