<?php
include 'db.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Collect form data safely
    $pet_name = trim($_POST['pet_name']);
    $animal_type = trim($_POST['animal_type']);
    $age = intval($_POST['age']);
    $gender = trim($_POST['gender']);
    $location = trim($_POST['location']);
    $description = trim($_POST['description']);

    // Handle photo uploads
    $uploaded_photos = [];
    $upload_dir = __DIR__ . "/../uploads/"; // folder path

    // Make sure folder exists
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Ensure file input name="photos[]"
    if (!empty($_FILES['photos']['name'][0])) {
        foreach ($_FILES['photos']['name'] as $key => $file_name) {
            $tmp_name = $_FILES['photos']['tmp_name'][$key];

            // Skip empty uploads
            if ($tmp_name == "") continue;

            // Create unique name for each file
            $new_filename = time() . "_" . basename($file_name);
            $target_file = $upload_dir . $new_filename;

            // Check if uploaded successfully
            if (move_uploaded_file($tmp_name, $target_file)) {
                $uploaded_photos[] = $target_file;
            } else {
                echo "<script>alert('Error uploading file: $file_name');</script>";
            }
        }
    }

    // Convert photo paths into comma-separated string
    $photos_str = implode(",", $uploaded_photos);

    // Insert data into DB
    $stmt = $conn->prepare("INSERT INTO adoption (pet_name, animal_type, age, gender, location, description, photos)
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissss", $pet_name, $animal_type, $age, $gender, $location, $description, $photos_str);

    if ($stmt->execute()) {
        echo "<script>
                alert('🐾 Pet posted successfully with photos!');
                window.location.href = '../view/view-pet.php';
              </script>";
    } else {
        echo "<script>
                alert('❌ Failed to post pet. Error: " . $stmt->error . "');
                window.history.back();
              </script>";
    }

    $stmt->close();
    $conn->close();
}
?>
