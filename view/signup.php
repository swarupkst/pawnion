<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PawNion - Sign Up</title>
  <link rel="icon" type="image/icon" href="../content/paonion-favicon.png">
  <link href="output.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" crossorigin="anonymous">
</head>

<body class="bg-[#F6F7F8] min-h-screen flex flex-col justify-center items-center px-4 py-8">

  <!-- Card -->
  <div class="bg-[#FEFFFE] shadow-lg rounded-3xl w-full max-w-md p-8 sm:p-10 text-center">

    <!-- Logo -->
    <div class="flex justify-center mb-4">
      <img src="../content/pawniol-logo.png" alt="PawNion" class="w-36">
    </div>

    <!-- Title -->
    <h2 class="text-3xl font-bold text-[#68AADB] mb-6">Create an Account 🐶</h2>

    <!-- Signup Form -->
    <form class="space-y-5" action="../control/register.php" method="post">

      <!-- Name -->
      <div class="text-left">
        <label class="block text-gray-700 font-medium mb-2">Full Name</label>
        <input type="text" name="full_name" placeholder="Enter your full name" required
          class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#68AADB]">
      </div>

      <!-- Email -->
      <div class="text-left">
        <label class="block text-gray-700 font-medium mb-2">Email</label>
        <input type="email" name="email" placeholder="Enter your email" required
          class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#68AADB]">
      </div>

      <!-- Password -->
      <div class="text-left">
        <label class="block text-gray-700 font-medium mb-2">Password</label>
        <input type="password" name="password" placeholder="Create a password" required
          class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#68AADB]">
      </div>

      <!-- Confirm Password -->
      <div class="text-left">
        <label class="block text-gray-700 font-medium mb-2">Confirm Password</label>
        <input type="password" name="confirm_password" placeholder="Confirm your password" required
          class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#68AADB]">
      </div>

      <!-- Terms Checkbox -->
      <div class="flex items-start text-sm text-gray-600">
        <input type="checkbox" required class="mt-1 mr-2 text-[#68AADB] focus:ring-[#68AADB]">
        <p>I agree to the <a href="#" class="text-[#E8793C] hover:text-[#68AADB] font-semibold transition">Terms & Conditions</a></p>
      </div>

      <!-- Signup Button -->
      <button type="submit"
        class="bg-[#68AADB] hover:bg-[#5b9ac9] text-white font-semibold px-8 py-3 rounded-2xl shadow-md transition duration-300 w-full">
        Sign Up
      </button>
    </form>


    <!-- Login Link -->
    <p class="mt-5 text-gray-600">
      Already have an account?
      <a href="login.php" class="text-[#E8793C] hover:text-[#68AADB] font-semibold transition">Log in</a>
    </p>

    <!-- Back to Home -->
    <a href="../index.php"
       class="inline-block mt-6 text-[#68AADB] hover:text-[#E8793C] font-semibold transition">
      ← Back to Home
    </a>
  </div>

  <!-- Footer -->
  <div class="text-center mt-8">
    <p class="text-gray-500 text-sm">
      © 2025 <span class="text-[#E8793C] font-semibold">PawNion</span> — Find Your New Companion
    </p>
  </div>

</body>
</html>
