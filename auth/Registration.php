<?php
require_once __DIR__ . '/../config/db.php';


$success = false;
$error = "";

if (isset($_POST['register'])) {
    
    $fullname = trim($_POST['fullname']);
    $email    = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = $_POST['role'];

    // Check if username or email already exists (NO column dependency)
    $checkSql = "SELECT 1 FROM users WHERE username = ? OR email = ?";
    $stmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($stmt, "ss", $username, $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        $error = "Username or Email already exists.";
    } else {

        // Insert user securely
        $insertSql = "INSERT INTO users (full_name, email, username, password, role)
                      VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insertSql);
        mysqli_stmt_bind_param($stmt, "sssss", $fullname, $email, $username, $password, $role);

        if (mysqli_stmt_execute($stmt)) {
            $success = true;
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Register</title>

<style>
body {
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background-image: url('/OliveNest/assets/images/background.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

.glass-box {
    width: 380px;
    padding: 35px;
    background: rgba(255, 255, 255, 0.18);
    border-radius: 18px;
    box-shadow: 0 0 25px rgba(0, 0, 0, 0.35);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: white;
    text-align: center;
}

h2 {
    font-size: 28px;
    margin-bottom: 15px;
}

input, select {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    background: transparent;
    border: none;
    border-bottom: 1px solid white;
    color: white;
    font-size: 15px;
    outline: none;
}

select option {
    color: black;
}

button {
    width: 100%;
    padding: 12px;
    margin-top: 20px;
    background: white;
    color: #333;
    font-size: 17px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

a {
    color: white;
}

/* ===== MODALS ===== */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.4);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 999;
}

.modal-content {
    background: white;
    padding: 25px 30px;
    border-radius: 14px;
    text-align: center;
    width: 320px;
    box-shadow: 0 0 20px rgba(0,0,0,0.3);
}

.modal-content h3 {
    margin-bottom: 10px;
    color: #333;
}

.modal-content p {
    color: #555;
}

.modal-content button {
    margin-top: 15px;
    padding: 10px 25px;
    border: none;
    background: #007bff;
    color: white;
    border-radius: 6px;
    cursor: pointer;
}
</style>
</head>

<body>

<div class="glass-box">
    <h2>Create Account</h2>

    <form method="POST">
        <input type="text" name="fullname" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>

        <select name="role" required>
            <option value="">Select Role</option>
            <option value="guest">Guest</option>
            <option value="owner">Owner</option>
        </select>

        <button type="submit" name="register">Register</button>
    </form>

    <p style="margin-top:10px;">
        Already have an account? <a href="login.php">Login</a>
    </p>
</div>

<!-- ===== SUCCESS MODAL ===== -->
<div id="successModal" class="modal">
    <div class="modal-content">
        <h3>Registration Successful</h3>
        <p>Your account has been created successfully.</p>
        <button onclick="window.location.href='login.php'">OK</button>
    </div>
</div>

<!-- ===== ERROR MODAL ===== -->
<div id="errorModal" class="modal">
    <div class="modal-content">
        <h3>Registration Failed</h3>
        <p><?php echo htmlspecialchars($error); ?></p>
        <button onclick="document.getElementById('errorModal').style.display='none'">
            OK
        </button>
    </div>
</div>

<?php if ($success) { ?>
<script>
    document.getElementById("successModal").style.display = "flex";
</script>
<?php } ?>

<?php if (!empty($error)) { ?>
<script>
    document.getElementById("errorModal").style.display = "flex";
</script>
<?php } ?>

</body>
</html>
