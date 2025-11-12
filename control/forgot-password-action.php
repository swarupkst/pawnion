<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    // Check if user exists
    $stmt = $conn->prepare("SELECT id, name FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Generate unique token
        $token = bin2hex(random_bytes(50));
        $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // Store token in DB
        $stmt2 = $conn->prepare("UPDATE users SET reset_token=?, token_expiry=? WHERE id=?");
        $stmt2->bind_param("ssi", $token, $expires, $user['id']);
        $stmt2->execute();

        // Send email
        $reset_link = "http://yourwebsite.com/view/reset-password.php?token=$token";
        $subject = "PawNion Password Reset";
        $message = "Hi " . $user['name'] . ",\n\nClick the link below to reset your password:\n$reset_link\n\nThis link will expire in 1 hour.";
        $headers = "From: no-reply@yourwebsite.com";

        if (mail($email, $subject, $message, $headers)) {
            echo "<script>alert('Password reset link sent! Check your email.'); window.location.href='../view/login.php';</script>";
        } else {
            echo "<script>alert('Failed to send email. Please try again later.'); window.history.back();</script>";
        }

    } else {
        echo "<script>alert('Email not found!'); window.history.back();</script>";
    }
}
?>
