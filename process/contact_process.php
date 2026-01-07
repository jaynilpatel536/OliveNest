<?php
include "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name    = mysqli_real_escape_string($conn, $_POST['name']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $phone   = mysqli_real_escape_string($conn, $_POST['phone']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $query = "INSERT INTO contact_messages (name, email, phone, message) 
              VALUES ('$name', '$email', '$phone', '$message')";
    $query = "INSERT INTO contact_messages (name, email, phone, message) 
              VALUES ('$name', '$email', '$phone', '$message')";


    if (mysqli_query($conn, $query)) {
        echo "<script>
                alert('Message sent successfully!');
                window.location.href='../pages/contact.php';
              </script>";
    } else {
        echo "<script>
                alert('Failed to send message. Try again later.');
                window.location.href='../pages/contact.php';
              </script>";
    }
}
?>
