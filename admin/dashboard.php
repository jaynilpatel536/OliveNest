<?php 
include "../config/db.php"; 
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
        $homes = mysqli_query($conn, "SELECT * FROM homes ORDER BY id DESC");
        while($row = mysqli_fetch_assoc($homes)){
            echo "<tr>";
            echo "<td>".$row['id']."</td>";
            echo "<td>".$row['owner_name']."</td>";
            echo "<td>".$row['home_title']."</td>";
            echo "<td>".$row['location']."</td>";
            echo "<td>".$row['price']."</td>";
            echo "<td>".$row['description']."</td>";
            echo "<td>".$row['status']."</td>";
            echo "<td>".$row['created_at']."</td>";

            echo "<td>";
            $imgs = explode(",", $row['images']);
            foreach($imgs as $i){
                echo "<img src='../uploads/$i' width='70' style='margin:5px; border-radius:4px;'>";
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
        $book = mysqli_query($conn, "SELECT * FROM booking ORDER BY id DESC");
        while($r = mysqli_fetch_assoc($book)){
            echo "<tr>";
            echo "<td>".$r['id']."</td>";
            echo "<td>".$r['name']."</td>";
            echo "<td>".$r['email']."</td>";
            echo "<td>".$r['phone']."</td>";
            echo "<td>".$r['room_type']."</td>";
            echo "<td>".$r['check_in']."</td>";
            echo "<td>".$r['check_out']."</td>";
            echo "<td>".$r['payment_status']."</td>";
            echo "<td>".$r['amount']."</td>";
            echo "<td>".$r['created_at']."</td>";
            echo "</tr>";
        }
        ?>
    </table>
</div>


<!-- =============== Users Section =============== -->
<div id="users" class="section hidden">
    <h2>Users</h2>

    <?php
    $users = mysqli_query($conn, "SELECT * FROM users ORDER BY user_id DESC");

    if (!$users) {
        echo "<p style='color:red; font-weight:bold;'>SQL ERROR: " . mysqli_error($conn) . "</p>";
    } else {
        if(mysqli_num_rows($users) > 0){
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

            while($u = mysqli_fetch_assoc($users)){
                echo "<tr>";
                echo "<td>".$u['user_id']."</td>";
                echo "<td>".$u['full_name']."</td>";
                echo "<td>".$u['email']."</td>";
                echo "<td>".$u['username']."</td>";
                echo "<td>".$u['password']."</td>";
                echo "<td>".$u['role']."</td>";
                echo "<td>".$u['created_at']."</td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p>No users found.</p>";
        }
    }
    ?>
</div>



<!-- =============== Contact Messages Section =============== -->
<div id="contact" class="section hidden">
    <h2>Contact Messages</h2>

    <?php
    // Query database
    $messages = mysqli_query($conn, "SELECT * FROM contact_messages ORDER BY id DESC");

    // Check if query failed
    if (!$messages) {
        echo "<p style='color:red; font-weight:bold;'>
                SQL ERROR: " . mysqli_error($conn) . "
              </p>";
    } 
    else {
        // Check rows exist
        if(mysqli_num_rows($messages) > 0){
            echo "<table>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Message</th>
                        <th>Date</th>
                    </tr>";

            // Fetch rows
            while($m = mysqli_fetch_assoc($messages)){
                echo "<tr>";
                echo "<td>".$m['id']."</td>";
                echo "<td>".$m['name']."</td>";
                echo "<td>".$m['email']."</td>";
                echo "<td>".$m['phone']."</td>";
                echo "<td>".$m['message']."</td>";
                echo "<td>".$m['created_at']."</td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p>No contact messages found.</p>";
        }
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
        $review = mysqli_query($conn, "SELECT * FROM review ORDER BY id DESC");
        while($rv = mysqli_fetch_assoc($review)){
            echo "<tr>";
            echo "<td>".$rv['id']."</td>";
            echo "<td>".$rv['user_name']."</td>";
            echo "<td>".$rv['room_name']."</td>";
            echo "<td>".$rv['rating']."</td>";
            echo "<td>".$rv['review_text']."</td>";
            echo "</tr>";
        }
        ?>
    </table>
</div>

</div>
</body>
</html>
