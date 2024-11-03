<?php
session_start();

$servername = "localhost";
$username = "root";  // Change if necessary
$password = "";      // Change if necessary
$dbname = "ssc_website";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Username = $conn->real_escape_string($_POST['username']);
    $Password = $conn->real_escape_string($_POST['password']);

    $sql = "SELECT * FROM userss WHERE username = '$Username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($Password == $row['password']) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $Username;
            header("Location: index.php");
            exit();
        } else {
            echo "<script>alert('Incorrect username or password.'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('Incorrect username or password.'); window.location.href='login.php';</script>";
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
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Add your CSS styling here */
        .login-container {
            width: 300px;
            margin: 100px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .login-container h2, .login-container h3 {
            text-align: center;
        }
        .login-container label {
            display: block;
            margin: 10px 0 5px;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: calc(100% - 10px); /* Adjust width to account for the icon */
            padding: 10px;
            margin: 5px 0 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            height: 40px; /* Adjust height for better alignment */
            box-sizing: border-box;
            position: relative;
        }
        .login-container .password-container {
            position: relative;
        }
        .login-container .password-container i {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #2C3E50;
            font-size: 18px;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 4px;
            background-color: #2C3E50;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }
        .login-container button:hover {
            background-color: #34495E;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="ssc logo.png" alt="Supreme Student Council Logo">
        </div>
        <h2>Supreme Student Council</h2>
        <h3>Isabela State University Santiago Extension Unit</h3>
        <form id="login-form" method="POST" action="login.php">
           
            <input type="text" id="username" name="username" placeholder="Username" required>

            
            <div class="password-container">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <i class="fa fa-eye" id="toggle-password"  onclick="togglePassword()"></i>
            </div>

            <button type="submit">Login</button>
        </form>
    </div>
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('toggle-password');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>