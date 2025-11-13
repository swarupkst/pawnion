<?php
session_start();
include '../model/db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('You must be logged in to post a pet.');
            window.location.href = '../auth/login.php';
          </script>";
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Collect form data safely
    $pet_name = trim($_POST['pet_name']);
    $animal_type = trim($_POST['animal_type']);
    $age = intval($_POST['age']);
    $gender = trim($_POST['gender']);
    $location = trim($_POST['location']);
    $contact_number = trim($_POST['contact_number']);
    $description = trim($_POST['description']);

    // Handle photo uploads
    $uploaded_photos = [];
    $upload_dir = __DIR__ . "/../uploads/"; // actual folder path on server
    $upload_url_prefix = "uploads/";        // path for database & browser

    // Create folder if it doesn’t exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Multiple photo uploads
    if (!empty($_FILES['photos']['name'][0])) {
        foreach ($_FILES['photos']['name'] as $key => $file_name) {
            $tmp_name = $_FILES['photos']['tmp_name'][$key];

            if ($tmp_name == "") continue;

            // Create a unique file name
            $new_filename = time() . "_" . basename($file_name);
            $target_path = $upload_dir . $new_filename;

            if (move_uploaded_file($tmp_name, $target_path)) {
                // ✅ Save only relative path (web-accessible)
                $uploaded_photos[] = $upload_url_prefix . $new_filename;
            } else {
                echo "<script>alert('Error uploading file: $file_name');</script>";
            }
        }
    }

    // Convert photo paths into comma-separated string
    $photos_str = implode(",", $uploaded_photos);

    // Insert data into DB
    $stmt = $conn->prepare("INSERT INTO adoption (user_id, pet_name, animal_type, age, gender, location, contact_number, description, photos)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ississsss", $user_id, $pet_name, $animal_type, $age, $gender, $location, $contact_number, $description, $photos_str);

    if ($stmt->execute()) {
        echo "<script>
                alert('🐾 Pet posted successfully with photos!');
                window.location.href = '../view/view-pet.php';
              </script>";
    } else {
        echo "<script>
                alert('❌ Failed to post pet. Error: " . addslashes($stmt->error) . "');
                window.history.back();
              </script>";
    }

    $stmt->close();
    $conn->close();
}
?>
