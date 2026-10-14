<?php
session_start();
/* ===== SAVE BOOKING DATES ===== */
/* ===== CHECK IF BOOKING DATE IS SET ===== */
$has_booking_dates = isset($_SESSION['check_in'], $_SESSION['check_out']);

/* ===== SAVE BOOKING DATES ===== */
if (isset($_POST['save_dates'])) {

    $check_in  = $_POST['check_in'];
    $check_out = $_POST['check_out'];

    $today = date('Y-m-d');
    $days  = (strtotime($check_out) - strtotime($check_in)) / 86400;

    if ($check_in < $today) {
        $date_error = "Past dates not allowed";
    } elseif ($days < 2 || $days > 15) {
        $date_error = "Date duration only 2 up to 15 days";
    } else {
        $_SESSION['check_in']  = $check_in;
        $_SESSION['check_out'] = $check_out;
        $has_booking_dates = true;
    }
}



require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header("Location: rooms.php");
    exit;
}

/* FETCH FROM SESSION OR DATABASE DIRECTLY */
if (isset($_SESSION['rooms_data'][$id])) {
    $room = $_SESSION['rooms_data'][$id];
} else {
    $roomDoc = $db->rooms->findOne(['id' => $id]);
    if (!$roomDoc) {
        header("Location: rooms.php");
        exit;
    }
    $room = docToArray($roomDoc);
    $_SESSION['rooms_data'][$id] = $room;
}

$room_price = (int) $room['price'];
$avg_rating = $room['rating'] ?? 4.5;
$title      = $room['title'];

/* PLATFORM FEE LOGIC */
if ($room_price <= 1500) {
    $platform_fee = 300;
} elseif ($room_price <= 2500) {
    $platform_fee = 700;
} else {
    $platform_fee = 1000;
}
/* ===== CHECK EXISTING BOOKINGS FOR THIS ROOM ===== */
function isDateAvailable($dbOrRoomId, $roomIdOrCheckIn = null, $checkInOrCheckOut = null, $checkOut = null) {
    global $db;

    if ($dbOrRoomId instanceof MongoDB\Database) {
        $database  = $dbOrRoomId;
        $room_id   = (int)$roomIdOrCheckIn;
        $check_in  = $checkInOrCheckOut;
        $check_out = $checkOut;
    } else {
        $database  = $db;
        $room_id   = (int)$dbOrRoomId;
        $check_in  = $roomIdOrCheckIn;
        $check_out = $checkInOrCheckOut;
    }

    $conflict = $database->bookings->findOne([
        'room_id' => $room_id,
        'booking_status' => ['$ne' => 'cancelled'],
        'check_in' => ['$lt' => $check_out],
        'check_out' => ['$gt' => $check_in]
    ]);

    return $conflict === null;
}


$locDoc = $db->rooms->findOne(['id' => (int)$id]);
$loc = $locDoc ? docToArray($locDoc) : [];

$city = $loc['city'] ?? '';
$full_address = $loc['full_address'] ?? '';
$latitude = $loc['latitude'] ?? '';
$longitude = $loc['longitude'] ?? '';


/* STORE FOR NEXT PAGES */
$_SESSION['room_id']       = $id;
$_SESSION['price']         = $room_price;
$_SESSION['platform_fee']  = $platform_fee;
$_SESSION['room_title']    = $title;

/* ===== SAVE BOOKING DATES ===== */
if (isset($_POST['save_dates'])) {

    $check_in  = $_POST['check_in'];
    $check_out = $_POST['check_out'];

    $today = date('Y-m-d');
    $days  = (strtotime($check_out) - strtotime($check_in)) / 86400;

    if ($check_in < $today) {
        $date_error = "Past dates not allowed";

    } elseif ($days < 2 || $days > 15) {
        $date_error = "Date duration only 2 up to 15 days";

    } elseif (!isDateAvailable($db, $id, $check_in, $check_out)) {
        $date_error = "Selected dates are already booked for this room";

    } else {
        $_SESSION['check_in']  = $check_in;
        $_SESSION['check_out'] = $check_out;
        $has_booking_dates = true;
    }
}




/* =========================================
   EXISTING IMAGE & DATA LOGIC
========================================= */

$folder = "../uploads/rooms/room".$id."/";
$images = glob($folder."*.{jpg,jpeg,png,webp}", GLOB_BRACE) ?: [];
if (empty($images)) {
    $assetGallery = glob(__DIR__ . "/../assets/images/1/*.{jpg,jpeg,png,webp}", GLOB_BRACE) ?: [];
    if (!empty($assetGallery)) {
        $images = array_map(function($p) {
            return "/OliveNest/assets/images/1/" . basename($p);
        }, $assetGallery);
    } elseif (file_exists(__DIR__ . "/../assets/images/room" . $id . ".jpg")) {
        $images = ["/OliveNest/assets/images/room" . $id . ".jpg"];
    } else {
        $images = ["/OliveNest/assets/images/room1.jpg"];
    }
}

