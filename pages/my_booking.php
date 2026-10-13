<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['username'])) {
    header("Location: /OliveNest/auth/login.php");
    exit();
}

$username = $_SESSION['username'];

/* =========================
   FETCH USER EMAIL
========================= */
$userDoc = $db->users->findOne(['username' => $username]);

if (!$userDoc) {
    die("User not found");
}

$user = docToArray($userDoc);
$user_email = $user['email'];

/* =========================
   CANCEL BOOKING (NO OUTPUT BEFORE HEADER)
========================= */
if (isset($_GET['cancel_id'])) {
    $cancel_id = (int) $_GET['cancel_id'];

    $db->bookings->updateOne(
        ['id' => $cancel_id, 'guest_email' => $user_email],
        ['$set' => ['booking_status' => 'cancelled']]
    );

    header("Location: my_booking.php");
    exit();
}

/* =========================
   FETCH BOOKINGS + PAYMENTS
========================= */
$cursor = $db->bookings->aggregate([
    ['$match' => ['guest_email' => $user_email]],
    ['$lookup' => [
        'from' => 'payments',
        'localField' => 'id',
        'foreignField' => 'booking_id',
        'as' => 'payment_info'
    ]],
    ['$addFields' => [
        'payment_status' => [
            '$ifNull' => [
                ['$arrayElemAt' => ['$payment_info.status', 0]],
                'pending'
            ]
        ]
    ]],
    ['$sort' => ['created_at' => -1]]
]);

$bookings = [];
foreach ($cursor as $doc) {
    $bookings[] = docToArray($doc);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Bookings - OliveNest</title>

<style>
:root {
    --primary: #1d3557;
    --secondary: #457b9d;
    --success: #2a9d8f;
    --warning: #e9c46a;
    --danger: #e63946;
    --bg: #f4f6f8;
    --card: #ffffff;
    --text-dark: #1f2937;
    --text-light: #6b7280;
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: "Segoe UI", Roboto, Arial, sans-serif;
    background: var(--bg);
    color: var(--text-dark);
}

.page-wrapper {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.content-area {
    flex: 1;
    padding: 40px 0;
}

.container {
    max-width: 1100px;
    margin: auto;
    padding: 0 20px;
}

h2 {
    font-size: 28px;
    font-weight: 600;
    color: var(--primary);
    margin-bottom: 30px;
}

/* Booking Card */
.card {
    background: var(--card);
    border-radius: 14px;
    padding: 24px;
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    gap: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    transition: transform .2s ease, box-shadow .2s ease;
}

.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 16px 40px rgba(0,0,0,0.12);
}

/* Left Content */
.card p {
    margin: 6px 0;
    font-size: 14.5px;
    color: var(--text-light);
}

.card strong {
    color: var(--text-dark);
}

.amount {
    margin-top: 12px;
    font-size: 20px;
    font-weight: 700;
    color: var(--primary);
}

/* Status */
.status {
    display: inline-block;
    padding: 6px 14px;
    border-radius: 30px;
    font-size: 12px;
    font-weight: 600;
    margin-left: 6px;
}

.confirmed { background: #d1fae5; color: #065f46; }
.pending   { background: #fef3c7; color: #92400e; }
.cancelled { background: #fee2e2; color: #991b1b; }

/* Actions */
.actions {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 12px;
    min-width: 170px;
}

.btn {
    padding: 10px 16px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    text-align: center;
    color: #fff;
}

.view {
    background: var(--secondary);
}

.view:hover {
    background: var(--primary);
}

.cancel {
    background: var(--danger);
}

.cancel:hover {
    background: #c53030;
}

.disabled {
    background: #d1d5db;
    cursor: not-allowed;
}

/* Empty State */
.empty {
    background: #fff;
    padding: 40px;
    border-radius: 14px;
    text-align: center;
    color: var(--text-light);
}

/* Responsive */
@media (max-width: 768px) {
    .card {
        flex-direction: column;
    }
    .actions {
        flex-direction: row;
    }
}
</style>
</head>

<body>

<div class="page-wrapper">

<?php include "../includes/header.php"; ?>

<main class="content-area">
<div class="container">

<h2>My Bookings</h2>

<?php if (empty($bookings)): ?>
    <div class="empty">You have not made any bookings yet.</div>
<?php endif; ?>

<?php foreach ($bookings as $row): ?>
<div class="card">

    <div>
        <p><strong>Booking ID:</strong> #<?= $row['id']; ?></p>
        <p><strong>Guest:</strong> <?= htmlspecialchars($row['guest_name']); ?></p>
        <p><strong>Dates:</strong> <?= $row['check_in']; ?> → <?= $row['check_out']; ?></p>

        <p>
            <strong>Booking:</strong>
            <span class="status <?= $row['booking_status']; ?>">
                <?= ucfirst($row['booking_status']); ?>
            </span>
        </p>

        <p>
            <strong>Payment:</strong>
            <span class="status <?= $row['payment_status']; ?>">
                <?= ucfirst($row['payment_status']); ?>
            </span>
        </p>

        <div class="amount">₹<?= number_format($row['total_amount'], 2); ?></div>
    </div>

    <div class="actions">
        <a href="room-details.php?id=<?= $row['room_id']; ?>" class="btn view">View Room</a>

        <?php if ($row['booking_status'] !== 'cancelled'): ?>
            <a href="my_booking.php?cancel_id=<?= $row['id']; ?>"
               onclick="return confirm('Are you sure you want to cancel this booking?');"
               class="btn cancel">
               Cancel Booking
            </a>
        <?php else: ?>
            <span class="btn disabled">Cancelled</span>
        <?php endif; ?>
    </div>

</div>
<?php endforeach; ?>

</div>
</main>

<?php include "../includes/footer.php"; ?>

</div>

</body>
</html>
