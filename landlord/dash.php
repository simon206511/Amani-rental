<?php 
include 'landlorddash.php';
include 'config.php'; // Ensure your database connection is established

// Query to get total maintenance requests
$totalRequestsQuery = "SELECT COUNT(*) as total FROM maintenance_requests";
$totalRequestsResult = $conn->query($totalRequestsQuery);
$totalRequestsRow = $totalRequestsResult->fetch_assoc();
$totalRequests = $totalRequestsRow['total'];

// Query to get completed maintenance requests
$completedRequestsQuery = "SELECT COUNT(*) as completed FROM maintenance_requests WHERE status = 'Completed'";
$completedRequestsResult = $conn->query($completedRequestsQuery);
$completedRequestsRow = $completedRequestsResult->fetch_assoc();
$completedRequests = $completedRequestsRow['completed'];

// Query to get pending maintenance requests
$pendingRequestsQuery = "SELECT COUNT(*) as pending FROM maintenance_requests WHERE status = 'Pending'";
$pendingRequestsResult = $conn->query($pendingRequestsQuery);
$pendingRequestsRow = $pendingRequestsResult->fetch_assoc();
$pendingRequests = $pendingRequestsRow['pending'];

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="resources/css/maintain.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <title>Maintenance Dashboard</title>
</head>
<body>
    <!-- Box container section -->
    <div class="boxes">
        <div class="box box1">
            <i class="uil uil-clipboard-notes"></i>
            <span class="text">Total Maintenance Requests</span>
            <span class="number"><?php echo $totalRequests; ?></span>
        </div>
        <div class="box box2">
            <i class="uil uil-check-circle"></i>
            <span class="text">Completed Requests</span>
            <span class="number"><?php echo $completedRequests; ?></span>
        </div>
        <div class="box box3">
            <i class="uil uil-clock"></i>
            <span class="text">Pending Requests</span>
            <span class="number"><?php echo $pendingRequests; ?></span>
        </div>
    </div>
</body>
</html>
<?php include 'maintainances.php'; ?>
</html>