// Dummy data

$total_reviews = 113;

$amenities = [
    'Wi-Fi'=>'💻',
    'Air Conditioning'=>'❄️',
    'Free Parking'=>'🚗',
    'Private Bathroom'=>'🛁',
    'Kitchen'=>'🍳',
    'Washing Machine'=>'🧺',
    'TV'=>'📺',
];

$sleeping_arrangements = [
    'Bedroom 1'=>'1 double bed',
];

$reviews = [
    ['name'=>'Alice','rating'=>5,'date'=>'Feb 2026','comment'=>'Amazing stay, very clean!'],
    ['name'=>'Bob','rating'=>4,'date'=>'Jan 2026','comment'=>'Great host, location perfect.'],
];

$today = date('Y-m-d');
$bookingCursor = $db->bookings->find([
    'room_id' => (int)$id,
    'booking_status' => ['$ne' => 'cancelled'],
    'check_out' => ['$gte' => $today]
], [
    'sort' => ['check_in' => 1]
]);

$bookings = [];
foreach ($bookingCursor as $doc) {
    $b = docToArray($doc);
    $in = new DateTime($b['check_in']);
    $out = new DateTime($b['check_out']);
    $b['days'] = (int) $in->diff($out)->days;
    $bookings[] = $b;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Room <?php echo $id; ?> · Stay</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body, html {
    margin:0;
    padding:0;
    font-family:'Inter',Arial,sans-serif;
    color:#222;
    background:#fff;
}
.wrapper {
    max-width:1400px;
    margin:auto;
    padding:20px;
}

/* ---------------- PHOTO GRID ---------------- */
.photo-grid {
    display:flex;
    height:80vh;
    gap:10px;
}
.photo-grid .main-img, .photo-grid .side-imgs {
    width:50%;
    height:100%;
}
.photo-grid .main-img {
    overflow:hidden;
    border-radius:16px;
}
.photo-grid .main-img img {
    width:100%;
    height:100%;
    object-fit:cover;
    cursor:pointer;
    transition:transform .3s;
}
.photo-grid .main-img img:hover { transform:scale(1.05); }

.photo-grid .side-imgs {
    display:grid;
    grid-template-columns:1fr 1fr;
    grid-template-rows:1fr 1fr;
    gap:10px;
}
.photo-grid .side-imgs img {
    width:100%;
    height:100%;
    object-fit:cover;
    border-radius:12px;
    cursor:pointer;
    transition:transform .3s;
}
.photo-grid .side-imgs img:hover { transform:scale(1.05); }

.show-overlay {
    position:absolute;
    inset:0;
    background:rgba(0,0,0,0.5);
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:18px;
    font-weight:600;
    border-radius:12px;
    cursor:pointer;
}

/* ---------------- TITLE & RATING ---------------- */
.title-rating {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin:25px 0;
    flex-wrap:wrap;
}
.title-rating h1 {
    font-size:28px;
    margin:0;
    font-weight:700;
}
.stars {
    color:#ff385c;
    font-size:16px;
}
.overall-rating {
    display:flex;
    align-items:center;
    gap:10px;
    font-size:16px;
}

/* ---------------- CONTENT ---------------- */
.content {
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:50px;
}
.section {
    margin-bottom:30px;
}
.section h2 {
    margin-bottom:12px;
    font-weight:600;
    font-size:20px;
}
.section p, .section li {
    line-height:1.8;
    color:#444;
    font-size:15px;
}

/* Amenities */
.amenities {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(120px,1fr));
    gap:15px;
    margin-top:10px;
}
.amenities .amenity {
    display:flex;
    align-items:center;
    gap:8px;
    font-size:15px;
}

/* Sleeping arrangements */
.sleeping {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(150px,1fr));
    gap:10px;
    margin-bottom:20px;
}
.sleeping .room {
    border:1px solid #eee;
    padding:12px;
    border-radius:12px;
    background:#fafafa;
}

/* Booking Card */
.booking {
    border:1px solid #ddd;
    border-radius:16px;
    padding:25px;
    position:sticky;
    top:20px;
    height:fit-content;
    background:#fff;
    box-shadow:0 4px 15px rgba(0,0,0,0.05);
}
.price {
    font-size:24px;
    font-weight:700;
}
.reserve {
    margin-top:18px;
    display:block;
    text-align:center;
    background:#ff385c;
    color:#fff;
    padding:16px;
    border-radius:12px;
    text-decoration:none;
    font-size:18px;
    font-weight:600;
}

