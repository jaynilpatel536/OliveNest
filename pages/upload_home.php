<?php include $_SERVER['DOCUMENT_ROOT']."/OliveNest/includes/header.php"; ?>
<?php


/* ================= AUTH CHECK ================= */
if (!isset($_SESSION['user_id'])) {
    header("Location: /OliveNest/auth/login.php");
    exit;
}

/* ================= ROLE CHECK ================= */
if ($_SESSION['role'] === 'guest') {
    echo "
    <script>
        alert('You are registered as a GUEST. Please register with an OWNER account to upload a home.');
        window.location.href = '/OliveNest/';
    </script>
    ";
    exit;
}

/* ================= DB CONNECTION ================= */
require_once __DIR__ . '/../config/db.php';

$msg = "";

/* ================= FORM SUBMIT ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $owner_name  = $_SESSION['name'];
    $home_title  = trim($_POST['home_title']);
    $location    = trim($_POST['location']);
    $price       = (int) $_POST['price'];
    $description = trim($_POST['description']);

    /* ================= IMAGE UPLOAD ================= */
    $targetDir = "../uploads/homes/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $imageName  = time() . "_" . basename($_FILES["home_image"]["name"]);
    $targetFile = $targetDir . $imageName;

    if (!move_uploaded_file($_FILES["home_image"]["tmp_name"], $targetFile)) {
        $msg = "Failed to upload image!";
    } else {
        /* ================= INSERT INTO DB ================= */
        try {
            $newId = getNextId($db, 'homes');
            $insertResult = $db->homes->insertOne([
                'id'          => $newId,
                'owner_name'  => $owner_name,
                'home_title'  => $home_title,
                'location'    => $location,
                'price'       => $price,
                'description' => $description,
                'images'      => $imageName,
                'status'      => 'pending',
                'created_at'  => new MongoDB\BSON\UTCDateTime()
            ]);

            if ($insertResult->getInsertedCount() > 0) {
                header("Location: upload_success.php");
                exit;
            } else {
                $msg = "Something went wrong: Failed to insert into database.";
            }
        } catch (Exception $e) {
            $msg = "Something went wrong: " . $e->getMessage();
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Upload Home</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins', sans-serif;}
body{background:#f4f6f8;}
.page-bg{background:url('/OliveNest/assets/images/upload.jpg') no-repeat center center/cover;min-height:calc(100vh - 160px);display:flex;justify-content:center;align-items:center;padding:40px 15px;}
.simple-form{width:100%;max-width:450px;background:#ffffff;padding:30px;border-radius:8px;}
.simple-form h2{text-align:center;margin-bottom:20px;color:#1d3557;}
.simple-form label{font-weight:600;margin-bottom:5px;display:block;color:#1d3557;}
.simple-form input,.simple-form textarea{width:100%;padding:12px;border:1px solid #ccc;border-radius:4px;margin-bottom:15px;font-size:14px;}
.simple-form textarea{resize:none;}
.simple-form button{width:100%;padding:12px;background:#1d3557;color:white;border:none;font-size:16px;cursor:pointer;}
.simple-form button:hover{opacity:0.9;}
.msg{text-align:center;color:green;margin-bottom:15px;font-weight:600;}
@media(max-width:600px){.simple-form{padding:20px;}}
</style>
</head>

<body>
<div class="page-bg">

<form class="simple-form" method="POST" enctype="multipart/form-data">
    <h2>Upload Home</h2>

    <?php if($msg): ?>
        <p class="msg"><?= htmlspecialchars($msg) ?></p>
    <?php endif; ?>

    <label>Owner Name</label>
    <input type="text" value="<?= htmlspecialchars($_SESSION['name']) ?>" readonly>

    <label>Home Title</label>
    <input type="text" name="home_title" required>

    <label>Location</label>
    <input type="text" name="location" required>

    <label>Price per Night</label>
    <input type="number" name="price" required>

    <label>Description</label>
    <textarea name="description" rows="4" required></textarea>

    <label>Upload Home Image</label>
    <input type="file" name="home_image" accept="image/*" required>

    <button type="submit">Upload Home</button>
</form>

</div>
<?php include $_SERVER['DOCUMENT_ROOT']."/OliveNest/includes/footer.php"; ?>
</body>
</html>
