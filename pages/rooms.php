<?php 
require_once __DIR__ . '/../includes/header.php'; 
require __DIR__ . '/../config/db.php'; 

$sort = $_GET['sort'] ?? '';
$orderBy = "ORDER BY rating DESC"; // default (Recommended)

// Determine sorting
switch ($sort) {
    case 'price_low': $orderBy = "ORDER BY price ASC"; break;
    case 'price_high': $orderBy = "ORDER BY price DESC"; break;
    case 'rating': $orderBy = "ORDER BY rating DESC"; break;
    case 'city': $orderBy = "ORDER BY city ASC"; break;
    case 'newest': $orderBy = "ORDER BY id DESC"; break;
}

$query = "SELECT id, title, city, price, rating, description FROM rooms $orderBy";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
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
    <?php while ($r = mysqli_fetch_assoc($result)) : ?>
        <?php 
        // Store room data in session
        $_SESSION['rooms_data'][$r['id']] = [
            'title' => $r['title'],
            'price' => $r['price'],
            'rating' => $r['rating'],
            'city' => $r['city']
        ];

        // Image logic
        $imgDir = "../uploads/rooms/room" . $r['id'] . "/";
        $imgs = glob($imgDir . "*.{jpg,jpeg,png,webp}", GLOB_BRACE);
        $cover = $imgs[0] ?? "../assets/images/no-image.jpg";
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
    <?php endwhile; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
