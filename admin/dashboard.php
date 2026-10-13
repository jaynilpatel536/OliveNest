<?php 
include __DIR__ . '/../config/db.php'; 
session_start();

// if admin not logged in redirect
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>

    <style>
        body {
            margin: 0;
            font-family: Arial;
            background: #f4f4f4;
        }

        /* Sidebar */
        .sidebar {
            width: 230px;
            height: 100vh;
            background: #222;
            position: fixed;
            top: 0;
            left: 0;
            color: white;
            padding-top: 20px;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #0ef;
        }

        .sidebar a {
            display: block;
            padding: 15px;
            color: white;
            text-decoration: none;
            border-bottom: 1px solid #333;
        }

        .sidebar a:hover {
            background: #0ef;
            color: black;
            font-weight: bold;
        }

        /* Main Content */
        .main {
            margin-left: 240px;
            padding: 20px;
        }

        h2 {
            background: #333;
            color: #fff;
            padding: 10px;
            border-radius: 4px;
        }

        table {
            width: 100%;
            margin-top: 15px;
            margin-bottom: 30px;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        th {
            background: #555;
            color: white;
        }

        .hidden {
            display: none;
        }
    </style>

    <script>
        function showSection(id) {
            const sections = document.querySelectorAll(".section");
            sections.forEach(sec => sec.style.display = "none");
            document.getElementById(id).style.display = "block";
        }
    </script>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="#" onclick="showSection('homes')">Homes</a>
    <a href="#" onclick="showSection('bookings')">Bookings</a>
    <a href="#" onclick="showSection('users')">Users</a>
    <a href="#" onclick="showSection('contact')">Contact Messages</a>
    <a href="#" onclick="showSection('reviews')">Reviews</a>
    <a href="logout.php" style="background:#f00; color:white;">Logout</a>
</div>

<div class="main">

<!-- =============== Homes Section =============== -->
<div id="homes" class="section">
    <h2>Uploaded Homes</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Owner</th>
            <th>Title</th>
            <th>Location</th>
            <th>Price</th>
            <th>Description</th>
            <th>Status</th>
            <th>Date</th>
            <th>Images</th>
        </tr>

        <?php
        $homesCursor = $db->homes->find([], ['sort' => ['id' => -1]]);
        foreach($homesCursor as $doc){
            $row = docToArray($doc);
            echo "<tr>";
            echo "<td>" . htmlspecialchars((string)($row['id'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars((string)($row['owner_name'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars((string)($row['home_title'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars((string)($row['location'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars((string)($row['price'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars((string)($row['description'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars((string)($row['status'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars((string)($row['created_at'] ?? '')) . "</td>";

            echo "<td>";
            $imagesVal = $row['images'] ?? '';
            $imgs = is_array($imagesVal) ? $imagesVal : explode(",", (string)$imagesVal);
            foreach($imgs as $i){
                $i = trim($i);
                if ($i !== '') {
                    echo "<img src='../uploads/" . htmlspecialchars($i) . "' width='70' style='margin:5px; border-radius:4px;'>";
                }
            }
            echo "</td>";

            echo "</tr>";
        }
        ?>
    </table>
</div>


<!-- =============== Bookings Section =============== -->
<div id="bookings" class="section hidden">
    <h2>Bookings</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Room</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Payment</th>
            <th>Amount</th>
            <th>Date</th>
        </tr>

        <?php
        $bookCursor = $db->bookings->find([], ['sort' => ['id' => -1]]);
        foreach($bookCursor as $doc){
            $r = docToArray($doc);
            echo "<tr>";
            echo "<td>" . htmlspecialchars((string)($r['id'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars((string)($r['guest_name'] ?? $r['name'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars((string)($r['guest_email'] ?? $r['email'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars((string)($r['guest_phone'] ?? $r['phone'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars((string)($r['room_type'] ?? $r['room_id'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars((string)($r['check_in'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars((string)($r['check_out'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars((string)($r['payment_status'] ?? $r['booking_status'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars((string)($r['total_amount'] ?? $r['amount'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars((string)($r['created_at'] ?? '')) . "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</div>


<!-- =============== Users Section =============== -->
<div id="users" class="section hidden">
    <h2>Users</h2>

    <?php
    $userDocs = iterator_to_array($db->users->find([], ['sort' => ['id' => -1]]));

    if(empty($userDocs)){
        echo "<p>No users found.</p>";
    } else {
        echo "<table>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Role</th>
                    <th>Date</th>
                </tr>";

        foreach($userDocs as $doc){
            $u = docToArray($doc);
            $userId = $u['id'] ?? $u['user_id'] ?? '';
            echo "<tr>";
            echo "<td>" . htmlspecialchars((string)$userId) . "</td>";
            echo "<td>" . htmlspecialchars((string)($u['full_name'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars((string)($u['email'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars((string)($u['username'] ?? '')) . "</td>";
            echo "<td>****</td>";
            echo "<td>" . htmlspecialchars((string)($u['role'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars((string)($u['created_at'] ?? '')) . "</td>";
            echo "</tr>";
        }

        echo "</table>";
    }
    ?>
</div>



<!-- =============== Contact Messages Section =============== -->
<div id="contact" class="section hidden">
    <h2>Contact Messages</h2>

    <?php
    $messageDocs = iterator_to_array($db->contact_messages->find([], ['sort' => ['id' => -1]]));

    if(empty($messageDocs)){
        echo "<p>No contact messages found.</p>";
    } else {
        echo "<table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Message</th>
                    <th>Date</th>
                </tr>";

        foreach($messageDocs as $doc){
            $m = docToArray($doc);
            echo "<tr>";
            echo "<td>" . htmlspecialchars((string)($m['id'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars((string)($m['name'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars((string)($m['email'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars((string)($m['phone'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars((string)($m['message'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars((string)($m['created_at'] ?? '')) . "</td>";
            echo "</tr>";
        }

        echo "</table>";
    }
    ?>
</div>



<!-- =============== Reviews Section =============== -->
<div id="reviews" class="section hidden">
    <h2>Reviews</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Room</th>
            <th>Rating</th>
            <th>Review</th>
        </tr>

        <?php
        $reviewCursor = $db->reviews->find([], ['sort' => ['id' => -1]]);
        foreach($reviewCursor as $doc){
            $rv = docToArray($doc);
            echo "<tr>";
            echo "<td>" . htmlspecialchars((string)($rv['id'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars((string)($rv['user_name'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars((string)($rv['room_name'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars((string)($rv['rating'] ?? '')) . "</td>";
            echo "<td>" . htmlspecialchars((string)($rv['review_text'] ?? '')) . "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</div>

</div>
</body>
</html>
