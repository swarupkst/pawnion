<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?message=" . urlencode("You must login first"));
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "pawnion";
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get search and filter values
$search = isset($_GET['search']) ? $_GET['search'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Prepare SQL
$sql = "SELECT * FROM adoption WHERE 1=1";

if ($search != '') {
    $search = $conn->real_escape_string($search);
    $sql .= " AND (pet_name LIKE '%$search%' OR location LIKE '%$search%')";
}

if ($type != '') {
    $type = $conn->real_escape_string($type);
    $sql .= " AND animal_type = '$type'";
}

$sql .= " ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>View Pets - PawNion</title>
  <link rel="icon" type="image/icon" href="../content/paonion-favicon.png">
  <link href="output.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body class="bg-[#F6F7F8] min-h-screen ">

  <!-- Main Container -->
  <div class="max-w-6xl mx-auto px-4">

    <!-- Navbar / Back -->
    <div class="flex justify-between items-center mb-6 mt-5">
      <a href="../index.php" class="text-[#68AADB] hover:text-[#E8793C] font-semibold transition">
        ← Back to Home
      </a>

      <a href="adoption.php" class="bg-[#68AADB] hover:bg-[#5a96c4] text-white font-semibold px-5 py-2 rounded-2xl shadow-md transition flex items-center gap-2">
        <i class="fas fa-paw"></i> Post for Adoption
      </a>
    </div>

    <!-- Title -->
    <h2 class="text-3xl sm:text-4xl font-bold text-center text-[#E8793C] mb-8">
      🐾 Available Pets for Adoption
    </h2>

    <!-- Search + Filter -->
    <form method="GET" class="flex flex-col md:flex-row justify-center items-center gap-4 mt-8">
      <input 
        type="text" 
        name="search" 
        placeholder="Search by pet name or location..." 
        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
        class="w-full md:w-1/3 px-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-[#E8793C]" 
      />
      <select 
        name="type" 
        class="w-full md:w-1/6 px-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-[#68AADB]">
        <option value="">All Types</option>
        <option <?php if(isset($_GET['type']) && $_GET['type'] == 'Dog') echo 'selected'; ?>>Dog</option>
        <option <?php if(isset($_GET['type']) && $_GET['type'] == 'Cat') echo 'selected'; ?>>Cat</option>
        <option <?php if(isset($_GET['type']) && $_GET['type'] == 'Bird') echo 'selected'; ?>>Bird</option>
        <option <?php if(isset($_GET['type']) && $_GET['type'] == 'Rabbit') echo 'selected'; ?>>Rabbit</option>
        <option <?php if(isset($_GET['type']) && $_GET['type'] == 'Other') echo 'selected'; ?>>Other</option>
      </select>
      <button type="submit" class="bg-[#E8793C] text-white px-6 py-2 rounded-full hover:opacity-90 transition">
        <i class="fas fa-search"></i> Search
      </button>
    </form>

    <!-- Pet List Grid -->
    <section class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mt-8">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($pet = $result->fetch_assoc()): ?>
          <?php
            $photos = explode(",", $pet['photos']);
            $first_photo = trim($photos[0]);

            // ✅ Ensure correct path for uploads
            if (!empty($first_photo)) {
                // Check if path already contains "uploads/"
                if (strpos($first_photo, 'uploads/') === false) {
                    $first_photo = "uploads/" . $first_photo;
                }
            } else {
                $first_photo = "../content/default-pet.png";
            }

            // Optional: check if file exists (avoid broken images)
            if (!file_exists($first_photo)) {
                $first_photo = "../content/default-pet.png";
            }
          ?>
          <div class="bg-white rounded-2xl shadow hover:shadow-lg transition">
            <img src="<?php echo htmlspecialchars($first_photo); ?>" alt="Pet" class="w-full h-48 object-cover rounded-t-2xl">
            <div class="p-4">
              <h2 class="text-lg font-semibold text-[#333]"><?php echo htmlspecialchars($pet['pet_name']); ?></h2>
              <p class="text-sm text-gray-600">
                <?php echo htmlspecialchars($pet['animal_type']); ?> • 
                <?php echo htmlspecialchars($pet['age']); ?> year<?php echo ($pet['age'] > 1 ? 's' : ''); ?> • 
                <?php echo htmlspecialchars($pet['location']); ?>
              </p>
              <p class="text-gray-500 mt-1"><?php echo htmlspecialchars($pet['description']); ?></p>
              <a href="pet-details.php?id=<?php echo $pet['id']; ?>" 
                 class="inline-block mt-3 bg-[#68AADB] text-white px-4 py-2 rounded-full hover:bg-[#589ACB] transition">
                 View Details
              </a>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="col-span-full text-center text-gray-500">No pets found.</p>
      <?php endif; ?>
    </section>
  </div> <!-- End Main Container -->

  <!-- Footer -->
  <div class="text-center mt-8">
    <p class="text-gray-500 text-sm">
      © 2025 <span class="text-[#E8793C] font-semibold">PawNion</span> — Find Your New Companion
    </p>
  </div>

</body>
</html>
<?php $conn->close(); ?>
