<?php
include 'auth_check.php';
include 'db.php';

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

$message = isset($_GET['message']) && $_GET['message'] == 'success' ? 'Event successfully added!' : '';
$new_event_id = isset($_GET['new_event_id']) ? (int)$_GET['new_event_id'] : 0;

// Helper function to create SQL for search
function createSearchSQL($search, $search_by, &$params, &$param_types) {
    if (!empty($search)) {
        $search_term = '%' . $search . '%';
        $params = [$search_term, $search_term, $search_term, $search_term, $search_term];
        $param_types = str_repeat('s', count($params));
        switch ($search_by) {
            case '*':
                return "WHERE event_name LIKE ? OR description LIKE ? OR event_date LIKE ? OR program_info LIKE ? OR status LIKE ?";
            case 'event_name':
                $params = [$search_term];
                $param_types = 's';
                return "WHERE event_name LIKE ?";
            case 'description':
                $params = [$search_term];
                $param_types = 's';
                return "WHERE description LIKE ?";
            case 'event_date':
                $params = [$search_term];
                $param_types = 's';
                return "WHERE event_date LIKE ?";
            case 'program_info':
                $params = [$search_term];
                $param_types = 's';
                return "WHERE program_info LIKE ?";
            case 'status':
                $params = [$search_term];
                $param_types = 's';
                return "WHERE status LIKE ?";
            default:
                return '';
        }
    }
    return '';
}

// Function to get total number of rows
function getTotalRows($conn, $search, $search_by) {
    $params = [];
    $param_types = '';
    $search_sql = createSearchSQL($search, $search_by, $params, $param_types);
    $total_sql = "SELECT COUNT(*) FROM event1 $search_sql";
    $stmt = $conn->prepare($total_sql);
    if ($params) $stmt->bind_param($param_types, ...$params);
    $stmt->execute();
    $total_result = $stmt->get_result();
    $total_row = $total_result->fetch_row();
    $stmt->close();
    return $total_row[0];
}

