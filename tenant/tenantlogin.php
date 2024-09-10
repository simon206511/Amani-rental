<?php
session_start(); // Start the session

// Database connection
$conn = new mysqli('localhost', 'root', '', 'amani_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$notification = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tenantId = $_POST['tenant_id'];
    $password = $_POST['password'];

    // Check if the tenant ID exists
    $stmt = $conn->prepare("SELECT password FROM tenants WHERE tenant_id = ?");
    $stmt->bind_param('s', $tenantId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result) {
        $storedPassword = $result['password'];

        // If the password is NULL, prompt to set a new password
        if ($storedPassword === null) {
            $notification = "<div class='error-message'><h3>Password not set. Please <a href='signuptenant.php?tenant_id=$tenantId'>register a password</a>.</h3></div>";
        } else {
            // Validate password
            if (password_verify($password, $storedPassword)) {
                // Successful login
                $_SESSION['tenant_id'] = $tenantId; // Set session variable
                header("Location: tenantd.php"); // Redirect to dashboard or another page
                exit();
            } else {
                $notification = "<div class='error-message'><h3>Invalid password. Please try again.</h3></div>";
            }
        }
    } else {
        $notification = "<div class='error-message'><h3>Invalid tenant ID. Please try again.</h3></div>";
    }

    $stmt->close();
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
         
    <title>Tenant Login</title> 
</head>
<body>
    
    <div class="container">
        <div class="forms">
            <div class="form login">
                <span class="title">Login</span>
                <?php if ($notification): ?>
                    <div class="notification">
                        <?php echo $notification; ?>
                    </div>
                <?php endif; ?>
                <form action="" method="POST">
                    <div class="input-field">
                        <input type="text" id="tenant_id" name="tenant_id" placeholder="Enter your Tenant ID" required>
                        <i class="uil uil-user icon"></i>
                    </div>
                    <div class="input-field">
                        <input type="password" id="password" name="password" class="password" placeholder="Enter your password" required>
                        <i class="uil uil-lock icon"></i>
                        <i class="uil uil-eye-slash showHidePw"></i>
                    </div>
                    <div class="checkbox-text">
                        <a href="#" class="text">Forgot password?</a>
                    </div>
                    <div class="input-field button">
                        <input type="submit" value="Login">
                    </div>
                </form>
                <div class="login-signup">
                    <span class="text">Not a member? <a href="signuptenant.php" class="text signup-link">Signup Now</a></span>
                </div>
            </div>
        </div>
    </div>
    <script src="script.js"></script> 
</body>
</html>
