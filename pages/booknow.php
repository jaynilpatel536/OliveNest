<?php
/* ========================= SESSION START ========================= */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ========================= LOGIN CHECK ========================= */
if (!isset($_SESSION['user_id'])) {
    header("Location: /OliveNest/auth/login.php");
    exit;
}

/* ========================= USER SESSION DATA ========================= */
$user_id       = (int) $_SESSION['user_id'];
$user_fullname = $_SESSION['name'];
$user_email    = $_SESSION['email'];

/* ========================= DATABASE CONNECTION ========================= */
require_once __DIR__ . "/../config/db.php";

/* ========================= VALIDATE ROOM ID ========================= */
$room_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($room_id <= 0) {
    die("Invalid Room");
}

/* ========================= FETCH ROOM DETAILS ========================= */
$roomDoc = $db->rooms->findOne(['id' => $room_id]);

if (!$roomDoc) {
    die("Room not found");
}

$room = docToArray($roomDoc);
$room_title = $room['title'];
$room_price = (int) $room['price'];

/* ========================= PRICE CALCULATION ========================= */
$platform_fee = round($room_price * 0.10);
$total_amount = $room_price + $platform_fee;

/* ========================= HANDLE BOOKING ========================= */
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $phone     = trim($_POST['phone']);
    $check_in  = $_POST['check_in'];
    $check_out = $_POST['check_out'];

    $today = date('Y-m-d');
    $days  = (strtotime($check_out) - strtotime($check_in)) / 86400;

    if (empty($phone) || empty($check_in) || empty($check_out)) {
        $error = "All fields are required";
    } elseif ($check_in < $today) {
        $error = "Past dates not allowed";
    } elseif (strtotime($check_out) <= strtotime($check_in)) {
        $error = "Check-out date must be after check-in date";
    } elseif ($days < 2 || $days > 15) {
        $error = "Date duration only 2 up to 15 days";
    } else {

        $booking_id = getNextId($db, 'bookings');
        $db->bookings->insertOne([
            'id'             => $booking_id,
            'user_id'        => $user_id,
            'room_id'        => $room_id,
            'guest_name'     => $user_fullname,
            'guest_email'    => $user_email,
            'guest_phone'    => $phone,
            'check_in'       => $check_in,
            'check_out'      => $check_out,
            'room_price'     => $room_price,
            'platform_fee'   => $platform_fee,
            'total_amount'   => $total_amount,
            'booking_status' => 'pending',
            'created_at'     => new MongoDB\BSON\UTCDateTime()
        ]);

        /* ========================= SAVE SESSION DATA ========================= */
        $_SESSION['booking_id']   = $booking_id;
        $_SESSION['room_price']   = $room_price;
        $_SESSION['platform_fee'] = $platform_fee;
        $_SESSION['total_amount'] = $total_amount;
        $_SESSION['guest_email']  = $user_email;

        header("Location: payment.php");
        exit;
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Book Room | OliveNest</title>

<style>
body {
    background: linear-gradient(135deg,#f5f7fa,#c3cfe2);
    font-family: Arial, sans-serif;
}
.box {
    width: 600px;
    margin: 50px auto;
    background: #fff;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,.2);
}
input, button {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 16px;
}
input[readonly] {
    background: #f1f1f1;
}
button {
    background: #4A90E2;
    color: #fff;
    font-weight: bold;
    cursor: pointer;
}
.price {
    background: #4A90E2;
    color: #fff;
    padding: 15px;
    border-radius: 10px;
    text-align: center;
    margin-bottom: 15px;
}
.error {
    background: #ff4d4d;
    color: #fff;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 10px;
    text-align: center;
}
</style>
</head>

<body>
<div class="box">
    <h2>Book: <?= htmlspecialchars($room_title) ?></h2>

    <div class="price">
        Room: ₹<?= $room_price ?><br>
        Platform Fee: ₹<?= $platform_fee ?><br>
        <b>Total: ₹<?= $total_amount ?></b>
    </div>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" value="<?= htmlspecialchars($user_fullname) ?>" readonly>
        <input type="email" value="<?= htmlspecialchars($user_email) ?>" readonly>
        <input type="text" name="phone" placeholder="Phone" required>

       <input type="date" name="check_in" value="<?= $_SESSION['check_in'] ?? '' ?>" required>
       <input type="date" name="check_out" value="<?= $_SESSION['check_out'] ?? '' ?>" required>


        <button type="submit">Next → Payment</button>
    </form>
</div>
</body>
</html>
