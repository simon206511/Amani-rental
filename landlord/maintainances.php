<?php
// Include database connection
include 'config.php';

// Fetch maintenance requests from the database
$sql = "SELECT * FROM maintenance_requests"; // Adjust the query based on your actual table structure
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Report</title>
    <link rel="stylesheet" href="resources/css/styletable.css">
    <link rel="stylesheet" href="responsive.css">
</head>
<body>
    <div class="report-container">
    <div class="report-header">
    <h1 class="recent-Maintenance">Recent Maintenance</h1>
    </div>
        <div class="report-body">
            <div class="report-topic-heading">
                <h3 class="t-op">Tenant ID</h3>
                <h3 class="t-op">House Number</h3>
                <h3 class="t-op">Description</h3>
                <h3 class="t-op">Status</h3>
                <h3 class="t-op">Actions</h3>
            </div>

            <div class="items">
                <?php while ($row = $result->fetch_assoc()): ?>
                <div class="item1">
                    <h3 class="t-op-nextlvl"><?php echo htmlspecialchars($row['tenant_id']); ?></h3>
                    <h3 class="t-op-nextlvl"><?php echo htmlspecialchars($row['house_number']); ?></h3>
                    <h3 class="t-op-nextlvl"><?php echo htmlspecialchars($row['description']); ?></h3>
                    <h3 class="t-op-nextlvl label-tag"><?php echo htmlspecialchars($row['status']); ?></h3>
                    <div class="actions">
                        <?php if ($row['status'] === 'Pending'): ?>
                            <a href="resolve_maintenance.php?id=<?php echo urlencode($row['id']); ?>" class="action-button">Resolve</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>

<?php
// Clean up
$conn->close();
?>
