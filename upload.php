
<?php
include 'auth_check.php';

$servername = "localhost";
$username = "root";  // Change if necessary
$password = "";      // Change if necessary
$dbname = "ssc_website";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['upload'])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["file"]["name"]);
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed_types = ['csv', 'jpg', 'jpeg', 'png', 'gif', 'pdf', 'docx', 'xlsx'];

    if (in_array($file_type, $allowed_types)) {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $file_name = $conn->real_escape_string(basename($_FILES["file"]["name"]));
            $file_path = $conn->real_escape_string($target_file);

            $sql = "INSERT INTO uploaded_files (file_name, file_path) VALUES ('$file_name', '$file_path')";
            
            if ($conn->query($sql) === TRUE) {
                echo "The file has been uploaded and saved in the database.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "Sorry, only CSV, JPG, JPEG, PNG, GIF, PDF, DOCX & XLSX files are allowed.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File</title>
    <link rel="stylesheet" href=".css">
</head>
<body>
    <div class="container">
        <h2>Upload File</h2>

        <!-- File Upload Form -->
        <form method="post" action="upload.php" enctype="multipart/form-data">
            <label for="file">Select file (CSV, JPG, JPEG, PNG, GIF, PDF, DOCX, XLSX):</label>
            <input type="file" id="file" name="file" accept=".csv, .jpg, .jpeg, .png, .gif, .pdf, .docx, .xlsx" required>
            <button type="submit" name="upload">Upload</button>
        </form>
    </div>
</body>
</html>
