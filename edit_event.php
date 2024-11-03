<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link rel="stylesheet" href="all.css"> 
    <script>
        function confirmEdit() {
            return confirm('Are you sure you want to edit this event?');
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

    <div class="content">
        <h1>Edit Event</h1>
        <a href="index.php" class="button" style="margin-bottom: 20px;">Back</a>
        <form method="POST" action="" onsubmit="return confirmEdit();">
            <?php include 'auth_check.php';
            include 'db.php';

            // Fetch event details if ID is provided
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $sql = "SELECT * FROM event WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                // Ensure status is initialized
                $status = $row['status'];
            }

            // Handle form submission
            $success_message = "";
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $id = $_POST['id'];
                $event_name = $_POST['event_name'];
                $description = $_POST['description'];
                $event_date = $_POST['event_date'];
                $program_info = $_POST['program_info'];
                $status = $_POST['status']; // Capture status from form

                $sql = "UPDATE event SET event_name=?, description=?, event_date=?, program_info=?, status=? WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('sssssi', $event_name, $description, $event_date, $program_info, $status, $id);

                if ($stmt->execute()) {
                    $success_message = "Event updated successfully!";
                    echo "<script>
                            alert('$success_message');
                            window.location.href = 'index.php';
                          </script>";
                    exit();
                } else {
                    echo "Error updating event: " . $conn->error;
                }
            }

            $conn->close();
            ?>

            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
            
            <label for="event_name">Event Name:</label>
            <input type="text" id="event_name" name="event_name" value="<?php echo $row['event_name']; ?>" required>
            
            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" required><?php echo $row['description']; ?></textarea>
            
            <label for="event_date">Date:</label>
            <input type="date" id="event_date" name="event_date" value="<?php echo $row['event_date']; ?>" required>
            
            <label for="program_info">Venue:</label>
            <input type="text" id="program_info" name="program_info" value="<?php echo $row['program_info']; ?>" required>
            
            <label for="status">Status:</label>
            <select name="status" id="status">
                <option value="Incoming" <?php if ($status == 'Incoming') echo 'selected'; ?>>Incoming</option>
                <option value="Ongoing" <?php if ($status == 'Ongoing') echo 'selected'; ?>>Ongoing</option>
                <option value="Done" <?php if ($status == 'Done') echo 'selected'; ?>>Done</option>
            </select>
            <br>
            <button type="submit">Update Event</button>
        </form>
    </div>
</body>
</html>
