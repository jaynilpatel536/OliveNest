<?php
require_once __DIR__ . '/../config/db.php';

$message = "";
$step = "email"; // default step

// STEP 1: Check Email
if (isset($_POST['check_email'])) {
    $email = trim($_POST['email']);
    $user = $db->users->findOne(['email' => $email]);
    if ($user) {
        $step = "reset"; // show reset form
    } else {
        $message = "This email is not registered!";
    }
}

// STEP 2: Reset Password
if (isset($_POST['reset_password'])) {
    $email = trim($_POST['email']);
    $newpass = $_POST['password'];
    $hashed = password_hash($newpass, PASSWORD_DEFAULT);
    $db->users->updateOne(
        ['email' => $email],
        ['$set' => ['password' => $hashed]]
    );
    $message = "Password updated successfully! <a href='login.php'>Login now</a>";
    $step = "done";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Forgot Password</title>
<style>
body {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    /* Background image */
    background-image: url('/OliveNest/assets/images/background.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    font-family: Arial, sans-serif;
}
.container {
    width: 450px;
    padding: 30px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(8px);
    border-radius: 15px;
    color: #fff;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 0 20px rgba(0,0,0,0.3);
}
h2 {
    margin-bottom: 20px;
    font-size: 28px;
}
input {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border: none;
    border-bottom: 1px solid #fff;
    background: transparent;
    color: white;
    outline: none;
    font-size: 15px;
}
button {
    width: 100%;
    padding: 12px;
    background: white;
    border: none;
    margin-top: 10px;
    font-size: 16px;
    border-radius: 6px;
    cursor: pointer;
}
button:hover {
    background: #f1f1f1;
}
.msg {
    color: #ff6961;
    font-size: 14px;
    margin-bottom: 10px;
}
a {
    color: #fff;
    text-decoration: underline;
}
</style>
</head>
<body>
<div class="container">
    <h2>Forgot Password</h2>

    <!-- Show messages -->
    <?php if ($message != ""): ?>
        <p class="msg"><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Step 1: Email Form -->
    <?php if ($step == "email"): ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit" name="check_email">Next</button>
        </form>
    <?php endif; ?>

    <!-- Step 2: New Password Form -->
    <?php if ($step == "reset"): ?>
        <form method="POST">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>">
            <input type="password" name="password" placeholder="Enter new password" required>
            <button type="submit" name="reset_password">Reset Password</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
