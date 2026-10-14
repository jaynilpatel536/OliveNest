<?php 
require __DIR__ . '/includes/header.php'; 
require __DIR__ . '/config/db.php'; // Database connection

// Fetch rooms from database
$sort = $_GET['sort'] ?? 'recommended';
$sortOrder = ['rating' => -1]; // Recommended (default)

if ($sort === 'price_low') {
    $sortOrder = ['price' => 1];
} elseif ($sort === 'price_high') {
    $sortOrder = ['price' => -1];
} elseif ($sort === 'rating') {
    $sortOrder = ['rating' => -1];
} elseif ($sort === 'city') {
    $sortOrder = ['city' => 1];
} elseif ($sort === 'newest') {
    $sortOrder = ['id' => -1];
}

$cursor = $db->rooms->find([], [
    'sort' => $sortOrder,
    'limit' => 8
]);

$rooms = [];
foreach ($cursor as $doc) {
    $rooms[] = docToArray($doc);
}

// Flash message
if (isset($_SESSION['flash_message'])) {
    echo '<div style="background:#d4edda; padding:15px; margin:10px 0; border:1px solid #c3e6cb; color:#155724; border-radius:5px; text-align:center;">' 
        . $_SESSION['flash_message'] . '</div>';
    unset($_SESSION['flash_message']); // Remove after showing once
}
?>
<style>
    .page-space{height:6px;}
    .container{width:90%;max-width:1300px;margin:auto;}
    /* HERO */
    .hero{position:relative;background:linear-gradient(135deg,#f4f7fb,#ffffff);padding:90px 0 80px;overflow:hidden;}
    .hero::before{content:'';position:absolute;top:-100px;left:-100px;width:300px;height:300px;background:rgba(29,53,87,0.07);border-radius:50%;}
    .hero-wrap{display:grid;grid-template-columns:1.1fr 1fr;gap:60px;align-items:center;position:relative;z-index:2;}
    .hero-content{animation:slideUp 1s ease;}
    .hero-badge{display:inline-block;padding:6px 14px;background:#1d3557;color:#fff;border-radius:30px;font-size:13px;margin-bottom:20px;}
    .hero-content h1{font-size:48px;color:#1d3557;line-height:1.2;margin-bottom:20px;}
    .hero-content p{font-size:16px;color:#444;line-height:1.7;max-width:500px;}
    .hero-stats{display:flex;gap:30px;margin:30px 0;}
    .hero-stats div{text-align:left;}
    .hero-stats strong{font-size:22px;color:#1d3557;}
    .hero-stats span{display:block;font-size:13px;color:#666;}
    .hero-btn{display:inline-block;padding:14px 30px;background:#1d3557;color:#fff;text-decoration:none;border-radius:30px;font-weight:600;transition:.3s;}
    .hero-btn:hover{background:#14253d;transform:translateY(-3px);}
    .hero-gallery{display:grid;grid-template-columns:repeat(2,1fr);grid-template-rows:repeat(2,220px);gap:18px;}
    .hero-gallery a{border-radius:18px;overflow:hidden;position:relative;display:block;background:#ddd;animation:fadeZoom 1.2s ease;}
    .hero-gallery img{width:100%;height:100%;object-fit:cover;transition: transform 0.6s ease;}
    .hero-gallery a:hover img{transform: scale(1.12);}
    .categories{background:#fff;padding:80px 0;text-align:center;}
    .categories h2{font-size:32px;color:#1d3557;margin-bottom:40px;}
    .category-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:30px;}
    .category-card{transition:.3s;}
    .category-card:hover{transform:translateY(-8px);}
    .category-card img{width:100%;height:320px;object-fit:cover;border-radius:18px;}
    .category-card p{margin-top:12px;font-weight:600;}
    .popular{padding:80px 0;}
    .popular h2{font-size:32px;color:#1d3557;margin-bottom:30px;}
    .popular-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:30px;}
    .popular-card{background:#fff;border-radius:18px;overflow:hidden;box-shadow:0 8px 20px rgba(0,0,0,.1);cursor:pointer;transition:.3s;}
    .popular-card:hover{transform:translateY(-6px);}
    .popular-card img{width:100%;height:220px;object-fit:cover;}
    .popular-card h3{margin:10px 15px 5px 15px;font-size:18px;color:#1d3557;}
    .popular-card p{padding:0 15px 10px 15px;font-size:14px;color:#555;}
    .stars{color:#ff385c;font-size:14px;margin:5px 15px;}
    .view-more{display:inline-block;margin-top:20px;padding:12px 30px;background:#1d3557;color:#fff;text-decoration:none;border-radius:30px;transition:.3s;}
    .view-more:hover{background:#14253d;}
    @keyframes slideUp{from{opacity:0;transform:translateY(40px);}to{opacity:1;transform:none;}}
    @keyframes fadeZoom{from{opacity:0;transform:scale(.95);}to{opacity:1;transform:none;}}
    @media(max-width:992px){.hero-wrap{grid-template-columns:1fr;}.hero-gallery{grid-template-rows:180px 180px;}}
</style>
<div class="page-space"></div>

<!-- HERO -->
<section class="hero">
    <div class="container hero-wrap">
        <div class="hero-content">
            <span class="hero-badge">🌿 Nature Stay</span>
            <h1>Stay Calm.<br>Stay Comfortable.<br>Stay at OliveNest.</h1>
            <p>Escape the noise and enjoy a peaceful homestay surrounded by nature. Designed for comfort, simplicity, and warmth.</p>
            <div class="hero-stats">
                <div><strong>4.9 ★</strong><span>Guest Rating</span></div>
                <div><strong>120+</strong><span>Happy Guests</span></div>
                <div><strong>24/7</strong><span>Support</span></div>
            </div>
        </div>
        <div class="hero-gallery">
            <div><img src="assets/images/1/h.jpg"></div>
            <div><img src="assets/images/1/i.jpg"></div>
            <div><img src="assets/images/1/e.jpg"></div>
            <div><img src="assets/images/1/l.jpg"></div>
        </div>
    </div>
</section>

<!-- CATEGORIES -->
<section class="categories">
    <div class="container">
        <h2>Why Choose OliveNest</h2>
        <div class="category-grid">
            <div class="category-card"><img src="assets/images/1/r.jpg"><p>Peaceful Location</p></div>
            <div class="category-card"><img src="assets/images/1/o.jpg"><p>Comfort Rooms</p></div>
            <div class="category-card"><img src="assets/images/1/s.jpg"><p>Nature View</p></div>
            <div class="category-card"><img src="assets/images/1/a.jpg"><p>Friendly Stay</p></div>
        </div>
    </div>
</section>

<!-- POPULAR HOMES -->
<section class="popular">
    <div class="container">
        <div class="popular-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h2>Popular Homes</h2>
            <form method="GET">
                <select name="sort" onchange="this.form.submit()" style="padding:8px 14px;border-radius:8px;">
                    <option value="recommended" <?= $sort=='recommended'?'selected':'' ?>>Recommended</option>
                    <option value="price_low" <?= $sort=='price_low'?'selected':'' ?>>Price: Low → High</option>
                    <option value="price_high" <?= $sort=='price_high'?'selected':'' ?>>Price: High → Low</option>
                    <option value="rating" <?= $sort=='rating'?'selected':'' ?>>Top Rated</option>
                    <option value="city" <?= $sort=='city'?'selected':'' ?>>City (A–Z)</option>
                    <option value="newest" <?= $sort=='newest'?'selected':'' ?>>Newest</option>
                </select>
            </form>
        </div>

        <div class="popular-grid">
            <?php foreach($rooms as $room): ?>
            <div class="popular-card">
                <a href="pages/room-details.php?id=<?php echo $room['id']; ?>">
                    <img src="assets/images/room<?php echo $room['id']; ?>.jpg" alt="<?php echo htmlspecialchars($room['title']); ?>">
                </a>
                <h3><?php echo htmlspecialchars($room['title']); ?></h3>
                <p style="font-size:13px;color:#777;">📍 <?php echo htmlspecialchars($room['city']); ?></p>
                <div class="stars">
                    <?php 
                    $full = floor($room['rating']); 
                    $half = ($room['rating'] - $full) >= 0.5 ? 1 : 0; 
                    for($i=0;$i<$full;$i++) echo "★"; 
                    if($half) echo "☆"; 
                    for($i=$full+$half;$i<5;$i++) echo "☆"; 
                    echo " ".$room['rating']; 
                    ?>
                </div>
                <p><?php echo htmlspecialchars($room['description']); ?></p>
                <p class="price">₹<?php echo $room['price']; ?> / night</p>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align:center;">
            <a href="/OliveNest/pages/rooms.php" class="view-more">View More Rooms</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