/* Reviews */
.reviews {
    margin-top:35px;
}
.review {
    border-top:1px solid #eee;
    padding:18px 0;
}
.review .stars {
    color:#ff385c;
    font-size:14px;
    margin-bottom:6px;
}
.date-overlay {
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.6);
    z-index:9999;
}
.date-box {
    background:#fff;
    width:380px;
    margin:12% auto;
    padding:25px;
    border-radius:16px;
}
.date-box h3 {
    margin-bottom:15px;
}
.field {
    margin-bottom:12px;
}
.field label {
    font-size:14px;
    font-weight:500;
    display:block;
    margin-bottom:4px;
}
.field input {
    width:100%;
    padding:10px;
    border-radius:8px;
    border:1px solid #ccc;
}
.date-note {
    color:#e63946;
    font-size:13px;
    margin:10px 0;
}
.date-error {
    background:#ffe5e5;
    color:#c1121f;
    padding:8px;
    border-radius:8px;
    margin-bottom:10px;
}
.full {
    width:100%;
}
.cancel {
    width:100%;
    margin-top:10px;
    padding:12px;
    border-radius:10px;
    border:none;
    background:#ddd;
}


/* Fullscreen Grid */
#photoGrid {
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.95);
    z-index:9999;
    overflow-y:auto;
    padding:40px 20px;
}
#photoGrid .closeGrid {
    position:absolute;
    top:20px;
    right:30px;
    font-size:32px;
    color:#fff;
    cursor:pointer;
}
#photoGrid .gridModal {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:10px;
}
#photoGrid .gridModal img {
    width:100%;
    height:200px;
    object-fit:cover;
    border-radius:10px;
    cursor:pointer;
    transition:transform .3s;
}
#photoGrid .gridModal img:hover { transform:scale(1.05); }

/* Lightbox */
#lightbox {
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.95);
    z-index:10000;
    align-items:center;
    justify-content:center;
}
#lightbox img {
    max-width:90%;
    max-height:85%;
}
#lightbox .closeBox {
    position:absolute;
    top:20px;
    right:30px;
    font-size:32px;
    color:#fff;
    cursor:pointer;
}

/* ---------------- RESPONSIVE ---------------- */
@media (max-width:900px) {
    .content { grid-template-columns:1fr; gap:20px; }
    .booking { position:relative; top:unset; margin-top:20px; }
    .photo-grid { flex-direction:column; height:auto; gap:10px; }
    .photo-grid .main-img, .photo-grid .side-imgs { width:100%; height:250px; }
    .photo-grid .side-imgs { display:grid; grid-template-columns:repeat(2,1fr); grid-template-rows:auto; gap:10px; }
}
</style>

</head>

<body>
<div class="wrapper">

<!-- Photo Grid -->
<div class="photo-grid">
    <!-- Left: Main Image -->
    <div class="main-img">
        <?php if(isset($images[0])): ?><img src="<?php echo $images[0]; ?>" onclick="openLightbox('<?php echo $images[0]; ?>')"><?php endif; ?>
    </div>

    <!-- Right: 4-image Grid -->
    <div class="side-imgs">
        <?php 
        $total_images = count($images);
        for($i=1;$i<5 && $i<$total_images;$i++): ?>
            <div style="position:relative;">
                <img src="<?php echo $images[$i]; ?>" onclick="openLightbox('<?php echo $images[$i]; ?>')">
                <?php if($i==4 && $total_images>5): ?>
                    <div class="show-overlay" onclick="openPhotoGrid()">Show all photos</div>
                <?php endif; ?>
            </div>
        <?php endfor; ?>
    </div>
</div>

<!-- Title & Rating -->
<div class="title-rating">
    <h1>Room <?php echo $id; ?></h1>
    <div class="overall-rating">
        <div class="stars"><?php for ($i = 1; $i <= 5; $i++) { echo $i <= floor($avg_rating) ? '★' : '☆';} ?>
</div>
        <div><?php echo $avg_rating; ?> · <?php echo $total_reviews; ?> reviews</div>
    </div>
</div>

<!-- Main Content -->
<div class="content">

<!-- Left Column -->

<div>
    <div class="section">
        <h2>Where you'll sleep</h2>
        <div class="sleeping">
            <?php foreach($sleeping_arrangements as $room=>$bed): ?>
            <div class="room"><strong><?php echo $room; ?>:</strong> <?php echo $bed; ?></div>
            <?php endforeach; ?>
        </div>
    </div>

