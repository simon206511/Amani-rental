<?php
include 'landlorddash.php';
// Include the database configuration file
include 'config.php'; 

// Create a new database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if connection is valid
if ($conn->ping()) {
    // Query to fetch all invoice details
    $query = "SELECT invoice_no, invoice_date, tenant_id, rent_amount, water_amount, garbage_amount, electricity_amount, total_amount FROM invoice";
    $result = $conn->query($query);

    if (!$result) {
        die("Query failed: " . $conn->error);
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
    <title>Invoice Details</title>
    <link rel="stylesheet" href="resources/css/styletable.css"> 
</head>
<body>
    
    <div class="report-container">
        <div class="report-header">
            <h1 class="recent-Maintenance">Invoice Details</h1>
            <button class="view" onclick="window.location.href='invoicecreation.php'">Create Invoice</button>
        </div>
        <div class="report-body">
            <div class="report-topic-heading">
                <h3 class="t-op">Invoice No</h3>
                <h3 class="t-op">Tenant ID</h3>
                <h3 class="t-op">Rent</h3>
                <h3 class="t-op">Total</h3>
                <h3 class="t-op">Actions</h3>
            </div>

            <div class="items">
                <?php
                $sql = "SELECT invoice_no, invoice_date, tenant_id, rent_amount, water_amount, garbage_amount, electricity_amount, total_amount FROM invoice";
                $result = $conn->query($sql);
                if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="item1">
                            <h3 class="t-op-nextlvl"><?php echo htmlspecialchars($row['invoice_date']); ?></h3>
                            <h3 class="t-op-nextlvl"><?php echo htmlspecialchars($row['tenant_id']); ?></h3>
                            <h3 class="t-op-nextlvl"><?php echo htmlspecialchars($row['rent_amount']); ?></h3>
                            <h3 class="t-op-nextlvl"><?php echo htmlspecialchars($row['total_amount']); ?></h3>
                            <div class="actions">
                                <a href="edit_invoice.php?invoice_no=<?php echo urlencode($row['invoice_no']); ?>" class="action-button">Edit</a>
                                <a href="delete_invoice.php?invoice_no=<?php echo urlencode($row['invoice_no']); ?>" class="action-button">Delete</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="item1">
                        <h3 class="t-op-nextlvl" colspan="9">No invoices found.</h3>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?>
