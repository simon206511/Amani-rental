<?php
// Include database connection
include 'landlorddash.php';
include 'config.php';

// Check if 'id' is provided in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Check if the form has been submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reason'])) {
        $reason = $_POST['reason'];

        // Update the maintenance request status to 'Completed', save the reason and resolved time
        $sql = "UPDATE maintenance_requests SET status = 'Completed', resolution_reason = ?, resolved_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $reason, $id);

        if ($stmt->execute()) {
            echo "Maintenance request resolved successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="resources/css/resolve.css">
    <title>Resolve Maintenance</title>
</head>
<body>
    <h1>Resolve Maintenance Request</h1>
    <?php if (isset($id)): ?>
        <form method="post" action="">
            <label for="reason">Reason for Resolution:</label>
            <textarea id="reason" name="reason" required></textarea>
            <br>
            <input type="submit" value="Submit">
        </form>
    <?php else: ?>
        <p>Invalid maintenance request.</p>
    <?php endif; ?>
</body>
</html>
