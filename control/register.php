<?php
session_start();
include 'db.php'; // Make sure this connects to your database

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get POST data and sanitize
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Initialize an array to hold errors
    $errors = [];

    // Basic validation
    if (empty($full_name)) {
        $errors[] = "Full name is required!";
    }

    if (empty($email)) {
        $errors[] = "Email is required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format!";
    }

    if (empty($password)) {
        $errors[] = "Password is required!";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters!";
    }

    if (empty($confirm_password)) {
        $errors[] = "Confirm password is required!";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match!";
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Email already registered!";
    }
    $stmt->close();

    // If there are errors, show alert and go back
    if (!empty($errors)) {
        $error_message = implode("\\n", $errors);
        echo "<script>alert('$error_message'); window.history.back();</script>";
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $full_name, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>
                alert('Registration successful!');
                window.location.href = '../view/login.php';
              </script>";
    } else {
        echo "<script>
                alert('Error: ".$stmt->error."');
                window.history.back();
              </script>";
    }

    $stmt->close();
    $conn->close();
}
?>
