--><?php
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
$conn = mysqli_connect("localhost", "root", "", "olivenest_db");
if (!$conn) {
    die("Database Connection Failed");
}

/* =========================
   RAZORPAY SDK
========================= */
require __DIR__ . '/../vendor/autoload.php';
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

/* SAME KEYS */
$keyId     = "rzp_test_RxT2clH6hdkSUa";
$keySecret = "8h1iRtm1girEIfd38Wf5levy";

$api = new Api($keyId, $keySecret);

try {

    /* =========================
       VERIFY SIGNATURE
    ========================= */
    $api->utility->verifyPaymentSignature([
        'razorpay_order_id'    => $_SESSION['razorpay_order_id'],
        'razorpay_payment_id'  => $payment_id,
        'razorpay_signature'   => $signature
    ]);

    /* =========================
       CHECK PAYMENT EXISTS
    ========================= */
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id FROM payments WHERE razorpay_order_id = ? AND status = 'pending'"
    );
    mysqli_stmt_bind_param($stmt, "s", $order_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if (!mysqli_fetch_assoc($res)) {
        die("Payment record not found or already processed");
    }

    /* =========================
       UPDATE PAYMENT STATUS
    ========================= */
    $stmt = mysqli_prepare(
        $conn,
        "UPDATE payments
         SET razorpay_payment_id = ?, status = 'success'
         WHERE razorpay_order_id = ?"
    );
    mysqli_stmt_bind_param($stmt, "ss", $payment_id, $order_id);
    mysqli_stmt_execute($stmt);

    /* =========================
       CONFIRM BOOKING
    ========================= */
    $stmt = mysqli_prepare(
        $conn,
        "UPDATE bookings
         SET booking_status = 'confirmed'
         WHERE id = ?"
    );
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['booking_id']);
    mysqli_stmt_execute($stmt);

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
    $stmt = mysqli_prepare(
        $conn,
        "UPDATE payments
         SET status = 'failed'
         WHERE razorpay_order_id = ?"
    );
    mysqli_stmt_bind_param($stmt, "s", $order_id);
    mysqli_stmt_execute($stmt);

    die("Payment verification failed");
}
