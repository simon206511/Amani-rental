<?php
include'tenantd.php';

// Check if the user is logged in
if (!isset($_SESSION['tenant_id'])) {
    header("Location: tenantlogin.php"); // Redirect to login page if not logged in
    exit();
}

// Database connection
$connection = new mysqli("localhost", "root", "", "amani_db");

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenantID = $_POST['tenantID'];
    $houseNumber = $_POST['houseNumber'];
    $description = $_POST['description'];
    $urgency = $_POST['urgency'];
    $contactMethod = $_POST['contactMethod'];
    $permissionToEnter = isset($_POST['permissionToEnter']) ? 1 : 0;

    // Insert request into the database
    $sql = "INSERT INTO maintenance_requests (tenant_id, house_number, description, urgency, contact_method, permission_to_enter, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'Pending')";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("sssssi", $tenantID, $houseNumber, $description, $urgency, $contactMethod, $permissionToEnter);

    if ($stmt->execute()) {
        $message = "Request submitted successfully.";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch tenant data
$tenantID = $_SESSION['tenant_id']; // Assuming tenant_id is stored in session after login
$sql = "SELECT full_name, tenant_id, house_number, email, mobile_number FROM tenants WHERE tenant_id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("s", $tenantID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $tenantData = $result->fetch_assoc();
} else {
    $tenantData = [
        'full_name' => '',
        'tenant_id' => '',
        'house_number' => '',
        'email' => '',
        'mobile_number' => ''
    ];
}

$stmt->close();
$connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Maintenance Request Form</title>
    <link rel="stylesheet" type="text/css" href="style3.css">
</head>
<body>
    <div class="home-section">
        <div class="container">
            <h2>Maintenance Request Form</h2>
            <!-- Display notification message if available -->
            <?php if (isset($message) && $message != ''): ?>
                <div class="notification" style="color: green; margin-bottom: 15px;">
                    <?= $message ?>
                </div>
            <?php endif; ?>
            <form action="maintainance_request.php" method="POST" id="maintenanceForm">
                <div class="form-group">
                    <label for="tenantName">Name:</label>
                    <input type="text" id="tenantName" name="tenantName" value="<?= htmlspecialchars($tenantData['full_name']) ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="tenantID">Tenant ID:</label>
                    <input type="text" id="tenantID" name="tenantID" value="<?= htmlspecialchars($tenantData['tenant_id']) ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="houseNumber">House Number:</label>
                    <input type="text" id="houseNumber" name="houseNumber" value="<?= htmlspecialchars($tenantData['house_number']) ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($tenantData['email']) ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number:</label>
                    <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($tenantData['mobile_number']) ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="description">Description of the Issue:</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="urgency">Urgency Level:</label>
                    <select id="urgency" name="urgency" required>
                        <option value="emergency">Emergency</option>
                        <option value="urgent">Urgent</option>
                        <option value="non-urgent">Non-Urgent</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="contactMethod">Preferred Contact Method:</label>
                    <input type="radio" id="phoneContact" name="contactMethod" value="Phone" checked>
                    <label for="phoneContact">Phone</label>
                    <input type="radio" id="emailContact" name="contactMethod" value="Email">
                    <label for="emailContact">Email</label>
                </div>
                <div class="form-group">
                    <label for="permissionToEnter">Permission to Enter:</label>
                    <input type="checkbox" id="permissionToEnter" name="permissionToEnter" value="Yes"> Yes
                </div>
                <button type="submit">Submit Request</button>
            </form>
        </div>
    </div>
</body>
</html>
