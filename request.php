<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request OTP</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="otp-container">
        <h2>Request OTP</h2>
        <form method="POST" action="request_otp.php">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <button type="submit">Request OTP</button>
        </form>
    </div>
</body>
</html>
<?php
include 'dbb.php';

function generateOTP() {
    return rand(100000, 999999); // Generate a random 6-digit OTP
}

function sendOTP($email, $otp) {
    // Simple email sending function for demonstration
    $subject = "Your OTP Code";
    $message = "Your OTP code is $otp";
    $headers = "From: no-reply@example.com";
    
    return mail($email, $subject, $message, $headers);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    
    $sql = "SELECT * FROM otp WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $otp = generateOTP();
        $otp_expiry = date('Y-m-d H:i:s', strtotime('+5 minutes')); // OTP expiry in 5 minutes

        $update_sql = "UPDATE otp SET otp='$otp', otp_expiry='$otp_expiry' WHERE id=" . $row['id'];
        
        if ($conn->query($update_sql) === TRUE) {
            if (sendOTP($email, $otp)) {
                echo "<script>alert('Your OTP has been sent to your email.');</script>";
            } else {
                echo "<script>alert('Failed to send OTP.');</script>";
            }
        } else {
            echo "<script>alert('Error generating OTP.');</script>";
        }
    } else {
        echo "<script>alert('Email not found.');</script>";
    }
}

$conn->close();
?>
