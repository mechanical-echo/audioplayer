<?php
$s_username = 'root';
$s_password = '';
$db = 'audio_db';

$connection = new mysqli('localhost', $s_username, $s_password, $db) or die("Unable to connect");

if (isset($_POST['login'])) {
    session_start();

    $username = mysqli_real_escape_string($connection, $_POST["login-username"]);
    $password = mysqli_real_escape_string($connection, $_POST["login-password"]);
    login($username, $password);
}
function login($username, $password)
{
    $sql_code = "SELECT * FROM users WHERE Username = '$username'";
    $result = mysqli_query($GLOBALS['connection'], $sql_code);
    if (mysqli_num_rows($result) == 1) {
        while ($user = mysqli_fetch_array($result)) {
            if (password_verify($password, $user['Password'])) {
                $_SESSION["user"] = $user['Username'];
                echo "<p class='notification'>Welcome, " . $user['Username'] . "!</p>";
                header("Refresh:1; url=index.php");
            } else {
                echo "Something is wrong! Can't log in!";
            }
        }
    } else {
        echo "Something is wrong! Can't find an account!";
    }
}

if (isset($_POST['signup'])) {
    session_start();

    $username = mysqli_real_escape_string($connection, $_POST["signup-username"]);
    $password = mysqli_real_escape_string($connection, $_POST["signup-password"]);
    $sql_code = "SELECT * FROM users WHERE Username = '$username'";
    $result = mysqli_query($connection, $sql_code);

    if (mysqli_num_rows($result) == 1) {
        echo "User '$username' already exists!";
    } else {
        $sql_code = "INSERT INTO users(Username, Password) VALUES ('$username', '" . password_hash($password, PASSWORD_DEFAULT) . "')";
        mysqli_query($connection, $sql_code);

        createStats($username);

        login($username, $password);
    }
}


function createStats($newUser)
{
    $sql_code = "SELECT * FROM users WHERE Username = '$newUser'";
    $result = mysqli_query($GLOBALS['connection'], $sql_code);

    $user = mysqli_fetch_array($result);

    $sql_code = "INSERT INTO statistics(User_ID, SecondsListened, Genre, SongsListened) VALUES ('" . $user['User_ID'] . "', 0, 'unknown', 0)";

    mysqli_query($GLOBALS['connection'], $sql_code);
}


if (isset($_POST['reset'])) {
    $userID = $_POST['reset'];
    $sql_code = "UPDATE statistics SET SecondsListened = 0, FavouriteArtist = 'unknown', SongsListened = 0 WHERE User_ID = " . $userID;
    mysqli_query($connection, $sql_code);
    echo "<div style='display: flex; justify-content: center; align-items: center; width: 100%; height: 100%; position: fixed; z-index: 10;'>
        <p class='notification big'>Data deleted succesfully!</p>
    </div>";
    header("location:index.php");
}

