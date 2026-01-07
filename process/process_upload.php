<?php
// process/process_upload.php
// absolute path to connection.php (project root)
$projectRoot = dirname(__DIR__);
require_once $projectRoot . '/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method.");
}

// sanitize inputs
$owner_name = mysqli_real_escape_string($conn, trim($_POST['owner_name'] ?? ''));
$home_title = mysqli_real_escape_string($conn, trim($_POST['home_title'] ?? ''));
$location   = mysqli_real_escape_string($conn, trim($_POST['location'] ?? ''));
$price      = (int)($_POST['price'] ?? 0);
$description= mysqli_real_escape_string($conn, trim($_POST['description'] ?? ''));

if (!$owner_name || !$home_title || !$location || !$price || !$description) {
    die("All fields are required.");
}

// prepare upload folder
$slug = preg_replace('/[^a-z0-9\-]/', '', strtolower(str_replace(' ', '-', $home_title)));
$uploadBase = $projectRoot . '/uploads/homes/';
$folder = $uploadBase . $slug . '/';

if (!is_dir($folder)) {
    mkdir($folder, 0755, true);
}

$filenames = [];
if (!empty($_FILES['images']['tmp_name'])) {
    foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
        if ($tmp && is_uploaded_file($tmp)) {
            $origName = basename($_FILES['images']['name'][$i]);
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            // basic allowed types
            if (!in_array($ext, ['jpg','jpeg','png','webp'])) continue;
            // unique filename
            $safe = time() . "_" . ($i+1) . "." . $ext;
            $target = $folder . $safe;
            if (move_uploaded_file($tmp, $target)) {
                $filenames[] = $safe;
            }
        }
    }
}

if (empty($filenames)) {
    die("No images were uploaded or files were invalid.");
}

$images_csv = implode(",", $filenames);

// insert into DB
$stmt = $conn->prepare("INSERT INTO homes (owner_name, home_title, location, price, description, images, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
$stmt->bind_param("sssiss", $owner_name, $home_title, $location, $price, $description, $images_csv);

if ($stmt->execute()) {
    header("Location: " . dirname($projectRoot) . "/OliveNest/pages/upload_success.php");
    exit;
} else {
    echo "DB error: " . $stmt->error;
}
