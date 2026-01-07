<?php
session_start();


require_once __DIR__ . '/../config/db.php';



$error = "";

if (isset($_POST['login'])) {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Fetch user by username
        $sql = "SELECT * FROM users WHERE username = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {

        $row = mysqli_fetch_assoc($result);

        // ✅ Verify hashed password
        if (password_verify($password, $row['password'])) {

            // Store session values
            $_SESSION['user_id'] = (int) $row['user_id'];
            $_SESSION['name']     = $row['full_name'];
            $_SESSION['email']    = $row['email'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role']     = $row['role'];

            // Redirect after login
           header("Location: /OliveNest/index.php");
                exit;


        } else {
            $error = "Invalid Username or Password!";
        }

    } else {
        $error = "Invalid Username or Password!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}

        body{
            display:flex;
            justify-content:center;
            align-items:center;
            min-height:100vh;
            background-image:url('/OliveNest/assets/images/background.jpg');
            background-size:cover;
            background-position:center;
            background-repeat:no-repeat;
        }

        .login-box{
            width:400px;
            padding:50px 40px;
            background:rgba(255,255,255,0.1);
            border-radius:20px;
            backdrop-filter:blur(15px);
            border:1px solid rgba(255,255,255,0.2);
            color:white;
            text-align:center;
        }

        h2{font-size:2rem;margin-bottom:30px;}

        .input-group{position:relative;margin-bottom:20px;}

        .input-group input{
            width:100%;
            padding:12px 20px;
            padding-right:40px;
            border-radius:30px;
            border:2px solid rgba(255,255,255,0.3);
            background:transparent;
            outline:none;
            color:white;
        }

        .input-group input::placeholder{color:rgba(255,255,255,0.7);}
        .input-group i{position:absolute;right:15px;top:50%;transform:translateY(-50%);}

        .options{display:flex;justify-content:space-between;margin-bottom:20px;}
        .options a{color:white;}

        .btn-login{
            width:100%;
            padding:12px;
            background:white;
            color:#2b1055;
            border:none;
            border-radius:30px;
            font-weight:bold;
            cursor:pointer;
        }

        .register-link{margin-top:20px;}
        .register-link a{color:white;font-weight:bold;}

        .error{
            background:rgba(255,0,0,0.4);
            padding:10px;
            border-radius:10px;
            margin-bottom:15px;
        }
    </style>
</head>

<body>

<div class="login-box">
    <h2>Login</h2>

    <?php if(!empty($error)) : ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">

        <div class="input-group">
            <input type="text" name="username" placeholder="Username" required>
            <i class="fas fa-user"></i>
        </div>

        <div class="input-group">
            <input type="password" name="password" placeholder="Password" required>
            <i class="fas fa-lock"></i>
        </div>

        <div class="options">
            <label><input type="checkbox" name="remember"> Remember me</label>
            <a href="forgot_password.php">Forgot password?</a>
        </div>

        <button type="submit" name="login" class="btn-login">Login</button>

        <div class="register-link">
            <p>Don't have an account? <a href="registration.php">Register</a></p>
        </div>

    </form>
</div>

</body>
</html>