<?php
session_start(); // Start the session

require_once 'config.php'; // Include database connection

$error = ''; // Initialize error variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve email and password from POST request
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute query to check user credentials
    $sql = "SELECT id, name, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result && password_verify($password, $result['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $result['id'];
        $_SESSION['user_email'] = $email; // Set email in session
        $_SESSION['user_name'] = $result['name']; // Store the user's name in the session

        // Redirect to the dashboard
        header("Location: landlorddash.php");
        exit();
    } else {
        // Handle invalid login
        $error = "Invalid email or password.";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Form</title>
  <link rel="stylesheet" href="resources/css/stylel.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
</head>
<body>
  <div class="wrapper">
    <header>Login Form</header>
    <?php if (!empty($error)): ?>
        <p style="color:red; text-align:center;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form action="" method="POST">
      <div class="field email">
        <div class="input-area">
          <input type="email" id="email" name="email" placeholder="Email" required>
          <i class="icon fas fa-envelope"></i>
          <i class="error error-icon fas fa-exclamation-circle"></i>
        </div>
        <div class="error error-txt">Email can't be blank</div>
      </div>
      <div class="field password">
        <div class="input-area">
          <input type="password" id="password" name="password" placeholder="Password" required>
          <i class="icon fas fa-lock"></i>
          <i class="error error-icon fas fa-exclamation-circle"></i>
        </div>
        <div class="error error-txt">Password can't be blank</div>
      </div>
      <div class="pass-txt"><a href="#">Forgot password?</a></div>
      <input type="submit" value="Login">
    </form>
    <div class="sign-txt">Not yet a member? <a href="signup.php">Signup now</a></div>
  </div>
</body>
</html>
