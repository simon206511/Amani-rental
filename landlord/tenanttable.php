<?php
include'landlorddash.php';
// Include the database configuration file
include 'config.php'; 

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amani_db";

// Create a new database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if connection is valid
if ($conn->ping()) {
    // Query to fetch all tenant details
    $query = "SELECT tenant_id, full_name, house_number, rent FROM tenants";
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
    <title>Tenant Details</title>
    <link rel="stylesheet" href="resources/css/styletable.css"> 
</head>
<body>
    
        <div class="report-container">
        <div class="report-header">
            <h1 class="recent-Maintenance">Tenant Details</h1>
            <button class="view" onclick="window.location.href='tenant.php'">Add Tenants</button>
        </div>
        <div class="report-body">
            <div class="report-topic-heading">
                <h3 class="t-op">Tenant ID</h3>
                <h3 class="t-op">Name</h3>
                <h3 class="t-op">House Number</h3>
                <h3 class="t-op">Rent</h3>
                <h3 class="t-op">Actions</h3>
            </div>

            <div class="items">
                <?php
                include 'config.php';

                $sql = "SELECT tenant_id, full_name, house_number, rent FROM tenants";
                $result = $conn->query($sql);
                if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="item1">
                            <h3 class="t-op-nextlvl"><?php echo htmlspecialchars($row['tenant_id']); ?></h3>
                            <h3 class="t-op-nextlvl"><?php echo htmlspecialchars($row['full_name']); ?></h3>
                            <h3 class="t-op-nextlvl"><?php echo htmlspecialchars($row['house_number']); ?></h3>
                            <h3 class="t-op-nextlvl"><?php echo htmlspecialchars($row['rent']); ?></h3>
                            <div class="actions">
                                <a href="edit_tenant.php?id=<?php echo urlencode($row['tenant_id']); ?>" class="action-button">Edit</a>
                                <a href="delete_tenant.php?id=<?php echo urlencode($row['tenant_id']); ?>" class="action-button">Delete</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="item1">
                        <h3 class="t-op-nextlvl" colspan="5">No tenants found.</h3>
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
