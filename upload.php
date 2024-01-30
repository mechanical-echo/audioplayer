<?php
$s_username = 'root';
$s_password = '';
$db = 'audio_db';

$connection = new mysqli('localhost', $s_username, $s_password, $db) or die("Unable to connect");

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}



session_start();

//GET SONG ATTRIBUTES
$fileTmpPath = $_FILES['song']['tmp_name'];
$fileName = $_FILES['song']['name'];

$title = $_POST['title'];
$artist = $_POST['artist'];
$album = $_POST['album'];
$duration = (int) $_POST['duration'];
$placement = (int) $_POST['placement'];

$sql_code = "SELECT * FROM users WHERE Username = '" . $_SESSION['user'] . "'";
$result = mysqli_query($connection, $sql_code);
$currentUser = mysqli_fetch_array($result);



//CREATE COMMON DIR
$uploadsDir = 'uploads';
if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0777, true);
}


// CREATE USER DIR
$userUploadsDir = 'uploads/user_' . $currentUser['User_ID'];
if (!is_dir($userUploadsDir)) {
    mkdir($userUploadsDir, 0777, true);
}


// MOVE FILE TO DIRECTORY
$uploadPath = $userUploadsDir . '/' . $fileName;
move_uploaded_file($fileTmpPath, $uploadPath);


// FIND DUPLICATES
$sql_code = "SELECT * FROM uploaded_songs WHERE File_Name = '" . $fileName . "' AND User_ID = " . $currentUser['User_ID'];
$result = mysqli_query($connection, $sql_code);



//EXECUTE
if (mysqli_num_rows($result) == 1) {
    echo "This file already has been saved for this user in database.\n";
} else {
    $sql_code = "INSERT INTO uploaded_songs(User_ID, File_Name, Title, Artist, Album, DurationSec, Placement) VALUES (" . $currentUser['User_ID'] . ", '" . $fileName . "', '" . $title . "', '" . $artist . "', '" . $album . "', $duration, $placement)";
    // echo "\n\n$sql_code\n\n";
    mysqli_query($connection, $sql_code);
}

echo "\nSuccess!";

$connection->close();

