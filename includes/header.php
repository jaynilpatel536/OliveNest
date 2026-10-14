<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OliveNest Homestay</title>
    <link rel="stylesheet" href="/OliveNest/assets/css/style.css">
    

    <!-- PROFILE DROPDOWN CSS -->
    <style>
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            background: #1d3557;
            color: white;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            color: white;
        }

        .nav-menu ul {
            list-style: none;
            display: flex;
            align-items: center;
        }

        .nav-menu ul li {
            margin-left: 20px;
            position: relative;
        }

        .nav-menu ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
        }

        /* PROFILE DROPDOWN */
        .profile-dropdown {
            display: inline-block;
        }

        .profile-btn {
            background: none;
            border: none;
            cursor: pointer;
        }

        .profile-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 40px;
            background:blueviolet ;
            min-width: 150px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            z-index: 1000;
            padding: 10px 0;
        }

        .dropdown-content a {
            padding: 10px 15px;
            display: block;
            text-decoration: none;
            color: #333;
        }

        .dropdown-content a:hover {
            background: #f3f3f3;
        }

        .profile-dropdown.active .dropdown-content {
            display: block;

        }
        /*footer css*/
        .site-footer{
    background:#f9fafb;           /* soft professional background */
    padding:70px 0 50px;
    border-top:1px solid #e6e6e6;
    font-family: "Poppins", sans-serif;
}

.footer-grid{
    max-width:1300px;
    margin:0 auto;
    padding:0 40px;

    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:50px;
}

/* Brand title */
.footer-grid h3{
    font-size:24px;
    font-weight:600;
    color:#0a2a4a;
}

/* Section titles */
.footer-grid h4{
    font-size:16px;
    font-weight:600;
    color:#0a2a4a;
    margin-bottom:18px;
}

/* Footer links / text */
.footer-grid p{
    font-size:14.5px;
    color:#555;
    margin:8px 0;
    cursor:pointer;
    transition:color 0.3s ease;
}

/* Hover effect */
.footer-grid p:hover{
    color:#6b8e23;   /* olive green accent */
}

    </style>
</head>
<body>

<header class="header">
    <a href="/OliveNest/index.php" class="logo">OliveNest</a>

    <nav class="nav-menu">
        <ul>
            <li><a href="/OliveNest/index.php">Home</a></li>

            <li><a href="/OliveNest/pages/about.php">About us</a></li>
            <li><a href="/OliveNest/pages/rooms.php">Rooms</a></li>
             <li><a href="/OliveNest/pages/booking-assistance.php">Help</a></li>
            <?php if(isset($_SESSION['username'])): ?>
            <li><a href="/OliveNest/pages/upload_home.php">Upload</a></li>

            <?php else: ?>
            <li><a href="/OliveNest/auth/login.php">Upload</a></li>
            <?php endif; ?>

            <li><a href="/OliveNest/pages/contact.php">Contact</a></li>

            <?php if(isset($_SESSION['username'])): ?>
            <!-- PROFILE DROPDOWN -->
            <li>
                <div class="profile-dropdown" id="profileDropdown">
                    <button class="profile-btn">
                        <img src="/OliveNest/assets/images/user.jpg" class="profile-icon" alt="Profile">
                    </button>
                    <div class="dropdown-content">
                        <a href="/OliveNest/auth/profile.php">Profile</a>
                        <a href="/OliveNest/pages/my_booking.php">My Bookings</a>

                        <a href="/OliveNest/auth/logout.php">Logout</a>
                        </div>
                </div>
            </li>
            <?php else: ?>
            <li><a href="/OliveNest/auth/login.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<script>
const profileBtn = document.querySelector('.profile-btn');
const profileDropdown = document.getElementById('profileDropdown');

if(profileBtn){
    profileBtn.addEventListener('click', function(e){
        profileDropdown.classList.toggle('active');
    });

    document.addEventListener('click', function(e){
        if(!profileBtn.contains(e.target) && !profileDropdown.contains(e.target)){
            profileDropdown.classList.remove('active');
        }
    });
}
</script>
