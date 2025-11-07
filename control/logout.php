<?php
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to home page or login page
echo "<script>
        alert('You have been logged out successfully 🐾');
        window.location.href ='../index.php';
      </script>";
exit;
?>
