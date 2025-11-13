<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('You must login first!');
            window.location.href = '../view/login.php';
          </script>";
    exit;
}

include '../model/db.php';

// Get data safely
if (!isset($_POST['pet_id']) || !is_numeric($_POST['pet_id'])) {
    echo "<script>
            alert('Invalid pet ID.');
            window.location.href = '../view/view-pet.php';
          </script>";
    exit;
}

$pet_id = intval($_POST['pet_id']);
$user_id = $_SESSION['user_id'];

// Update pet only if it belongs to logged-in user
$sql = "UPDATE adoption SET is_adopted = 1 WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $pet_id, $user_id);
$stmt->execute();

// Show success/failure messages using JS alerts
if ($stmt->affected_rows > 0) {
    echo "<script>
            alert('Marked as adopted successfully!');
            window.location.href = '../view/view-pet.php';
          </script>";
    exit;
} else {
    echo "<script>
            alert('You can only mark your own post.');
            window.location.href = '../view/view-pet.php';
          </script>";
    exit;
}

// Close connections
$stmt->close();
$conn->close();
?>
