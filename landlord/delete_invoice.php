<?php
include 'landlorddash.php';
// Include the database configuration file
include 'config.php'; 

// Create a new database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if invoice_no is provided
if (isset($_GET['invoice_no']) && !empty($_GET['invoice_no'])) {
    $invoiceNo = $_GET['invoice_no'];
    
    // Prepare and execute the deletion query
    $deleteQuery = $conn->prepare("DELETE FROM invoice WHERE invoice_no = ?");
    $deleteQuery->bind_param('s', $invoiceNo);

    if ($deleteQuery->execute()) {
        echo "<script>alert('Invoice deleted successfully!'); window.location.href = 'invoice_list.php';</script>";
    } else {
        echo "<script>alert('Error: " . $deleteQuery->error . "'); window.location.href = 'invoice_list.php';</script>";
    }
} else {
    echo "<script>alert('No invoice number provided.'); window.location.href = 'invoicess.php';</script>";
}

$conn->close();
?>
