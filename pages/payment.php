<?php
/* ========================= SESSION START ========================= */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ========================= REQUIRED SESSION CHECK ========================= */
if (
    !isset(
        $_SESSION['user_id'],
        $_SESSION['booking_id'],
        $_SESSION['total_amount']
    )
) {
    header("Location: rooms.php");
    exit;
}

$user_id    = (int) $_SESSION['user_id'];
$booking_id = (int) $_SESSION['booking_id'];
$total      = (int) $_SESSION['total_amount'];

/* ========================= DB CONNECTION ========================= */
require_once __DIR__ . '/../config/db.php';

/* ========================= RAZORPAY SDK ========================= */
use Razorpay\Api\Api;

/* 🔑 Keys read from environment (set in Render dashboard) */
$keyId     = getenv('RAZORPAY_KEY_ID')     ?: 'rzp_test_RxT2clH6hdkSUa';
$keySecret = getenv('RAZORPAY_KEY_SECRET') ?: '8h1iRtm1girEIfd38Wf5levy';
$api = new Api($keyId, $keySecret);

/* ========================= AMOUNT ========================= */
$amount_paise = $total * 100; // Razorpay requires paise

/* ========================= CREATE RAZORPAY ORDER ========================= */
$order = $api->order->create([
    'receipt'  => 'booking_' . $booking_id,
    'amount'   => $amount_paise,
    'currency' => 'INR'
]);

$razorpay_order_id = $order['id'];

/* ========================= INSERT PAYMENT (PENDING) ========================= */
$payment_id = getNextId($db, 'payments');
$db->payments->insertOne([
    'id'                  => $payment_id,
    'user_id'             => $user_id,
    'booking_id'          => $booking_id,
    'razorpay_order_id'   => $razorpay_order_id,
    'razorpay_payment_id' => null,
    'amount'              => $total,
    'status'              => 'pending',
    'created_at'          => new MongoDB\BSON\UTCDateTime()
]);

/* ========================= STORE ORDER ID ========================= */
$_SESSION['razorpay_order_id'] = $razorpay_order_id;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment | OliveNest</title>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>

<body style="font-family:Arial;background:#f5f7fa;text-align:center;padding-top:50px;">

<h2>Complete Payment</h2>
<p><b>Total Amount: ₹<?= htmlspecialchars($total) ?></b></p>

<button id="payBtn" style="
    padding: 15px 35px; 
    font-size: 18px; 
    background: #1d3557; 
    color: #fff; 
    border: none; 
    border-radius: 8px; 
    cursor: pointer;
">
Pay Now
</button>

<script>
var options = {
    "key": "<?= $keyId ?>",
    "amount": "<?= $amount_paise ?>",
    "currency": "INR",
    "name": "OliveNest",
    "description": "Room Booking Payment",
    "order_id": "<?= $razorpay_order_id ?>",
    "handler": function (response) {
        window.location.href =
            "payment_success.php?payment_id=" + response.razorpay_payment_id +
            "&order_id=" + response.razorpay_order_id +
            "&signature=" + response.razorpay_signature;
    },
    "theme": {
        "color": "#1d3557"
    }
};

var rzp = new Razorpay(options);
document.getElementById('payBtn').onclick = function(e){
    rzp.open();
    e.preventDefault();
}
</script>

</body>
</html>
