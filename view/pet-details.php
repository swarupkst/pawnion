<?php
session_start();
include '../control/db.php';

// Get pet ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Invalid pet ID'); window.location.href='view-pet.php';</script>";
    exit;
}

$pet_id = intval($_GET['id']);

// Fetch pet data
$stmt = $conn->prepare("SELECT * FROM adoption WHERE id = ?");
$stmt->bind_param("i", $pet_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Pet not found'); window.location.href='view-pet.php';</script>";
    exit;
}

$pet = $result->fetch_assoc();

// Split photos
$photos = explode(",", $pet['photos']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($pet['pet_name']); ?> - Details</title>
<link href="output.css" rel="stylesheet">
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="bg-[#F6F7F8] min-h-screen">

<div class="max-w-5xl mx-auto px-4 py-8 flex flex-col items-center">

    <!-- Back button -->
    <a href="view-pet.php" class="text-[#68AADB] hover:text-[#E8793C] font-semibold mb-6 inline-block">
        ← Back to Pets
    </a>

    <!-- Pet Name -->
    <h1 class="text-4xl font-bold text-[#E8793C] mb-6 text-center"><?php echo htmlspecialchars($pet['pet_name']); ?></h1>

    <!-- Photo Gallery -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-6 w-full justify-items-center">
        <?php foreach ($photos as $photo): ?>
            <?php
                $photo = trim($photo);
                if (empty($photo) || !file_exists("../" . $photo)) {
                    $photo = "content/default-pet.png";
                }
            ?>
            <img src="../<?php echo htmlspecialchars($photo); ?>" alt="Pet Photo" class="w-64 h-64 object-cover rounded-lg shadow">
        <?php endforeach; ?>
    </div>

    <!-- Pet Details -->
    <div class="bg-white rounded-2xl shadow p-6 w-full md:w-3/4 mb-4 mx-auto">
        <p class="text-gray-700 mb-2 pl-2"><strong>Type:</strong> <?php echo htmlspecialchars($pet['animal_type']); ?></p>
        <p class="text-gray-700 mb-2 pl-2"><strong>Age:</strong> <?php echo htmlspecialchars($pet['age']); ?> year<?php echo ($pet['age'] > 1 ? 's' : ''); ?></p>
        <p class="text-gray-700 mb-2 pl-2"><strong>Gender:</strong> <?php echo htmlspecialchars($pet['gender']); ?></p>
        <p class="text-gray-700 mb-2 pl-2"><strong>Location:</strong> <?php echo htmlspecialchars($pet['location']); ?></p>
        <p class="text-gray-700 mb-2 pl-2"><strong>Contact:</strong> <?php echo htmlspecialchars($pet['contact_number']); ?></p>
        <p class="text-gray-700 mt-4 pl-2"><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($pet['description'])); ?></p>
    </div>

    <!-- Mark as Adopted Button -->
    <?php 
    if (isset($_SESSION['user_id'], $pet['user_id']) && $pet['user_id'] == $_SESSION['user_id'] && $pet['is_adopted'] == 0): ?>
        <form action="../control/mark_adopted.php" method="POST" class="mt-4">
            <input type="hidden" name="pet_id" value="<?php echo $pet['id']; ?>">
            <button type="submit" class="bg-[#E8793C] hover:bg-[#d46c33] text-white px-6 py-2 rounded-full">
                Mark as Adopted
            </button>
        </form>
    <?php endif; ?>

</div>

<!-- Footer -->
<div class="text-center mt-8">
    <p class="text-gray-500 text-sm">
        © 2025 <span class="text-[#E8793C] font-semibold">PawNion</span> — Find Your New Companion
    </p>
</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
