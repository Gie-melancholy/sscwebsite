<?php
include ('auth_check.php');
// Database connection settings
$servername = "localhost";
$username = "root";  // Update with your database username if necessary
$password = "";      // Update with your database password if necessary
$dbname = "ssc_website";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = ""; // Initialize empty message variable

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'upload') {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["file"]["name"]);
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed_types = ['csv', 'jpg', 'jpeg', 'png', 'gif', 'pdf', 'docx', 'xlsx'];

    // Check if file type is allowed
    if (in_array($file_type, $allowed_types)) {
        // Ensure the uploads directory exists
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Attempt to move the uploaded file to the target directory
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $file_name = $conn->real_escape_string(basename($_FILES["file"]["name"]));
            $file_path = $conn->real_escape_string($target_file);

            // Insert file details into the database
            $sql = "INSERT INTO uploaded_files (file_name, file_path) VALUES ('$file_name', '$file_path')";
            if ($conn->query($sql) === TRUE) {
                $message = "File uploaded successfully!";
            } else {
                $message = "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            $message = "Sorry, there was an error uploading your file.";
        }
    } else {
        $message = "Sorry, only CSV, JPG, JPEG, PNG, GIF, PDF, DOCX & XLSX files are allowed.";
    }
}

// Handle file deletion
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Select file path from database
    $select_sql = "SELECT file_path FROM uploaded_files WHERE id = ?";
    $stmt = $conn->prepare($select_sql);
    $stmt->bind_param('i', $delete_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $file_path = $row['file_path'];

        // Delete file from server
        if (unlink($file_path)) {
            // Delete file record from database
            $delete_sql = "DELETE FROM uploaded_files WHERE id = ?";
            $stmt = $conn->prepare($delete_sql);
            $stmt->bind_param('i', $delete_id);
            if ($stmt->execute()) {
                $message = "File deleted successfully.";
            } else {
                $message = "Error deleting file: " . $conn->error;
            }
        } else {
            $message = "Error deleting file from server.";
        }
    } else {
        $message = "File not found.";
    }
}

// Retrieve uploaded files from the database
$sql = "SELECT * FROM uploaded_files";
$result = $conn->query($sql);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Files</title>
    <link rel="stylesheet" href="all.css">
    <style>
        .small-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: #E74C3C;
            padding: 0;
        }
        .uploaded-files ul {
            list-style-type: none;
            padding: 0;
        }
        .uploaded-files li {
            margin: 5px 0;
            padding: 5px;
            border-bottom: 1px solid #ddd;
        }
        .uploaded-files a {
            text-decoration: none;
            color: #3498DB;
        }
    </style>
    <script>
        function showMessage(message) {
            alert(message);
        }
    </script>
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
    <h2>Upload Here</h2>
    <?php if (!empty($message)) : ?>
        <script>
            showMessage("<?php echo $message; ?>");
        </script>
    <?php endif; ?>

    <!-- Form for file upload -->
    <form method="post" action="attendance.php" enctype="multipart/form-data">
        <label for="file">Select file (CSV, JPG, JPEG, PNG, GIF, PDF, DOCX, XLSX):</label>
        <input type="file" id="file" name="file" accept=".csv, .jpg, .jpeg, .png, .gif, .pdf, .docx, .xlsx, .doc" required>
        <input type="hidden" name="action" value="upload"> <!-- Action to handle upload -->
        <button type="submit">Upload</button>
    </form>

    <div class="uploaded-files">
        <h3>Uploaded Files</h3>
        <ul>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $file_id = $row['id'];
                    $file_name = $row['file_name'];
                    $file_path = $row['file_path'];
                    echo "<li>";
                    echo "<a href='" . $file_path . "' target='_blank'>" . $file_name . "</a>";
                    echo "<form method='get' action='attendance.php' style='display:inline;'>";
                    echo "<input type='hidden' name='action' value='delete'>";
                    echo "<input type='hidden' name='delete_id' value='$file_id'>";
                    echo "<button type='submit' class='small-btn' onclick='return confirm(\"Are you sure you want to delete this file?\")'>
                            <i class='fas fa-trash'></i>
                          </button>"; // Trash can icon
                    echo "</form>";
                    echo "</li>";
                }
            } else {
                echo "<li>No files uploaded.</li>";
            }
            ?>
        </ul>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>
