<?php
/* ===========================
   OliveNest Database Connection
   =========================== */

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db   = "olivenest_db";

    $conn = new mysqli($host, $user, $pass, $db);
    $conn->set_charset("utf8mb4");

} catch (mysqli_sql_exception $e) {

    // Log technical error (for developer)
    error_log("OliveNest DB Error: " . $e->getMessage());

    // Friendly message for users
    die("
        <div style='
            max-width:500px;
            margin:120px auto;
            padding:30px;
            background:#ffffff;
            border-radius:14px;
            box-shadow:0 10px 25px rgba(0,0,0,0.15);
            text-align:center;
            font-family:Arial;
        '>
            <h2 style='color:#e63946;'>OliveNest is temporarily unavailable</h2>
            <p>We’re experiencing a technical issue.</p>
            <p>Please try again in a few minutes.</p>
        </div>
    ");
}
?>
