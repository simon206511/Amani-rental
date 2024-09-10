<?php
include 'tenantd.php';

// Fetch logged-in tenant's ID
$tenant_id = $_SESSION['tenant_id'];

// Check if connection is valid
if ($conn->ping()) {
    // Query to fetch invoices for the logged-in tenant
    $query = "SELECT * FROM invoice WHERE tenant_id = ?";
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
    <title>Invoice Report</title>
    <link rel="stylesheet" href="styletable.css">
    <link rel="stylesheet" href="responsive.css">
</head>
<body>
    <div class="report-container">
        <div class="report-header">
            <h1 class="recent-Invoices">Recent Invoices</h1>
            <button class="view" onclick="window.location.href='view_invoice.php'">view Invoice</button>
        </div>

        <div class="report-body">
            <div class="report-topic-heading">
                <h3 class="t-op">Invoice No</h3>
                <h3 class="t-op">Date Issued</h3>
                <h3 class="t-op">Rent Amount</h3>
                <h3 class="t-op">Total Amount</h3>
                <h3 class="t-op">View</h3>
            </div>

            <div class="items">
                <?php while ($row = $result->fetch_assoc()): ?>
                <div class="item1">
                    <h3 class="t-op-nextlvl"><?php echo htmlspecialchars($row['invoice_no']); ?></h3>
                    <h3 class="t-op-nextlvl"><?php echo htmlspecialchars($row['invoice_date']); ?></h3>
                    <h3 class="t-op-nextlvl"><?php echo htmlspecialchars($row['rent_amount']); ?></h3>
                    <h3 class="t-op-nextlvl"><?php echo htmlspecialchars($row['total_amount']); ?></h3>
                    <a href="view_invoice.php?id=<?php echo urlencode($row['id']); ?>" class="view-details">View</a>
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
