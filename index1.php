<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$rows_per_page = isset($_GET['rows_per_page']) ? (int)$_GET['rows_per_page'] : 5; // Default to 5 rows per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $rows_per_page;

// Initialize search variables
$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_sql = '';
if (!empty($search)) {
    $search_sql = "WHERE event_name LIKE '%$search%' OR description LIKE '%$search%' OR event_date LIKE '%$search%' OR program_info LIKE '%$search%' OR status LIKE '%$search%'";
}

// Get the total number of rows
$total_sql = "SELECT COUNT(*) FROM event $search_sql";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_row();
$total_rows = $total_row[0];

$total_pages = ceil($total_rows / $rows_per_page);

// Fetch the rows for the current page
$sql = "SELECT * FROM event $search_sql LIMIT $offset, $rows_per_page";
$result = $conn->query($sql);

// Get status counts for the chart
$status_sql = "SELECT status, COUNT(*) as count FROM event GROUP BY status";
$status_result = $conn->query($status_sql);
$status_data = [];
while ($row = $status_result->fetch_assoc()) {
    $status_data[$row['status']] = $row['count'];
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
        .dropdown-container {
            margin-bottom: 20px;
        }

        .dropdown-container select {
            padding: 5px;
        }

        .chart-container {
            width: 50%;
            margin: 20px auto;
        }
		 
        .button-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .button-container button {
            padding: 10px 20px; /* Adjust padding as needed for button size */
            font-size: 14px; /* Adjust font size if necessary */
        }

        /* Additional styles for other elements */
        .left-side {
            flex: 1;
            margin-right: 20px;
        }

        .right-side {
            flex: 1;
        }

        @media (max-width: 768px) {
            .content {
                flex-direction: column;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body><div>
    <div class="navbar">
        <div class="menu">
            <a href="index.php">Events</a>
            <a href="attendance.php">Attendance</a>
            <a href="about.php">About Us</a>
        </div>
        <div class="logout">
            <img src="ssc logo.png" alt="User Image">
            <a href="login.php">Logout</a>
        </div>
    </div>

    <div class="content">
        <h1>Events</h1>
        <div class="dropdown-container">
            <form action="index.php" method="GET">
                <label for="rows_per_page">Rows per page:</label> 
                <select name="rows_per_page" id="rows_per_page" onchange="this.form.submit()">
                    <option value="1" <?php if ($rows_per_page == 1) echo 'selected'; ?>>1</option>
                    <option value="5" <?php if ($rows_per_page == 5) echo 'selected'; ?>>5</option>
                    <option value="10" <?php if ($rows_per_page == 10) echo 'selected'; ?>>10</option>
                </select> <br>
                <input type="hidden" name="page" value="1"> <!-- Reset to page 1 on rows per page change -->
                <input type="text" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Search</button>
            </form>
        </div>
        <div class="table-container">
	        <button onclick="window.print()">Print Events</button>
            <button class="add-event-button" onclick="window.location.href='add_event.php'">Add Event</button>
           </div>
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
                    if ($result->num_rows > 0) {
                        $i = $offset + 1;
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
                                    <a href='delete_event.php?id=" . $row['id'] . "' onclick=\"return confirm('Are you sure you want to delete this event?')\">Delete</a>
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
        <div class="pagination">
            <?php
            if ($page > 1) {
                echo '<a href="?page=' . ($page - 1) . '&rows_per_page=' . $rows_per_page . '&search=' . urlencode($search) . '">&laquo;</a>';
            }
            for ($i = 1; $i <= $total_pages; $i++) {
                echo '<a href="?page=' . $i . '&rows_per_page=' . $rows_per_page . '&search=' . urlencode($search) . '"' . ($i == $page ? ' class="active"' : '') . '>' . $i . '</a>';
            }
            if ($page < $total_pages) {
                echo '<a href="?page=' . ($page + 1) . '&rows_per_page=' . $rows_per_page . '&search=' . urlencode($search) . '">&raquo;</a>';
            }
            ?>
        </div>
        <div class="chart-container">
            <canvas id="statusChart"></canvas>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusData = <?php echo json_encode($status_data); ?>;
            const ctx = document.getElementById('statusChart').getContext('2d');
            const statusChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: Object.keys(statusData),
                    datasets: [{
                        label: 'Event Status',
                        data: Object.values(statusData),
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
