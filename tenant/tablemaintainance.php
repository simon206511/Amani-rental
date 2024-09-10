<?php
include 'tenantd.php';

// Fetch logged-in tenant's ID
$tenant_id = $_SESSION['tenant_id'];

// Check if connection is valid
if ($conn->ping()) {
    // Query to fetch maintenance requests for the logged-in tenant
    $query = "SELECT * FROM maintenance_requests WHERE tenant_id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("s", $tenant_id);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        die("Failed to prepare the statement: " . $conn->error);
    }
} else {
    die("Connection is not valid.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Report</title>
    <link rel="stylesheet" href="styletable.css">
    <link rel="stylesheet" href="responsive.css">
</head>
<body>
    <div class="report-container">
        <div class="report-header">
            <h1 class="recent-Maintenance">Recent Maintenance</h1>
            <button class="view" onclick="window.location.href='maintainance.php'">Add Maintenance</button>
        </div>

        <div class="report-body">
            <div class="report-topic-heading">
                <h3 class="t-op">Tenant ID</h3>
                <h3 class="t-op">House Number</h3>
                <h3 class="t-op">Description</h3>
                <h3 class="t-op">Status</h3>
            </div>

            <div class="items">
                <?php while ($row = $result->fetch_assoc()): ?>
                <div class="item1">
                    <h3 class="t-op-nextlvl"><?php echo htmlspecialchars($row['tenant_id']); ?></h3>
                    <h3 class="t-op-nextlvl"><?php echo htmlspecialchars($row['house_number']); ?></h3>
                    <h3 class="t-op-nextlvl"><?php echo htmlspecialchars($row['description']); ?></h3>
                    <h3 class="t-op-nextlvl label-tag"><?php echo htmlspecialchars($row['status']); ?></h3>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>

<?php
// Clean up
$stmt->close();
$conn->close();
?>
