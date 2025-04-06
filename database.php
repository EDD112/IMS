<?php
session_start();
// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "user_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user details from session
$username = $_SESSION["username"];
$sql = "SELECT student_id, firstname, lastname, profile_picture, sidebar_profile_picture FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found.");
}

$student_id = $user["student_id"];
$firstname = $user["firstname"];
$lastname = $user["lastname"];
$profile_picture = $user["profile_picture"] ?? 'uploads/default-profile.png';
$sidebar_profile_picture = $user["sidebar_profile_picture"] ?? 'uploads/default-sidebar.png';

// Function to handle image upload
function uploadImage($file, $username, $column, $conn) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = time() . "_" . basename($file["name"]);
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        $update_sql = "UPDATE users SET $column = ? WHERE username = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ss", $target_file, $username);
        $update_stmt->execute();
        return $target_file;
    }
    return false;
}

// Handle profile picture uploads
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == UPLOAD_ERR_OK) {
        $newProfile = uploadImage($_FILES["profile_picture"], $username, "profile_picture", $conn);
        if ($newProfile) {
            $profile_picture = $newProfile;
        }
    }

    if (isset($_FILES["sidebar_profile_picture"]) && $_FILES["sidebar_profile_picture"]["error"] == UPLOAD_ERR_OK) {
        $newSidebarProfile = uploadImage($_FILES["sidebar_profile_picture"], $username, "sidebar_profile_picture", $conn);
        if ($newSidebarProfile) {
            $sidebar_profile_picture = $newSidebarProfile;
        }
    }
}


?>
