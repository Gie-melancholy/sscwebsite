<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supreme Student Council</title>
  
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }

        .navbar {
            background-color: #2C3E50;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar a {
            color: #ECF0F1;
            text-decoration: none;
            padding: 10px;
        }

        .navbar a:hover {
            background-color: #34495E;
        }

        .navbar img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .content {
            max-width: 1000px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333333;
        }

        .table-container {
            margin: 20px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #2C3E50;
            color: #ffffff;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination a {
            color: #2C3E50;
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            margin: 0 4px;
            border-radius: 4px;
        }

        .pagination a.active {
            background-color: #2C3E50;
            color: #ffffff;
            border: 1px solid #2C3E50;
        }

        .pagination a:hover {
            background-color: #ddd;
        }

        .add-event-form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .add-event-form h2 {
            color: #333333;
        }

        .add-event-form label {
            display: block;
            margin-bottom: 8px;
            color: #333333;
        }

        .add-event-form input,
        .add-event-form textarea {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .add-event-form button {
            background-color: #2980B9;
            color: #ffffff;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .add-event-form button:hover {
            background-color: #005299;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="menu">
            <a href="#">Events</a>
            <a href="#">Attendance</a>
            <a href="#">About Us</a>
        </div>
        <div class="logout">
            <a href="#">Logout</a>
            <img src="path/to/your/image.png" alt="User Image">
        </div>
    </div>

    <div class="content">
        <h1>Events</h1>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Events</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Program Info</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
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

                    $sql = "SELECT * FROM events";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . $row["id"] . "</td>
                                    <td>" . $row["event_name"] . "</td>
                                    <td>" . $row["description"] . "</td>
                                    <td>" . $row["event_date"] . "</td>
                                    <td><a href='#'>View</a></td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No events found</td></tr>";
                    }

                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
        <div class="pagination">
            <a href="#">&laquo;</a>
            <a href="#" class="active">1</a>
            <a href="#">2</a>
            <a href="#">3</a>
            <a href="#">4</a>
            <a href="#">5</a>
            <a href="#">6</a>
            <a href="#">&raquo;</a>
        </div>

        <div class="add-event-form">
            <h2>Add New Event</h2>
            <form method="POST" action="add_event.php">
                <label for="event_name">Event Name:</label>
                <input type="text" id="event_name" name="event_name" required>

                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" required></textarea>

                <label for="event_date">Date:</label>
                <input type="date" id="event_date" name="event_date" required>

                <label for="program_info">Program Info:</label>
                <input type="text" id="program_info" name="program_info" required>

                <button type="submit">Add Event</button>
            </form>
        </div>
    </div>
</body>
</html>
