<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli('localhost', 'root', '', 'property_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tenantId = $_GET['tenant_id'] ?? '';
$notification = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tenantId = $_POST['tenant_id'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Password validation
    if ($newPassword !== $confirmPassword) {
        $notification = "<div class='error-message'><h3>Passwords do not match.</h3></div>";
    } elseif (strlen($newPassword) < 8 || !preg_match("/[A-Z]/", $newPassword) || !preg_match("/[0-9]/", $newPassword)) {
        $notification = "<div class='error-message'><h3>Password must be at least 8 characters long, contain at least one uppercase letter, and one number.</h3></div>";
    } else {
        // Hash the password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // Update the password
        $stmt = $conn->prepare("UPDATE tenants SET password = ? WHERE tenant_id = ?");
        $stmt->bind_param('ss', $hashedPassword, $tenantId);
        if ($stmt->execute()) {
            $notification = "<div class='success-message'><h3>Password reset successfully. <a href='tenantlogin.php'>Login now</a>.</h3></div>";
        } else {
            $notification = "<div class='error-message'><h3>Error updating password. Please try again.</h3></div>";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- ===== Iconscout CSS ===== -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <!-- ===== CSS ===== -->
    <link rel="stylesheet" href="style1.css">   
    <title>Reset Password</title> 
</head>
<body>
    <div class="container">
        <div class="forms">
            <div class="form signup">
                <span class="title">Reset Password</span>
                <?php if ($notification): ?>
                    <div class="notification">
                        <?php echo $notification; ?>
                    </div>
                <?php endif; ?>
                <form action="" method="POST">
                    <input type="hidden" name="tenant_id" value="<?php echo htmlspecialchars($tenantId); ?>">
                    <div class="input-field">
                        <input type="password" id="newPassword" name="newPassword" class="password" placeholder="New Password" required>
                        <i class="uil uil-lock icon"></i>
                        <i class="uil uil-eye-slash showHidePw"></i>
                    </div>
                    <div class="input-field">
                        <input type="password" id="confirmPassword" name="confirmPassword" class="password" placeholder="Confirm New Password" required>
                        <i class="uil uil-lock icon"></i>
                        <i class="uil uil-eye-slash showHidePw"></i>
                    </div>
                    <div class="input-field button">
                        <input type="submit" value="Reset Password">
                    </div>
                </form>
                <div class="login-signup">
                    <span class="text">Already have an account? <a href="tenantlogin.php" class="text login-link">Login Now</a></span>
                </div>
            </div>
        </div>
    </div>
    <script src="script.js"></script> 
</body>
</html>
