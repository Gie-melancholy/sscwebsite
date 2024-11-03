<?php
session_start();

// Include Composer's autoloader
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database configuration
$servername = "localhost";
$username = "root";  // Change if necessary
$password = "";      // Change if necessary
$dbname = "ssc_website";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Username = $conn->real_escape_string($_POST['username']);
    $Password = $conn->real_escape_string($_POST['password']);

    $sql = "SELECT * FROM users WHERE username = '$Username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($Password == $row['password']) {
            // Generate OTP
            $otp = rand(100000, 999999);
            $_SESSION['username'] = $Username;
            $_SESSION['otp'] = $otp;

            // Update OTP in the database
            $updateOtpSql = "UPDATE users SET otp='$otp' WHERE username='$Username'";
            $conn->query($updateOtpSql);

            // Send OTP via email using PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.example.com'; // Set the SMTP server to send through
                $mail->SMTPAuth = true;
                $mail->Username = 'your_email@example.com'; // SMTP username
                $mail->Password = 'your_password'; // SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('no-reply@example.com', 'SSC Website');
                $mail->addAddress($row['email']); // Send email to the user's email from the database

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Your OTP Code';
                $mail->Body = "Your OTP code is $otp";

                $mail->send();
                header("Location: ver.php");
                exit();
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "<script>alert('Incorrect username or password.'); window.location.href='login1.php';</script>";
        }
    } else {
        echo "<script>alert('Incorrect username or password.'); window.location.href='login1.php';</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supreme Student Council Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="ssc logo.png" alt="Supreme Student Council Logo">
        </div>
        <h2>Supreme Student Council</h2>
        <h3>Isabela State University Santiago Extension Unit</h3>
        <form id="login-form" method="POST" action="login1.php">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
