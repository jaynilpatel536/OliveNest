<?php
session_start();
require_once __DIR__ . '/../config/db.php';


if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$edit = isset($_GET['edit']);
$msg = "";

/* SAVE EXTRA INFO */
if (isset($_POST['save'])) {
    $extra = $_POST['extra_info'] ?? '';

    $sql = "UPDATE users SET extra_info='$extra' WHERE username='$username'";
    mysqli_query($conn, $sql);

    $msg = "Profile updated successfully!";
    $edit = false;
}

/* FETCH USER */
$result = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
$user = mysqli_fetch_assoc($result);

// Safely define extra_info if it doesn't exist
$extra_info = $user['extra_info'] ?? "";
?>
<!DOCTYPE html>
<html>
<head>
<title>My Profile</title>
<style>
body{font-family:Arial;background:#f2f2f2;}
.card{
    width:450px;background:white;padding:30px;
    margin:60px auto;border-radius:14px;
    box-shadow:0 0 15px rgba(0,0,0,0.15);
}
.btn{padding:10px 20px;border:none;border-radius:6px;cursor:pointer;}
.edit{background:#3498db;color:white;}
.save{background:#2ecc71;color:white;}
</style>
</head>

<body>

<div class="card">

<h2>My Profile</h2>

<?php if($msg): ?>
<p style="color:green"><?= $msg ?></p>
<?php endif; ?>

<?php if(!$edit): ?>

    <!-- VIEW MODE -->
    <p><strong>Name:</strong> <?= $user['full_name'] ?></p>
    <p><strong>Username:</strong> <?= $user['username'] ?></p>
    <p><strong>Email:</strong> <?= $user['email'] ?></p>
    <p><strong>Role:</strong> <?= ucfirst($user['role']) ?></p>

    <hr>
    <h4>About Me</h4>
    <p><?= nl2br($extra_info ?: "No additional information added yet.") ?></p>

    <br>
    <a href="profile.php?edit=1" class="btn edit">Edit Profile</a>

<?php else: ?>

    <!-- EDIT MODE -->
    <form method="POST">
        <label>Tell us about yourself</label>
        <textarea name="extra_info" rows="6" style="width:100%;"><?= $extra_info ?></textarea>

        <br><br>
        <button type="submit" name="save" class="btn save">Save</button>
    </form>

<?php endif; ?>

</div>

</body>
</html>
