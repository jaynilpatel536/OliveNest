<?php
session_start();
include __DIR__ . '/../config/db.php';

if (isset($_SESSION['admin'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - OliveNest</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="login-container">
    <h2>Admin Login</h2>

    <form method="POST">
        <input type="text" name="username" placeholder="Enter Username" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <button type="submit" name="login">Login</button>
    </form>
</div>

<?php
if (isset($_POST['login'])) {

    $username = trim($_POST['username'] ?? '');
    $password = md5($_POST['password'] ?? '');   // encrypt password

    $admin = $db->admin_users->findOne([
        'username' => $username,
        'password' => $password
    ]);

    if ($admin) {
        $_SESSION['admin'] = $username;
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<script>alert('Invalid username or password');</script>";
    }
}
?>
</body>
</html>
