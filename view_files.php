<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

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

// Retrieve all uploaded files from the database
$sql = "SELECT * FROM uploaded_files";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Files</title>
    <link rel="stylesheet" href="all.css">
</head>
<body>
    <div class="navbar">
        <div class="menu">
            <a href="index.php">Events</a>
            <a href="attendance.php">Attendance</a>
            <a href="about.php">About Us</a>
        </div>
        <div class="logout">
            <img src="ssc logo.png" alt="User Image">
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <div class="container">
        <h2>All Uploaded Files</h2>
        <div class="uploaded-files">
            <?php if ($result->num_rows > 0) : ?>
                <ul>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <li><a href="<?php echo $row['file_path']; ?>" target="_blank"><?php echo $row['file_name']; ?></a></li>
                    <?php endwhile; ?>
                </ul>
            <?php else : ?>
                <p>No files uploaded yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
