<?php
$s_username = 'root';
$s_password = '';
$db = 'audio_db';

$connection = new mysqli('localhost', $s_username, $s_password, $db) or die("Unable to connect");

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}


session_start();

$sql_code = "SELECT * FROM users WHERE Username = '" . $_SESSION['user'] . "'";
$result = mysqli_query($connection, $sql_code);
$currentUser = mysqli_fetch_array($result);

if (isset($_POST['image'])) {
    $base64Data = $_POST['image'];
    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Data));

    //CREATE COMMON DIR
    $uploadsDir = 'uploads';
    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0777, true);
    }

    //CREATE USER DIR
    $userUploadsDir = 'uploads/user_' . $currentUser['User_ID'];
    if (!is_dir($userUploadsDir)) {
        mkdir($userUploadsDir, 0777, true);
    }


    // CREATE PIC DIR
    $pics = 'uploads/user_' . $currentUser['User_ID'] . "/pics/";
    if (!is_dir($pics)) {
        mkdir($pics, 0777, true);
    }

    $filename = $pics . uniqid() . ".png";
    file_put_contents($filename, $imageData);
    echo $filename;
} else {
    // Handle the case when the 'image' parameter is not set
    echo "Error: 'image' parameter not set.";
}


$connection->close();