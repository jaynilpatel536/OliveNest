<?php
// process/process_upload.php
session_start();

// session authentication check
if (!isset($_SESSION['user_id'])) {
    header("Location: /OliveNest/auth/login.php");
    exit;
}

$projectRoot = dirname(__DIR__);
require_once dirname(__DIR__) . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method.");
}

// sanitize inputs
$owner_name = trim($_POST['owner_name'] ?? '');
$home_title = trim($_POST['home_title'] ?? '');
$location   = trim($_POST['location'] ?? '');
$price      = (int)($_POST['price'] ?? 0);
$description= trim($_POST['description'] ?? '');

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
$newId = getNextId($db, 'homes');
try {
    $result = $db->homes->insertOne([
        'id'          => $newId,
        'owner_name'  => $owner_name,
        'home_title'  => $home_title,
        'location'    => $location,
        'price'       => $price,
        'description' => $description,
        'images'      => $images_csv,
        'status'      => 'pending',
        'created_at'  => new MongoDB\BSON\UTCDateTime()
    ]);

    if ($result->getInsertedCount() > 0) {
        header("Location: /OliveNest/pages/upload_success.php");
        exit;
    } else {
        echo "DB error: Failed to insert home.";
    }
} catch (Exception $e) {
    echo "DB error: " . $e->getMessage();
}
