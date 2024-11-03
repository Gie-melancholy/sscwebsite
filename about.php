<?php 
include 'auth_check.php'; 

$servername = "localhost";
$username = "root";  // Change if necessary
$password = "";      // Change if necessary
$dbname = "ssc_website";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM uploaded_files";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link rel="stylesheet" href="all.css">
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
            </div>
        </div>
    <div class="container">
        <h2>ABOUT US</h2>
		
		<div>
            <p>Vision:<br>
            The Supreme Student Council (SSC) of Isabela State University aims to foster a dynamic and inclusive student community where every voice is heard, every talent is nurtured, and every student is empowered to achieve their full potential.</p>
            
            <p>Mission:<br>
            Our mission is to represent and serve the student body with integrity, transparency, and dedication. We strive to enhance the overall student experience through advocacy, leadership, and the organization of meaningful activities and programs.</p>
            
            <p>Core Values:<br>
            - Leadership: Cultivating responsible and innovative leaders.<br>
            - Integrity: Upholding the highest standards of honesty and transparency.<br>
            - Inclusivity: Embracing diversity and ensuring every student feels valued and included.<br>
            - Service: Dedication to serving the student community and addressing their needs and concerns.<br>
            - Excellence: Striving for excellence in all our endeavors, from academics to extracurricular activities.</p>
		</div>
    
    </div>
</body>
</html>
