<?php
require "database.php";
if (isset($_GET['time'])) {
    $time = (int) $_GET['time'];
    $time++;
    $sql_code = "UPDATE statistics SET SecondsListened = $time WHERE User_ID = " . $_GET['userId'];
    mysqli_query($connection, $sql_code);
    // Send back the updated content
    echo $time;
}

if (isset($_GET['songs'])) {
    $songs = (int) $_GET['songs'];
    $sql_code = "UPDATE statistics SET SongsListened = $songs WHERE User_ID = " . $_GET['userId'];
    mysqli_query($connection, $sql_code);
    echo $songs;
}

if (isset($_GET['artist'])) {

    $sql_code = "SELECT Artist, COUNT(*) AS frequency FROM uploaded_songs WHERE User_ID = " . $_GET['userId'] . " GROUP BY Artist ORDER BY frequency DESC LIMIT 1";
    $result = mysqli_query($connection, $sql_code);
    $song = mysqli_fetch_array($result);
    $artist = $song['Artist'];
    echo $artist;
}
// ON DELETE
if (isset($_GET['fileIndex'])) {
    $fileIndex = $_GET['fileIndex'];

    $sql_code = "SELECT * FROM uploaded_songs WHERE User_ID = " . $_GET['userId'] . " AND Placement = " . $fileIndex;
    $result = mysqli_query($connection, $sql_code);
    $song = mysqli_fetch_array($result);

    $fileName = $song['File_Name'];
    $imagePath = $song['Image'];

    $sql_code = "DELETE FROM uploaded_songs WHERE User_ID = " . $_GET['userId'] . " AND Placement = " . $fileIndex;
    mysqli_query($connection, $sql_code);


    $path = "uploads/user_" . $_GET['userId'] . "/" . $fileName;
    unlink($path);
    unlink($imagePath);

    $sql_code = "SELECT * FROM uploaded_songs WHERE User_ID = " . $_GET['userId'] . " ORDER BY Placement ASC";
    $result = mysqli_query($connection, $sql_code);
    $i = 0;
    foreach ($result as $song):
        $sql_code = "UPDATE uploaded_songs SET Placement = " . $i . " WHERE Song_ID = " . $song['Song_ID'];
        mysqli_query($connection, $sql_code);
        $i++;
    endforeach;

}


if (isset($_GET['path'])) {
    $path = $_GET['path'];
    $placement = $_GET['placement'];
    $userId = $_GET['userId'];
    $sql_code = "UPDATE uploaded_songs SET Image = '" . $path . "' WHERE User_ID = " . $userId . " AND Placement = " . $placement;
    mysqli_query($connection, $sql_code);
}
if (isset($_GET['placement'])) {
    $placement = $_GET['placement'];
    $userId = $_GET['userId'];
    $sql_code = "UPDATE statistics SET LastListened = " . $placement . " WHERE User_ID = " . $userId;
    mysqli_query($connection, $sql_code);
}

if (isset($_GET['newp'])) {
    $oldp = $_GET['oldp'];
    $newp = $_GET['newp'];
    $userId = $_GET['userId'];

    $sql_code = "SELECT * FROM uploaded_songs WHERE User_ID = " . $userId . " AND Placement = " . $oldp;
    $result = mysqli_query($connection, $sql_code);

    $old = mysqli_fetch_array($result);
    $oldid = $old['Song_ID'];

    $sql_code = "SELECT * FROM uploaded_songs WHERE User_ID = " . $userId . " AND Placement = " . $newp;
    $result = mysqli_query($connection, $sql_code);

    $new = mysqli_fetch_array($result);
    $newid = $new['Song_ID'];

    $sql_code = "UPDATE uploaded_songs SET Placement = " . $newp . " WHERE User_ID = " . $userId . " AND Song_ID = " . $oldid;
    mysqli_query($connection, $sql_code);

    $sql_code = "UPDATE uploaded_songs SET Placement = " . $oldp . " WHERE User_ID = " . $userId . " AND Song_ID = " . $newid;
    mysqli_query($connection, $sql_code);
    header("location:index.php");
}

if (isset($_GET['i'])) {
    $i = $_GET['i'];
    $userId = $_GET['userId'];
    $sql_code = "SELECT * FROM uploaded_songs WHERE User_ID = " . $userId . " AND Placement = " . $i;
    $result = mysqli_query($connection, $sql_code);
    $song = mysqli_fetch_array($result);
    echo $song['File_Name'];
    echo "#@#";
    echo $song['Title'];
    echo "#@#";
    echo $song['Artist'];
    echo "#@#";
    echo $song['Album'];
    echo "#@#";
    echo $song['DurationSec'];
    echo "#@#";
    echo $song['Image'];
}


if (isset($_GET['current'])) {
    $current = $_GET['current'];
    $userId = $_GET['userId'];
    $sql_code = "SELECT * FROM uploaded_songs WHERE User_ID = " . $userId;
    $result = mysqli_query($connection, $sql_code);
    $count = 0;
    foreach ($result as $song):
        $count++;
    endforeach;
    echo $count;
}

if (isset($_GET['tr'])) {
    $tr = $_GET['tr'];
    $userId = $_GET['userId'];
    $sql_code = "SELECT * FROM uploaded_songs WHERE User_ID = " . $userId . " ORDER BY Placement ASC";
    $result = mysqli_query($connection, $sql_code);
    foreach ($result as $song):
        $album = $song['Album'];
        if ($album == "undefined") {
            $album = "";
        }
        $tr .= "<tr><td>{$song['Title']}</td><td>{$song['Artist']}</td><td>{$album}</td><td>{$song['DurationSec']}</td><td>
        <button onclick='onUp(this)'><i class='fa-solid fa-angle-up'></i></button>
        <button onclick='onDown(this)'><i class='fa-solid fa-angle-down'></i></button>
        <button onclick='onDelete(this)'><i class='fa-solid fa-trash'></i></button>
        </td></tr>";
    endforeach;
    echo $tr;
}