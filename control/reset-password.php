<?php
include '../model/db.php';

// Step 1: Check token
if (!isset($_GET['token'])) {
    die("❌ No token provided.");
}

$token = $_GET['token'];

// Step 2: Validate token
$stmt = $conn->prepare("SELECT reset_token, token_expiry FROM users WHERE reset_token=? LIMIT 1");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("❌ Invalid token!");
}

// Step 3: Check token expiry
if (strtotime($user['token_expiry']) < time()) {
    die("❌ Token is expired!");
}

// Step 4: Handle password reset form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        echo "<script>alert('❌ Passwords do not match!');</script>";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt2 = $conn->prepare("UPDATE users SET password=?, reset_token=NULL, token_expiry=NULL WHERE reset_token=?");
        $stmt2->bind_param("ss", $hashed_password, $token);
        $stmt2->execute();

        echo "<script>
                alert('✅ Password changed successfully!');
                window.location.href='../view/login.php';
              </script>";
        exit();
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
  <link href="../view/output.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body class="bg-[#F6F7F8] min-h-screen flex flex-col items-center justify-center px-4 py-10">

  <!-- Container -->
  <div class="bg-[#FEFFFE] shadow-lg rounded-3xl w-full max-w-md p-8 sm:p-10">

    <!-- Back Button -->
    <a href="../view/login.php" class="text-[#68AADB] hover:text-[#E8793C] font-semibold mb-6 inline-block transition">
      ← Back to Login
    </a>

    <!-- Title -->
    <h2 class="text-3xl sm:text-4xl font-bold text-center text-[#E8793C] mb-6">
      🔐 Reset Password
    </h2>

    <!-- Info Text -->
    <p class="text-gray-600 text-center mb-8">
      Enter your new password below to reset your account.
    </p>

    <!-- Form -->
    <form method="POST" class="space-y-6">

      <!-- Hidden Token -->
      <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">

      <!-- New Password -->
      <div>
        <label class="block text-gray-700 font-medium mb-2">New Password</label>
        <input type="password" name="password" placeholder="Enter new password" required
          class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#68AADB]">
      </div>

      <!-- Confirm Password -->
      <div>
        <label class="block text-gray-700 font-medium mb-2">Confirm Password</label>
        <input type="password" name="confirm_password" placeholder="Confirm new password" required
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
      <a href="../view/login.php" class="text-[#68AADB] hover:text-[#E8793C] font-semibold transition">Login here</a>
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
