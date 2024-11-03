<?php
include 'auth_check.php';
include 'db.php';

// Function to get total number of rows
function getTotalRows($conn, $search) {
    $search_sql = '';
    $params = [];
    $param_types = '';

    if (!empty($search)) {
        $search_sql = "WHERE event_name LIKE ? OR description LIKE ? OR event_date LIKE ? OR program_info LIKE ? OR status LIKE ?";
        $search_term = '%' . $search . '%';
        $params = [$search_term, $search_term, $search_term, $search_term, $search_term];
        $param_types = 'sssss'; // 5 string parameters
    }

    $total_sql = "SELECT COUNT(*) FROM event $search_sql";
    $stmt = $conn->prepare($total_sql);

    if (!empty($search)) {
        $stmt->bind_param($param_types, ...$params);
    }

    $stmt->execute();
    $total_result = $stmt->get_result();
    $total_row = $total_result->fetch_row();
    $stmt->close();

    return $total_row[0];
}


function fetchRows($conn, $search, $rows_per_page, $page) {
    $offset = ($page - 1) * $rows_per_page;
    $search_sql = '';
    $params = [];
    $param_types = '';

    if (!empty($search)) {
        $search_sql = "WHERE event_name LIKE ? OR description LIKE ? OR event_date LIKE ? OR program_info LIKE ? OR status LIKE ?";
        $search_term = '%' . $search . '%';
        $params = [$search_term, $search_term, $search_term, $search_term, $search_term];
        $param_types = 'sssss'; // 5 string parameters
    }

    $sql = "SELECT * FROM event $search_sql LIMIT ?, ?";
    $stmt = $conn->prepare($sql);

    if (!empty($search)) {
        // Append the types for LIMIT and OFFSET
        $param_types .= 'ii';
        // Combine the parameters
        $combined_params = array_merge($params, [$offset, $rows_per_page]);
        $stmt->bind_param($param_types, ...$combined_params);
    } else {
        $stmt->bind_param('ii', $offset, $rows_per_page);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}


// Function to get status counts for the chart
function getStatusData($conn) {
    $status_sql = "SELECT status, COUNT(*) as count FROM event GROUP BY status";
    $status_result = $conn->query($status_sql);
    $status_data = [];
    while ($row = $status_result->fetch_assoc()) {
        $status_data[$row['status']] = $row['count'];
    }
    return $status_data;
}

// Default values
$rows_per_page = isset($_GET['rows_per_page']) ? (int)$_GET['rows_per_page'] : 5; // Default to 5 rows per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';

$total_rows = getTotalRows($conn, $search);
$total_pages = ceil($total_rows / $rows_per_page);

$result = fetchRows($conn, $search, $rows_per_page, $page);
$status_data = getStatusData($conn);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supreme Student Council</title>
    <link rel="stylesheet" href="all.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
.search-and-buttons {
    display: flex;
    justify-content: space-between; /* Space between the button group and the search form */
   
    margin-bottom: 0; /* Space below the container */
}

.button-group {
    display: flex;
    gap: 6px; /* Space between the buttons */
}

.search-container {
    display: flex;
    align-items: center;
}

.search-bar {
    height: 50px; /* Adjust height to 50px */
    width: 600px; /* Keep width as 600px */
    box-sizing: border-box; /* Include padding in total width */
    
    font-size: 16px; /* Adjust font size if needed */
    margin-bottom: 10px;
}

.search-button, .clear-button {
    height: 40px; /* Height of the buttons */
    padding: 10px 30px; /* Padding inside the buttons */
    border: none;
    border-radius: 4px;
    background-color: #2C3E50;
    color: #ffffff;
    font-size: 16px;
    cursor: pointer;
    width: 110px; /* Width of the buttons */
}

.search-button:hover, .clear-button:hover {
    background-color: #34495E;
}

.clear-button {
    background-color: #E74C3C; /* Background color for clear button */
}

.clear-button:hover {
    background-color: #C0392B;
}

.add-event-button {
    height: 40px; /* Match height of search bar */
    padding: 10px 30px; /* Adjust padding for button */
    border: none;
    border-radius: 4px;
    background-color: #2C3E50;
    color: #ffffff;
    font-size: 16px;
    cursor: pointer;
    margin-bottom: 20px;
    width: 200px;
}

.print-button {
    height: 40px; /* Match height of search bar */
    padding: 10px 40px; /* Adjust padding for buttons */
    border: none;
    border-radius: 4px;
    background-color: #2C3E50;
    color: #ffffff;
    font-size: 16px;
    cursor: pointer;
    margin-bottom: 81px; /* Space between buttons */
    width: 180px;
}

.add-event-button:hover, .print-button:hover {
    background-color: #34495E;
}
    </style>
</head>
<body>
    <div>
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

        <div class="content printable">
    <h1>Events</h1>
    <div class="search-and-buttons">
        <form action="index.php" method="GET" class="search-form">
            <input type="text" name="search" id="search" class="search-bar" placeholder="Search..." value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
            <div class="button-group">
                <button type="submit" class="search-button">Search</button>
                <button type="button" class="clear-button" onclick="clearSearch()">Clear</button>
            </div>
        </form>
        <button class="print-button" onclick="window.print()">Print</button>
        <button class="add-event-button" onclick="window.location.href='add_event.php'">Add Event</button>
    </div>

            <?php if ($total_rows > 0): ?>
            <p>Total records found: <?php echo $total_rows; ?></p>
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Events</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Venue</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = ($page - 1) * $rows_per_page + 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $i++ . "</td>";
                        echo "<td>" . htmlspecialchars(ucwords($row['event_name']), ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>" . htmlspecialchars(ucwords($row['description']), ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>" . htmlspecialchars($row['event_date'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>" . htmlspecialchars(ucwords($row['program_info']), ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>" . htmlspecialchars(ucwords($row['status']), ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td class='actions'>
                                <a href='edit_event.php?id=" . urlencode($row['id']) . "'>Edit</a>
                                <a href='delete_event.php?id=" . urlencode($row['id']) . "' onclick=\"return confirm('Are you sure you want to delete this event?')\">Delete</a>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <?php else: ?>
            <p>No events found.</p>
            <?php endif; ?>

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
    <script>
    function clearSearch() {
        document.getElementById('search').value = ''; // Clear the search input
        document.querySelector('form').submit(); // Submit the form to refresh the page
    }
</script>

</body>
</html>
