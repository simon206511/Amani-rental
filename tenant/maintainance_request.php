<?php
session_start(); // Start the session

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
    // Retrieve form data
    $tenantID = $_POST['tenantID'];
    $houseNumber = $_POST['houseNumber'];
    $description = $_POST['description'];
    $urgency = $_POST['urgency'];
    $contactMethod = $_POST['contactMethod'];
    $permissionToEnter = isset($_POST['permissionToEnter']) ? 1 : 0;

    // Validate input data
    if (empty($tenantID) || empty($houseNumber) || empty($description) || empty($urgency) || empty($contactMethod)) {
        $message = "All fields are required.";
    } else {
        // Prepare SQL query
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
}

// Close the database connection
$connection->close();

// Redirect back to the form page with a message
header("Location: maintainance.php?message=" . urlencode($message));
exit();
