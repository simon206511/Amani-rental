<?php
include 'config.php';

// Check if form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Define variables and sanitize inputs
    $name = trim($_POST['name']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);
    $cPassword = trim($_POST['cPassword']);
    $role = trim($_POST['role']);

    // Validate input
    if (empty($name) || empty($email) || empty($password) || empty($cPassword) || empty($role)) {
        echo "Please fill out all fields.";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email address.";
        exit;
    }

    if ($password !== $cPassword) {
        echo "Passwords do not match.";
        exit;
    }

    // Check if email already exists
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Email already exists.";
        $stmt->close();
        exit;
    }
    $stmt->close();

    // Password hashing
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert data into database
    $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("ssss", $name, $email, $password_hash, $role);
    $stmt->execute();

    // Check if insert was successful
    if ($stmt->affected_rows === 1) {
        echo "Registration successful! You can now <a href='login.html'>login</a>.";
    } else {
        echo "Registration failed. Please try again later.";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "Form has not been submitted.";
}

