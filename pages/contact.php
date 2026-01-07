<?php include "../includes/header.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us - OliveNest</title>

    <!-- INTERNAL CSS -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background: #f5f5f5;
        }

        /* Background Image */
        .contact-section {
            min-height: 100vh;
            background-image: url("/OliveNest/assets/images/70432.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;

            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        /* Contact Box */
        .contact-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
            text-align: center;
        }

        .contact-container h2 {
            font-size: 28px;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .sub-text {
            font-size: 14px;
            color: #555;
            margin-bottom: 25px;
        }

        /* Form */
        .form-group {
            margin-bottom: 15px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 14px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
            outline: none;
        }

        .form-group textarea {
            resize: none;
            height: 120px;
        }

        /* Button */
        .send-btn {
            width: 100%;
            padding: 12px;
            background-color: #2ecc71;
            color: #fff;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .send-btn:hover {
            background-color: #27ae60;
        }

        /* Mobile Responsive */
        @media (max-width: 600px) {
            .contact-container {
                padding: 30px 20px;
            }
        }
        .footer { 
        background-color: #1d3557; 
        color: #f5f5f5;
        padding: 60px 20px 20px;
        font-family: 'Georgia', serif; }
        /* Headings */
.footer h3,
.footer h4 {
    color: #ffffff;
}

/* List text */
.footer li,
.footer p {
    color: #f1faee;   /* light cream – very visible */
}

/* Optional hover */
.footer li:hover,
.footer p:hover {
    color: #a8dadc;
}
    </style>
</head>

<body>

<section class="contact-section">
    <div class="contact-container">
        <h2>Get In Touch</h2>
        <p class="sub-text">Send us your message — we reply within 24 hours.</p>

        <?php if(isset($_SESSION['username'])): ?>
<form action="../process/contact_process.php" method="POST">
<?php else: ?>
<form action="/OliveNest/login.php" method="GET">
<?php endif; ?>


            <div class="form-group">
                <input type="text" name="name" placeholder="Full Name" required>
            </div>

            <div class="form-group">
                <input type="email" name="email" placeholder="Email Address" required>
            </div>

            <div class="form-group">
                <input type="text" name="phone" placeholder="Phone Number" required>
            </div>

            <div class="form-group">
                <textarea name="message" placeholder="Your Message" required></textarea>
            </div>

            <button type="submit" class="send-btn">Send Message</button>

        </form>
    </div>
</section>

</body>
</html>

<?php include "../includes/footer.php"; ?>