<div style="margin-top:20px;">
    <h2>Location</h2>
    <p>📍 <?php echo htmlspecialchars($full_address); ?></p>
    <?php if($latitude && $longitude): ?>
        <a href="https://www.google.com/maps/search/?api=1&query=<?php echo $latitude; ?>,<?php echo $longitude; ?>" 
           target="_blank" 
           style="display:inline-block;padding:10px 20px;background:#1d3557;color:#fff;border-radius:8px;text-decoration:none;margin-top:10px;">
           Open in Google Maps
        </a>
    <?php endif; ?>
</div>

    <div class="section">
        <h2>What this place offers</h2>
        <div class="amenities">
            <?php foreach($amenities as $name=>$icon): ?>
            <div class="amenity"><?php echo $icon; ?> <?php echo $name; ?></div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="section">
        <h2>Description</h2>
        <p>Relax in a stylish Airbnb-inspired room with premium comfort, peaceful surroundings, and modern amenities. Perfect for solo travelers, couples, or small families. Enjoy a cozy bedroom, clean bathroom, fully equipped kitchen, and all the amenities you need for a comfortable stay.</p>
    </div>

    <div class="reviews">
        <h2>Reviews</h2>
        <?php foreach($reviews as $rev): ?>
        <div class="review">
            <div class="stars"><?php for($i=1;$i<=5;$i++){ echo $i<=$rev['rating']?'★':'☆'; } ?></div>
            <strong><?php echo $rev['name']; ?></strong> · <small><?php echo $rev['date']; ?></small>
            <p><?php echo $rev['comment']; ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Right Column: Booking -->
<div class="booking">
    <div class="price">
    ₹<?php echo $room_price; ?>
    <span style="font-size:14px;color:#666;">/per night</span>
</div>
<a class="reserve" style="background:#1d3557;margin-top:10px;" onclick="openDateBox()">Booking Date  </a>

<?php if(isset($_SESSION['username'])): ?>

    <?php if($has_booking_dates): ?>
        <a class="reserve" href="booknow.php?id=<?= $id ?>">Book Now</a>
    <?php else: ?>
        <a class="reserve" onclick="alert('First add booking date')" style="background:#aaa;cursor:not-allowed;">Book Now</a>
    <?php endif; ?>

<?php else: ?>
    <a class="reserve" href="/OliveNest/auth/login.php">Book Now</a>
<?php endif; ?>

<h3 style="margin-top:40px;">Already Booked Dates</h3>

<table border="1" cellpadding="10" cellspacing="0" width="100%">
<tr>
    <th>No</th>
    <th>Check-in</th>
    <th>Check-out</th>
    <th>Total Days</th>
</tr>

<?php $i=1; foreach($bookings as $b): ?>
<tr>
    <td><?= $i++ ?></td>
    <td><?= $b['check_in'] ?></td>
    <td><?= $b['check_out'] ?></td>
    <td><?= $b['days'] ?></td>
</tr>
<?php endforeach; ?>
</table>

</div>

</div>
</div>

<!-- Fullscreen Photo Grid -->
<div id="photoGrid">
    <span class="closeGrid" onclick="closePhotoGrid()">×</span>
    <div class="gridModal">
        <?php foreach($images as $img): ?>
            <img src="<?php echo $img; ?>" onclick="openLightbox('<?php echo $img; ?>')">
        <?php endforeach; ?>
    </div>
</div>

<!-- Lightbox -->
<div id="lightbox">
    <span class="closeBox" onclick="closeLightbox()">×</span>
    <img id="lbImg">
</div>

<!-- Booking Date Modal -->
<div id="dateBox" class="date-overlay">
  <div class="date-box">
    <h3>Select Booking Dates</h3>

    <?php if (count($bookings) === 0): ?>
    <tr>
      <td colspan="4" style="text-align:center;color:#777;">
        No upcoming bookings for this room
      </td>
    </tr>
    <?php endif; ?>

    <form method="POST">
        <div class="field">
            <label>Check-in</label>
            <input type="date" name="check_in" required>
        </div>

        <div class="field">
            <label>Check-out</label>
            <input type="date" name="check_out" required>
        </div>

        <p class="date-note">Date duration only 2 up to 15 days</p>

        <button type="submit" name="save_dates" class="reserve full">Save Dates</button>
        <button type="button" onclick="closeDateBox()" class="cancel">Cancel</button>
    </form>
  </div>
</div>



<script>
function openPhotoGrid(){document.getElementById('photoGrid').style.display='block';}
function closePhotoGrid(){document.getElementById('photoGrid').style.display='none';}
function openLightbox(src){document.getElementById('lightbox').style.display='flex';document.getElementById('lbImg').src=src;}
function closeLightbox(){document.getElementById('lightbox').style.display='none';}
</script>
<script>
function openDateBox(){
    document.getElementById('dateBox').style.display='block';
}
function closeDateBox(){
    document.getElementById('dateBox').style.display='none';
}
</script>


</body>
</html>
