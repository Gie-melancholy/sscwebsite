<?php
include 'auth_check.php';
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $currentPassword = $conn->real_escape_string($_POST['current_password']);
    $newPassword = $conn->real_escape_string($_POST['new_password']);
    $confirmPassword = $conn->real_escape_string($_POST['confirm_password']);
    $username = $_SESSION['username'];

    if ($newPassword === $confirmPassword) {
        $sql = "SELECT password FROM users WHERE username = '$username'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($currentPassword == $row['password']) {
                $sql = "UPDATE users SET password = '$newPassword' WHERE username = '$username'";
                if ($conn->query($sql) === TRUE) {
                    $message = "Password updated successfully.";
                } else {
                    $message = "Error updating password: " . $conn->error;
                }
            } else {
                $message = "Current password is incorrect.";
            }
        } else {
            $message = "User not found.";
        }
    } else {
        $message = "New passwords do not match.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="all.css">
    <style>
        .change-password-container {
            width: 300px;
            margin: 100px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .change-password-container h2 {
            text-align: center;
        }
        .change-password-container label {
            display: block;
            margin: 10px 0 5px;
        }
        .change-password-container input[type="password"] {
            width: 280px;
            padding: 10px;
            margin: 5px 0 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .change-password-container button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 4px;
            background-color: #2C3E50;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }
        .change-password-container button:hover {
            background-color: #34495E;
        }
        .message {
            color: Black;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="menu">
            <a href="index.php">Events</a>
            <a href="attendance.php">Files</a>
            <a href="about.php">About Us</a>
        </div>
        <div class="logout">
            <img src="ssc logo.png" alt="User Image">
            <p><a href="logout.php">Logout</a></p>
            <p><a href="set.php">Settings</a></p>
        </div>
    </div>  
    <div class="change-password-container">
        <h2>Change Password</h2>
        <?php if (isset($message)): ?>
            <p class="message"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <form method="POST" action="set.php">
            <label for="current_password">Current Password:</label>
            <input type="password" id="current_password" name="current_password" placeholder="Enter current password" required>

            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>

            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>

            <button type="submit">Change Password</button>
        </form>
    </div>
</body>
</html>
