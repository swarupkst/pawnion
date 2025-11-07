<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page with a message
    header("Location: login.php?message=" . urlencode("You must login first"));
    exit();
}
?>


<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "pawnion";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get pet ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Pet ID is missing.");
}

$pet_id = intval($_GET['id']);

// Fetch pet details
$stmt = $conn->prepare("SELECT * FROM adoption WHERE id = ?");
$stmt->bind_param("i", $pet_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Pet not found.");
}

$pet = $result->fetch_assoc();
$photos = explode(",", $pet['photos']); // multiple photos
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($pet['pet_name']); ?> - PawNion</title>
<link href="output.css" rel="stylesheet">
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="bg-[#F6F7F8] min-h-screen">

  <div class="max-w-4xl mx-auto px-4 py-10">

    <!-- Back button -->
    <a href="view-pet.php" class="text-[#68AADB] hover:text-[#E8793C] font-semibold transition mb-4 inline-block">
      ← Back to Pets
    </a>

    <!-- Pet Details -->
    <div class="bg-white rounded-2xl shadow p-6">
      
      <!-- Photo Gallery -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <?php foreach ($photos as $photo): ?>
          <img src="<?php echo !empty($photo) ? $photo : '../content/default-pet.jpg'; ?>" 
               alt="Pet Photo" class="w-full h-64 object-cover rounded-2xl">
        <?php endforeach; ?>
      </div>

      <!-- Pet Info -->
      <h1 class="text-3xl font-bold text-[#E8793C] mb-2"><?php echo htmlspecialchars($pet['pet_name']); ?></h1>
      <p class="text-gray-600 mb-2">
        <strong>Type:</strong> <?php echo htmlspecialchars($pet['animal_type']); ?> |
        <strong>Age:</strong> <?php echo $pet['age']; ?> year<?php echo ($pet['age'] > 1 ? 's' : ''); ?> |
        <strong>Gender:</strong> <?php echo htmlspecialchars($pet['gender']); ?> |
        <strong>Location:</strong> <?php echo htmlspecialchars($pet['location']); ?>
      </p>
      <p class="text-gray-500 mb-4"><?php echo nl2br(htmlspecialchars($pet['description'])); ?></p>

      <!-- Contact / Adopt Button -->
      <a href="contact.php" 
         class="inline-block bg-[#68AADB] text-white px-6 py-3 rounded-2xl hover:bg-[#589ACB] transition">
         Contact to Adopt
      </a>

    </div>
  </div>

  <!-- Footer -->
  <div class="text-center mt-8">
    <p class="text-gray-500 text-sm">
      © 2025 <span class="text-[#E8793C] font-semibold">PawNion</span> — Find Your New Companion
    </p>
  </div>

</body>
</html>

<?php $conn->close(); ?>
