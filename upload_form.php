<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="container">
        <h2>Upload File</h2>
        <form method="post" action="process_upload.php" enctype="multipart/form-data">
            <label for="file">Select file:</label>
            <input type="file" id="file" name="file" accept=".jpg, .jpeg, .png, .gif, .pdf, .docx, .xlsx">
            <button type="submit">Upload</button>
        </form>
    </div>
</body>
</html>
<?php
session_start();

// Database configuration
$servername = "localhost";
$username = "root"; // Change if necessary
$password = ""; // Change if necessary
$dbname = "ssc_website";

// Check if form submitted with file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $file_name = $_FILES["file"]["name"];
    $file_tmp = $_FILES["file"]["tmp_name"];

    // Move uploaded file to a directory
    $uploads_dir = 'uploads/';
    $target_file = $uploads_dir . basename($file_name);

    if (move_uploaded_file($file_tmp, $target_file)) {
        // File upload successful, now store details in database
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Prepare SQL to insert file details into database
        $sql = "INSERT INTO uploaded_files (file_name, file_path, upload_date) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $file_name, $target_file);
        
        if ($stmt->execute()) {
            echo "File uploaded successfully: <a href='$target_file' target='_blank'>$file_name</a>";
        } else {
            echo "Error uploading file.";
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Error uploading file.";
    }
}
?>
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f0f0f0;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 600px;
    margin: 50px auto;
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    color: #333;
}

form {
    display: flex;
    flex-direction: column;
}

label {
    margin-bottom: 10px;
}

input, button {
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
}

button {
    background-color: #2C3E50;
    color: #fff;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #34495E;
}

input[type="file"] {
    width: 100%;
}

</style>