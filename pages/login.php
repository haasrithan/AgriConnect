<?php
include('../includes/db.php');  // Include the database connection
session_start();

// Set the default language if it's not already set
if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = 'en'; // Default language is English
}

if (isset($_POST['login'])) 
{
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    // Prepare the SQL query to fetch user details based on email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Verify the password and check the role
        if (password_verify($password, $user['password']) && strtolower($user['role']) === strtolower($role)) {
            // Successful login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on user role
            if (strtolower($user['role']) === 'farmer') {
                header("Location: ../pages/farmer_dashboard.php");
            } elseif (strtolower($user['role']) === 'organization') {
                $_SESSION['org_email'] = $user['email'];
                header("Location: ../pages/organization.php");
            } else {
                header("Location: ../index.php");
            }
            exit();
        } else {
            $error_message = "Invalid password or role.";
        }
    } else {
        $error_message = "Email not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            background: url('https://static.vecteezy.com/system/resources/previews/019/878/100/non_2x/planting-a-small-plant-on-a-pile-of-soil-with-gardening-tools-on-green-bokeh-background-free-photo.jpg') no-repeat center center/cover;
            font-family: 'Poppins', sans-serif;
            margin:0;
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
        }
        .login-container {
            background:rgba(255,255,255,0.95);
            padding:50px 40px;
            border-radius:25px;
            box-shadow:0 15px 35px rgba(0,0,0,0.5);
            animation:fadeIn 1s ease-in;
            max-width:450px;
            width:90%;
        }
        h2 {
            font-size:2.5em;
            margin-bottom:20px;
            color:#1b4332;
            letter-spacing:1.5px;
            text-align:center;
        }
        label {
            display:block;
            margin-top:15px;
            font-size:1.1em;
            color:#333;
            text-align:left;
        }
        input, select {
            width:100%;
            padding:10px;
            margin:10px 0 20px;
            border:1px solid #ccc;
            border-radius:8px;
            font-size:1em;
        }
        button {
            width:100%;
            padding:12px;
            background:linear-gradient(135deg, #0f9d58 0%, #0f766e 100%);
            color:#fff;
            border:none;
            border-radius:8px;
            font-size:1.2em;
            cursor:pointer;
            transition:transform 0.4s, box-shadow 0.4s;
        }
        button:hover {
            transform:translateY(-5px);
            box-shadow:0 8px 25px rgba(0,0,0,0.4);
        }
        .error-message {
            color:#e74c3c;
            margin-top:15px;
            font-size:1.1em;
        }
        .back-button {
            position:absolute;
            top:20px;
            left:20px;
            background:rgba(0,0,0,0.3);
            color:white;
            padding:10px 20px;
            border-radius:8px;
            text-decoration:none;
            font-size:1.1em;
        }
        .back-button:hover {
            background:rgba(0,0,0,0.5);
        }
        .login-container:hover{
            transform: scale(1.05);
        }
        @keyframes fadeIn {from {opacity:0;} to {opacity:1;}}
    </style>
</head>
<body>

    <div class="login-container">
        <h2 id="login-title">Login</h2>
        <form method="POST">
        <a href="register.php" class="back-button" id="back-button">Back</a>

        <label id="email-label">Email:</label>
        <input type="email" name="email" required>

        <label id="password-label">Password:</label>
        <input type="password" name="password" required>

        <label id="role-label">Select Role:</label>
        <select name="role" required>
            <option value="Farmer">Farmer</option>
            <option value="User">User</option>
            <option value="Organization">Organization</option> <!-- Add Organization role -->
        </select>

        <button type="submit" name="login" id="login-btn">Login</button>
        </form>

        <?php if (isset($error_message)): ?>
            <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>
    </div>

    <script>
        // Language translations
        const translations = {
            en: {
                "login-title": "Login",
                "email-label": "Email",
                "password-label": "Password",
                "role-label": "Select Role",
                "login-btn": "Login",
                "back-button": "Back"
            },
            hi: {
                "login-title": "लॉगिन करें",
                "email-label": "ईमेल",
                "password-label": "पासवर्ड",
                "role-label": "रोल चुनें",
                "login-btn": "लॉगिन करें",
                "back-button": "वापस"
            },
            te: {
                "login-title": "లాగిన్",
                "email-label": "ఈమెయిల్",
                "password-label": "పాస్‌వర్డ్",
                "role-label": "రోల్ ఎంచుకోండి",
                "login-btn": "లాగిన్",
                "back-button": "తిరిగి"
            }
        };

        // Translate page based on selected language
        function translatePage(language = '<?php echo $_SESSION['language'] ?? 'en'; ?>') {
            if (translations[language]) {
                Object.keys(translations[language]).forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.textContent = translations[language][id];
                    }
                });
            }
        }

        // Execute translation when the page loads
        window.onload = () => {
            translatePage();
        };
    </script>
</body>
</html>
