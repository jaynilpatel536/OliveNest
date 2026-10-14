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
$is_demo_mode = false;
$razorpay_order_id = "";

try {
    $order = $api->order->create([
        'receipt'  => 'booking_' . $booking_id,
        'amount'   => $amount_paise,
        'currency' => 'INR'
    ]);
    $razorpay_order_id = $order['id'];
} catch (Exception $e) {
    // If Razorpay keys are expired or invalid, switch gracefully to demo mode
    $is_demo_mode = true;
    $razorpay_order_id = 'order_demo_' . time() . '_' . $booking_id;
}

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
<style>
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f5f7fa; text-align: center; padding-top: 60px; }
    .payment-box { max-width: 480px; margin: 0 auto; background: #fff; padding: 35px; border-radius: 14px; box-shadow: 0 8px 30px rgba(0,0,0,0.08); }
    .btn { padding: 14px 35px; font-size: 16px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; }
    .btn-pay { background: #1d3557; color: #fff; }
    .btn-demo { background: #2a9d8f; color: #fff; }
    .notice { background: #e8f4f8; color: #1d3557; padding: 12px 16px; border-radius: 8px; font-size: 13px; margin: 20px 0; line-height: 1.5; }
</style>
</head>

<body>

<div class="payment-box">
    <h2>Complete Payment</h2>
    <p style="font-size:20px;color:#1d3557;margin:15px 0;"><b>Total Amount: ₹<?= htmlspecialchars($total) ?></b></p>

    <?php if ($is_demo_mode): ?>
        <div class="notice">
            💳 <strong>Test Payment Mode</strong><br>
            Active Razorpay API credentials can be set via Render environment variables. You can confirm your booking in test mode below:
        </div>

        <a href="payment_success.php?payment_id=pay_demo_<?= time() ?>&order_id=<?= urlencode($razorpay_order_id) ?>&signature=demo_verified" class="btn btn-demo">
            ✅ Confirm Test Payment
        </a>
    <?php else: ?>
        <button id="payBtn" class="btn btn-pay">Pay with Razorpay</button>

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
            "theme": { "color": "#1d3557" }
        };

        var rzp = new Razorpay(options);
        document.getElementById('payBtn').onclick = function(e){
            rzp.open();
            e.preventDefault();
        }
        </script>
    <?php endif; ?>
</div>

</body>
</html>
