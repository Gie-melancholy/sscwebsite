<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login1.php");
    exit();
}

if (isset($_POST['otp'])) {
    header("Location: login1.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="otp-container">
        <h2>OTP Verification</h2>
        <form id="otp-form" method="POST" action="login.php">
            <label for="otp">Enter OTP:</label>
            <input type="text" id="otp" name="otp" required>
            <button type="submit">Verify OTP</button>
        </form>
    </div>
</body>
</html>
