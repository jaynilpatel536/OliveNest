<?php
require_once __DIR__ . '/../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');

    $newId = getNextId($db, 'contact_messages');

    try {
        $result = $db->contact_messages->insertOne([
            'id'         => $newId,
            'name'       => $name,
            'email'      => $email,
            'phone'      => $phone,
            'message'    => $message,
            'created_at' => new MongoDB\BSON\UTCDateTime()
        ]);

        if ($result->getInsertedCount() > 0) {
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
    } catch (Exception $e) {
        echo "<script>
                alert('Failed to send message. Try again later.');
                window.location.href='../pages/contact.php';
              </script>";
    }
}
?>
