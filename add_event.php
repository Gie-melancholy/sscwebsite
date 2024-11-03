<?php
include 'auth_check.php';
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_name = $_POST['event_name'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $program_info = $_POST['program_info'];
    $status = $_POST['status'];

    // Use prepared statements to prevent SQL injection
    $sql = "INSERT INTO event1 (event_name, description, event_date, program_info, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $event_name, $description, $event_date, $program_info, $status);

    if ($stmt->execute()) {
        // Redirect to the main page with a success message and the ID of the new event
        $new_event_id = $stmt->insert_id;
        header("Location: index.php?message=success&new_event_id=$new_event_id");
        exit();
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event</title>
    <link rel="stylesheet" href="all.css">
    <style>
        /* Your existing CSS styles */
    </style>
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
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <div class="content">
        <h1>Add Event</h1>
        <a href="index.php" class="button" style="margin-bottom: 20px;">Back</a><hr>
        <form method="POST" action="add_event.php">
            <label for="event_name">Event Name:</label>
            <input type="text" id="event_name" name="event_name" required>
            
            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" required></textarea>
            
            <label for="event_date">Date:</label>
            <input type="date" id="event_date" name="event_date" required>
            
            <label for="program_info">Venue:</label>
            <input type="text" id="program_info" name="program_info" required>
            
            <label for="status">Status:</label>
            <select name="status" id="status">
                <option value="Incoming">Incoming</option>
                <option value="Ongoing">Ongoing</option>
                <option value="Done">Done</option>
            </select>
            <br>
            <button type="submit">Add Event</button>
        </form>
    </div>
</body>
</html>
