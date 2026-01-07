<?php include __DIR__ . '/../includes/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submission Successful</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* GLOBAL LAYOUT */
        html, body { height: 100%; margin: 0; }

        /* PAGE FLEX SYSTEM */
        .page-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: #f4f6f8;
        }

        /* CENTER CONTENT */
        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        /* SUCCESS BOX */
        .success-box {
            max-width: 650px;
            width: 100%;
            background: #ffffff;
            padding: 40px 30px;
            text-align: center;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }
        .success-box h2 { color: #1d3557; margin-bottom: 12px; }
        .success-box p { color: #555; font-size: 16px; margin-bottom: 25px; }

        /* BUTTON */
        .success-box a {
            display: inline-block;
            padding: 12px 22px;
            background: #2C5F2D;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
        }
        .success-box a:hover { opacity: 0.9; }
    </style>
</head>
<body>
<div class="page-container">
    <!-- CENTER CONTENT -->
    <div class="main-content">
        <div class="success-box">
            <h2>Thanks — Submission Received</h2>
            <p>Your home has been submitted and is pending admin approval.</p>
            <a href="/OliveNest/">Return Home</a>
        </div>
    </div>

    <!-- FOOTER WILL ALWAYS SHOW -->
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</div>
</body>
</html>
