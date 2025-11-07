<?php
session_start();
include 'db.php'; // ✅ Make sure this file contains your DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        // Prepare and bind (to prevent SQL Injection)
        $stmt = $conn->prepare("SELECT id, full_name, email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $name, $email_db, $hashed_password);
            $stmt->fetch();

            // Verify password
            if (password_verify($password, $hashed_password)) {
                // ✅ Store session variables
                $_SESSION['user_id'] = $id;
                $_SESSION['user_email'] = $email_db;

                echo "<script>
                        alert('Login successful! Welcome back, $name 🐾');
                        window.location.href = '../index.php';
                      </script>";
            } else {
                echo "<script>
                        alert('Incorrect password. Please try again.');
                        window.history.back();
                      </script>";
            }
        } else {
            echo "<script>
                    alert('No account found with that email.');
                    window.history.back();
                  </script>";
        }
        $stmt->close();
    } else {
        echo "<script>
                alert('Please fill in all fields.');
                window.history.back();
              </script>";
    }
}
$conn->close();
?>
