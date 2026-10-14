<?php
/* =========================
   SESSION START
========================= */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =========================
   VALIDATION
========================= */
if (
    !isset($_GET['payment_id'], $_GET['order_id'], $_GET['signature']) ||
    !isset($_SESSION['razorpay_order_id'], $_SESSION['booking_id'])
) {
    die("Invalid Payment Access");
}

$payment_id = $_GET['payment_id'];
$order_id   = $_GET['order_id'];
$signature  = $_GET['signature'];

/* =========================
   DB CONNECTION
========================= */
require_once __DIR__ . '/../config/db.php';

/* =========================
   RAZORPAY SDK
========================= */
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

/* 🔑 Keys read from environment (set in Render dashboard) */
$keyId     = getenv('RAZORPAY_KEY_ID')     ?: 'rzp_test_RxT2clH6hdkSUa';
$keySecret = getenv('RAZORPAY_KEY_SECRET') ?: '8h1iRtm1girEIfd38Wf5levy';

$api = new Api($keyId, $keySecret);

try {

    /* =========================
       VERIFY SIGNATURE
    ========================= */
    if (strpos($order_id, 'order_demo_') === 0 && $signature === 'demo_verified') {
        // Verified in Demo Mode
    } else {
        $api->utility->verifyPaymentSignature([
            'razorpay_order_id'    => $_SESSION['razorpay_order_id'],
            'razorpay_payment_id'  => $payment_id,
            'razorpay_signature'   => $signature
        ]);
    }

    /* =========================
       CHECK PAYMENT EXISTS
    ========================= */
    $payment = $db->payments->findOne([
        'razorpay_order_id' => $order_id,
        'status'            => 'pending'
    ]);

    if (!$payment) {
        die("Payment record not found or already processed");
    }

    /* =========================
       UPDATE PAYMENT STATUS
    ========================= */
    $db->payments->updateOne(
        ['razorpay_order_id' => $order_id],
        ['$set' => [
            'razorpay_payment_id' => $payment_id,
            'status'              => 'success'
        ]]
    );

    /* =========================
       CONFIRM BOOKING
    ========================= */
    $db->bookings->updateOne(
        ['id' => (int) $_SESSION['booking_id']],
        ['$set' => ['booking_status' => 'confirmed']]
    );

    /* =========================
       CLEAN ONLY PAYMENT SESSION
    ========================= */
    unset(
        $_SESSION['booking_id'],
        $_SESSION['total_amount'],
        $_SESSION['razorpay_order_id']
    );

    /* =========================
       SET FLASH MESSAGE
    ========================= */
    $_SESSION['flash_message'] = "✅ Payment successful! Your booking is confirmed.";

    /* =========================
       REDIRECT TO INDEX
    ========================= */
    header("Location: ../index.php");
    exit;

} catch (SignatureVerificationError $e) {

    /* =========================
       PAYMENT FAILED
    ========================= */
    $db->payments->updateOne(
        ['razorpay_order_id' => $order_id],
        ['$set' => ['status' => 'failed']]
    );

    die("Payment verification failed");
}
