<?php 
require_once __DIR__ . '/../includes/header.php'; 
require __DIR__ . '/../config/db.php'; 

$sort = $_GET['sort'] ?? '';
$sortOrder = ['rating' => -1]; // default (Recommended)

// Determine sorting
switch ($sort) {
    case 'price_low': $sortOrder = ['price' => 1]; break;
    case 'price_high': $sortOrder = ['price' => -1]; break;
    case 'rating': $sortOrder = ['rating' => -1]; break;
    case 'city': $sortOrder = ['city' => 1]; break;
    case 'newest': $sortOrder = ['id' => -1]; break;
}

$cursor = $db->rooms->find([], ['sort' => $sortOrder]);
$rooms = [];
foreach ($cursor as $doc) {
    $rooms[] = docToArray($doc);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rooms - OliveNest</title>
    <style>
        body { font-family: Arial; background:#f3f3f3; margin:0; padding:0; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; padding: 30px; }
        .card { background: white; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); overflow: hidden; transition: 0.3s; }
        .card:hover { transform: translateY(-5px); }
        .card img { width: 100%; height: 200px; object-fit: cover; }
        .card-body { padding: 15px; }
        .stars { color: #ff385c; font-size: 14px; margin-bottom: 6px; }
        .price { font-weight: bold; margin-top:6px; }
        .rooms-header { display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; padding: 20px 30px 10px; }
        .rooms-title { text-align: center; margin: 0; }
        .sort-form { justify-self: end; }
        .sort-form select { padding: 8px 14px; border-radius: 8px; border: 1px solid #ccc; background: #fff; font-size: 14px; cursor: pointer; }

        /* Mobile */
        @media (max-width: 768px) {
            .rooms-header { grid-template-columns: 1fr; gap: 12px; }
            .rooms-title { text-align: center; }
            .sort-form { justify-self: center; }
        }
    </style>
</head>
<body>

<div class="rooms-header">
    <div></div> <!-- empty left spacer -->
    <h1 class="rooms-title">Explore Our Rooms</h1>
    <form method="GET" class="sort-form">
        <select name="sort" onchange="this.form.submit()">
            <option value="">Recommended</option>
            <option value="price_low" <?= ($sort=='price_low')?'selected':'' ?>>Price: Low → High</option>
            <option value="price_high" <?= ($sort=='price_high')?'selected':'' ?>>Price: High → Low</option>
            <option value="rating" <?= ($sort=='rating')?'selected':'' ?>>Top Rated</option>
            <option value="city" <?= ($sort=='city')?'selected':'' ?>>City (A–Z)</option>
            <option value="newest" <?= ($sort=='newest')?'selected':'' ?>>Newest</option>
        </select>
    </form>
</div>

<p style="text-align:center;color:#666;font-size:14px;">
    Showing homes sorted by <strong><?= $sort ?: 'Recommended'; ?></strong>
</p>

<div class="grid">
    <?php foreach ($rooms as $r) : ?>
        <?php 
        // Store room data in session
        $_SESSION['rooms_data'][$r['id']] = [
            'title' => $r['title'],
            'price' => $r['price'],
            'rating' => $r['rating'],
            'city' => $r['city']
        ];

        // Image logic: Check uploads first, then assets/images/room{id}.jpg
        $imgDir = "../uploads/rooms/room" . $r['id'] . "/";
        $imgs = glob($imgDir . "*.{jpg,jpeg,png,webp}", GLOB_BRACE) ?: [];
        if (!empty($imgs)) {
            $cover = $imgs[0];
        } elseif (file_exists(__DIR__ . "/../assets/images/room" . $r['id'] . ".jpg")) {
            $cover = "/OliveNest/assets/images/room" . $r['id'] . ".jpg";
        } else {
            $cover = "/OliveNest/assets/images/room1.jpg";
        }
        ?>
        <div class="card">
            <a href="room-details.php?id=<?= $r['id'] ?>">
                <img src="<?= $cover ?>" alt="<?= htmlspecialchars($r['title']) ?>">
            </a>
            <div class="card-body">
                <h3><?= htmlspecialchars($r['title']) ?></h3>
                <p style="color:#666;font-size:14px;">📍 <?= htmlspecialchars($r['city']) ?></p>
                <div class="stars">
                    <?php 
                    $full = floor($r['rating']); 
                    for ($i=0; $i<5; $i++) echo $i < $full ? "★" : "☆"; 
                    ?>
                    <span><?= $r['rating'] ?></span>
                </div>
                <p><?= htmlspecialchars($r['description']) ?></p>
                <p class="price">₹<?= $r['price'] ?> / night</p>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
