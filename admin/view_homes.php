<?php
session_start();
include "../config/db.php"; // Adjust path if needed

// OPTIONAL: Check admin login
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM homes ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin - Uploaded Homes</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f6f8;
    margin: 0;
    padding: 0;
}
.container {
    max-width: 1100px;
    margin: 50px auto;
    padding: 20px;
}
h2 {
    text-align: center;
    color: #2C5F2D;
    margin-bottom: 30px;
}
.home-card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
    box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
}
.home-card h3 { margin-top: 0; }
.home-images img {
    width: 120px;
    height: 80px;
    object-fit: cover;
    margin-right: 10px;
    border-radius: 8px;
}
</style>
</head>
<body>

<div class="container">
    <h2>Uploaded Homes</h2>

    <?php while($row = mysqli_fetch_assoc($result)): ?>
        <div class="home-card">
            <h3><?php echo htmlspecialchars($row['home_title']); ?></h3>
            <p><strong>Owner:</strong> <?php echo htmlspecialchars($row['owner_name']); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
            <p><strong>Price/Night:</strong> ₹<?php echo htmlspecialchars($row['price']); ?></p>
            <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
            <div class="home-images">
                <?php
                $images = explode(",", $row['images']); // split multiple images
                foreach ($images as $img) {
                    if($img != "") {
                        echo '<img src="../uploads/homes/'.$img.'" alt="Home Image">';
                    }
                }
                ?>
            </div>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>