// Function to fetch rows with pagination, search, and sorting
function fetchRows($conn, $search, $search_by, $rows_per_page, $page, $sort_column, $sort_order) {
    $offset = ($page - 1) * $rows_per_page;
    $params = [];
    $param_types = '';
    $search_sql = createSearchSQL($search, $search_by, $params, $param_types);
    $sql = "SELECT * FROM event1 $search_sql ORDER BY $sort_column $sort_order LIMIT ?, ?";
    $stmt = $conn->prepare($sql);
    if ($params) {
        $params[] = $offset;
        $params[] = $rows_per_page;
        $param_types .= 'ii';
        $stmt->bind_param($param_types, ...$params);
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
    $status_sql = "SELECT status, COUNT(*) as count FROM event1 GROUP BY status";
    $status_result = $conn->query($status_sql);
    $status_data = [];
    while ($row = $status_result->fetch_assoc()) {
        $status_data[$row['status']] = $row['count'];
    }
    return $status_data;
}

// Default values
$rows_per_page = isset($_GET['rows_per_page']) ? (int)$_GET['rows_per_page'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_by = isset($_GET['search_by']) ? $_GET['search_by'] : 'event_name'; // Default search by event_name
$sort_column = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'event_name';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'DESC';

// Get total number of rows and calculate total pages
$total_rows = getTotalRows($conn, $search, $search_by);
$total_pages = ceil($total_rows / $rows_per_page);

// Fetch rows for the current page
$result = fetchRows($conn, $search, $search_by, $rows_per_page, $page, $sort_column, $sort_order);

// Get data for the status chart
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
	
        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin: 10px 0 5px;
            color: #333333;
        }

        .search-and-buttons, .button-group, .search-container {
            display: flex;
            gap: 4px;
        }

        .search-button, .clear-button, .rows-per-page, .add-event-button, .print-button {
            height: 35px;
            padding: 10px;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            margin-top: 0;
            width: 160px;
        }

        .search-bar {
            width: 550px;
            height: 15px;
            padding: 10px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            margin-bottom: 10px;
        }

        .add-event-button, .print-button {
            background-color: #2C3E50;
            color: #ffffff;
            width: 200px;
            height: 35px;
            margin-top: 30px;
        }

        .search-button:hover, .add-event-button:hover, .print-button:hover {
            background-color: #34495E;
        }

        .clear-button {
            background-color: #2C3E50;
            width: 160px;
        }

        .clear-button:hover {
            background-color: #C0392B;
        }

        .rows-per-page {
            background-color: #2C3E50;
            color: white;
            width: 160px;
        }

        .rows-per-page option {
            background-color: #fff;
            color: #000;
        }

        .sortable:hover {
            cursor: pointer;
        }

        .sort-arrows {
            display: inline-block;
            vertical-align: middle;
        }

        .sort-arrows a {
            text-decoration: none;
            color: inherit;
        }
        /* Your existing styles */

/* Hide the username in the normal view */
.print-username {
    display: none;
    
}

/* Show the username in the print view */
@media print {
    .print-username {
        display: block;
        padding: 10px;
        margin-top: 10px;
        font-size: 14px;
        font-weight: bold;
        text-align: right;
        background-color: #f5f5f5;
        border: 2px solid #ccc;
       
        
    }

    /* Hide elements not needed in print view */
    .chart-container, .search-and-buttons, .pagination, .actions, p, .navbar, .content .printable button {
        display: none;
    }
}

        
    </style>
</head>
<body>
    <div>
        <div class="navbar">
            <div class="menu">
                <a href="index.php">Events</a>
                <a href="attendance.php">Files</a>
                <a href="about.php">About Us</a>
            </div>
            <div class="logout">
                <img src="ssc logo.png" alt="User Image"> 
               
                <p><a href="logout.php">Logout</a></p>
                <p><a href="set.php">Settings</a></p>
            </div>
        </div>
       
        <div class="content printable">
            <h1>Events</h1>
            <div class="search-and-buttons">
                <form action="index.php" method="GET" class="search-form">
                    <div class="dropdown-container">
                        <label for="search_by"></label>
                        <select name="search_by" id="search_by" class="search-dropdown" onchange="this.form.submit()">
                            <option value="*" <?php echo $search_by == '*' ? 'selected' : ''; ?>>All</option>
                            <option value="event_name" <?php echo $search_by == 'event_name' ? 'selected' : ''; ?>>Events</option>
                            <option value="description" <?php echo $search_by == 'description' ? 'selected' : ''; ?>>Description</option>
                            <option value="event_date" <?php echo $search_by == 'event_date' ? 'selected' : ''; ?>>Date</option>
                            <option value="program_info" <?php echo $search_by == 'program_info' ? 'selected' : ''; ?>>Venue</option>
                            <option value="status" <?php echo $search_by == 'status' ? 'selected' : ''; ?>>Status</option>
                        </select>
                    </div>
                    <input type="text" name="search" id="search" class="search-bar" placeholder="Search..." value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="button-group">
                        <button type="submit" class="search-button">Search</button>
                        <button type="button" class="clear-button" onclick="clearSearch()">Clear</button>
                        <select name="rows_per_page" class="rows-per-page" onchange="this.form.submit()">
                            <?php foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 20, 30] as $option): ?>
                                <option value='<?php echo $option; ?>'<?php echo $option == $rows_per_page ? ' selected' : ''; ?>><?php echo $option; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <input type="hidden" name="sort_column" value="<?php echo htmlspecialchars($sort_column, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="sort_order" value="<?php echo htmlspecialchars($sort_order, ENT_QUOTES, 'UTF-8'); ?>">
                </form>               
                <button class="print-button" onclick="window.print()">Print</button> 
                <button class="add-event-button" onclick="window.location.href='add_event.php'">Add Event</button>
            </div>
            <div class="print-username">
            Printed By: <?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>
            </div>
    
            <?php if ($total_rows > 0): ?>
                <p>Results: <?php echo $total_rows;?></p>
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <?php foreach (['event_name' => 'Events', 'description' => 'Description', 'event_date' => 'Date', 'program_info' => 'Venue', 'status' => 'Status'] as $col => $label): ?>
                                <th class="sortable">
                                    <?php echo $label; ?>
                                    <span class="sort-arrows">
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['sort_column' => $col, 'sort_order' => 'ASC'])); ?>">▲</a>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['sort_column' => $col, 'sort_order' => 'DESC'])); ?>">▼</a>
                                    </span>
                                </th>
                            <?php endforeach; ?>
                            <th class="actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = ($page - 1) * $rows_per_page + 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $i++ . "</td>";
                            foreach (['event_name', 'description', 'event_date', 'program_info', 'status'] as $field) {
                                echo "<td>" . htmlspecialchars($row[$field], ENT_QUOTES, 'UTF-8') . "</td>";
                            }
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
                    echo '<a href="?page=' . ($page - 1) . '&rows_per_page=' . $rows_per_page . '&search=' . urlencode($search) . '&search_by=' . urlencode($search_by) . '&sort_column=' . urlencode($sort_column) . '&sort_order=' . urlencode($sort_order) . '">&laquo;</a>';
                }
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo '<a href="?page=' . $i . '&rows_per_page=' . $rows_per_page . '&search=' . urlencode($search) . '&search_by=' . urlencode($search_by) . '&sort_column=' . urlencode($sort_column) . '&sort_order=' . urlencode($sort_order) . '"' . ($i == $page ? ' class="active"' : '') . '>' . $i . '</a>';
                }
                if ($page < $total_pages) {
                    echo '<a href="?page=' . ($page + 1) . '&rows_per_page=' . $rows_per_page . '&search=' . urlencode($search) . '&search_by=' . urlencode($search_by) . '&sort_column=' . urlencode($sort_column) . '&sort_order=' . urlencode($sort_order) . '">&raquo;</a>';
                }
                ?>
            </div>

            
        </div>
    </div>
     <div class="chart-container">
                <canvas id="statusChart"></canvas>
            </div>
    <!-- Modal -->
    <div id="messageModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <p id="modalMessage"><?php echo $message; ?></p>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusData = <?php echo json_encode($status_data); ?>;
            const ctx = document.getElementById('statusChart').getContext('2d');
            new Chart(ctx, {
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

            // Get the modal
            var modal = document.getElementById('messageModal');
            // Get the <span> element that closes the modal
            var closeButton = document.querySelector('.close-button');
            // Check if there's a message parameter in the URL
            var urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('message') === 'success') {
                modal.style.display = 'block'; // Show the modal
            }
            // When the user clicks on <span> (x), close the modal
            closeButton.onclick = function() {
                modal.style.display = 'none';
            }
            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }
        });

        function clearSearch() {
            document.getElementById('search').value = '';
            document.querySelector('form').submit();
        }
    </script>
</body>
</html>
