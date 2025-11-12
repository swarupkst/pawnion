<?php
session_start();
include '../control/db.php';

if (!isset($_GET['token'])) {
    die("Invalid request");
}

$token = $_GET['token'];

// Verify token
$stmt = $conn->prepare("SELECT id, name FROM users WHERE reset_token=? AND token_expiry >= NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Invalid or expired token");
}

$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    if ($password !== $confirm) {
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt2 = $conn->prepare("UPDATE users SET password=?, reset_token=NULL, token_expiry=NULL WHERE id=?");
        $stmt2->bind_param("si", $hash, $user['id']);
        $stmt2->execute();

        echo "<script>alert('Password reset successful!'); window.location.href='login.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PawNion - Reset Password</title>
  <link rel="icon" type="image/icon" href="../content/paonion-favicon.png">
  <link href="output.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body class="bg-[#F6F7F8] min-h-screen flex flex-col items-center justify-center px-4 py-10">

  <div class="bg-[#FEFFFE] shadow-lg rounded-3xl w-full max-w-md p-8 sm:p-10">

    <!-- Back Button -->
    <a href="login.php" class="text-[#68AADB] hover:text-[#E8793C] font-semibold mb-6 inline-block transition">
      ← Back to Login
    </a>

    <!-- Title -->
    <h2 class="text-3xl sm:text-4xl font-bold text-center text-[#E8793C] mb-6">
      🔑 Reset Password
    </h2>

    <p class="text-gray-600 text-center mb-8">
      Enter your new password below to reset your account.
    </p>

    <!-- Form -->
    <form method="POST" class="space-y-6">

      <!-- New Password -->
      <div>
        <label class="block text-gray-700 font-medium mb-2">New Password</label>
        <input type="password" name="password" placeholder="Enter new password" required
          class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#68AADB]">
      </div>

      <!-- Confirm Password -->
      <div>
        <label class="block text-gray-700 font-medium mb-2">Confirm Password</label>
        <input type="password" name="confirm" placeholder="Confirm new password" required
          class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#68AADB]">
      </div>

      <!-- Submit Button -->
      <div class="pt-4 text-center">
        <button type="submit"
          class="bg-[#E8793C] hover:bg-[#d66c30] text-white font-semibold px-8 py-3 rounded-2xl shadow-md transition duration-300 w-full sm:w-auto">
          Reset Password
        </button>
      </div>

    </form>

    <!-- Extra Links -->
    <p class="text-center text-gray-500 text-sm mt-6">
      Remembered your password? 
      <a href="login.php" class="text-[#68AADB] hover:text-[#E8793C] font-semibold transition">Login here</a>
    </p>

  </div>

  <!-- Footer -->
  <div class="text-center mt-8">
    <p class="text-gray-500 text-sm">
      © 2025 <span class="text-[#E8793C] font-semibold">PawNion</span> — Find Your New Companion
    </p>
  </div>

</body>
</html>
