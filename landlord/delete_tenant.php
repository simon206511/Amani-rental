<?php
include 'landlorddash.php';
include 'config.php'; 

// Check if ID is provided
if (!isset($_GET['id'])) {
    die("Tenant ID is required.");
}

$tenantId = $_GET['id'];

// Prepare and execute the delete statement
$stmt = $conn->prepare("DELETE FROM tenants WHERE tenant_id = ?");
$stmt->bind_param('s', $tenantId);

if ($stmt->execute()) {
    header("Location: tenant_details.php?message=Tenant deleted successfully");
} else {
    die("Error deleting tenant: " . $stmt->error);
}

$stmt->close();
$conn->close();
