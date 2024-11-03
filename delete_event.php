<?php include 'auth_check.php';

$servername = "localhost";
$username = "root"; // Change if necessary
$password = "";     // Change if necessary
$dbname = "ssc_website";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare and bind
    $stmt = $conn->prepare("DELETE FROM event WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $success_message = "Event deleted successfully!";
        echo "<script>
                alert('$success_message');
                window.location.href = 'index.php';
              </script>";
        exit();
    } else {
        echo "Error deleting event: " . $conn->error;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supreme Student Council</title>
    <link rel="stylesheet" href="all.css">
    <style>
        /* Optional CSS for notification */
        .success-message {
            background-color: #d4edda; /* Green background */
            color: #155724; /* Dark green text */
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #c3e6cb; /* Light green border */
            border-radius: 4px;
        }
    </style>
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
            <p><a href="logout.php">Logout</a></p>
        </div>
    </div>

    <div class="content">
        <h1>Events</h1>

        <!-- PHP for displaying success message -->
        <?php
        if (isset($_SESSION['delete_success'])) {
            echo '<div class="success-message">' . $_SESSION['delete_success'] . '</div>';
            unset($_SESSION['delete_success']); // Clear the session variable after displaying
        }
        ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Events</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Program Info</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetching events from database
                    $sql = "SELECT * FROM event";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        $i = 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $i++ . "</td>";
                            echo "<td>" . $row['event_name'] . "</td>";
                            echo "<td>" . $row['description'] . "</td>";
                            echo "<td>" . $row['event_date'] . "</td>";
                            echo "<td>" . $row['program_info'] . "</td>";
                            echo "<td>" . $row['status'] . "</td>";
                            echo "<td class='actions'>
                                    <a href='edit_event.php?id=" . $row['id'] . "'>Edit</a>
                                    <a href='delete_event.php?id=" . $row['id'] . "' onclick=\"return confirmDelete(event)\">Delete</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No events found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Optional: JavaScript or Chart.js integration -->
    <script>
        function confirmDelete(event) {
            if (!confirm('Are you sure you want to delete this event?')) {
                event.preventDefault(); // Cancel the default action (navigation)
            }
        }
    </script>
</body>
</html>
