<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['tenant_id'])) {
    header("Location: tenantlogin.php"); // Redirect to login page if not logged in
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'amani_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch tenant data
$tenantId = $_SESSION['tenant_id'];
$sql = "SELECT full_name FROM tenants WHERE tenant_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $tenantId);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

$tenantName = $result ? $result['full_name'] : 'Tenant';

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style2.css">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    
    <title>Dashboard</title>
</head>
<body>
    <nav class="sidebar">
        <header>
            <div class="image-text">
                <span class="image">
                    <img src="amani-logo.png" alt="">
                </span>

                <div class="text logo-text">
                    <span class="name">Amani Nyumba</span>
                    <span class="profession">Rental Management</span>
                </div>
            </div>
        </header>

        <div class="menu-bar">
            <div class="menu">
                <ul class="menu-links">
                    <li class="nav-link">
                        <a href="dashboard.php" id="dashboard-link">
                            <i class='bx bx-home-alt icon'></i>
                            <span class="text nav-text">Dashboard</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="tablemaintainance.php">
                            <i class='bx bx-wrench icon'></i>
                            <span class="text nav-text">Maintainance</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="tenantinvoice.php">
                            <i class='bx bx-wallet-alt icon'></i>
                            <span class="text nav-text">Invoices</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="bottom-content">
                <li class="">
                    <a href="logout.php">
                        <i class='bx bx-log-out icon'></i>
                        <span class="text nav-text">Logout</span>
                    </a>
                </li>
            </div>
        </div>
    </nav>

    <nav class="top-bar">
        <div class="sidebar-button">
            <i class='bx bx-menu'></i>
            <span class="dashboard">Dashboard</span>
        </div>
        <div class="search-box">
            <input type="text" placeholder="Search...">
            <i class='bx bx-search'></i>
        </div>
        <div class="profile-details">
            <!-- Using user icon instead of an image -->
            <i class='bx bx-user icon'></i>
            <span class="Tenant_name"><?php echo htmlspecialchars($tenantName); ?></span>
            <i class='bx bx-chevron-down'></i>
        </div>
    </nav>
    <script src="script1.js"></script>
</body>
</html>
