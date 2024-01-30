<?php
session_start();
// require "database.php";
require "update_content.php";
if (!isset($_SESSION["user"])) {
  header("location:login.php");
  exit();
}
$sql_code = "SELECT * FROM users WHERE Username = '" . $_SESSION['user'] . "'";
$result = mysqli_query($connection, $sql_code);
$currentUser = mysqli_fetch_array($result);

$sql_code = "SELECT * FROM statistics WHERE User_ID = " . $currentUser['User_ID'];
$result = mysqli_query($connection, $sql_code);
$stats = mysqli_fetch_array($result);
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <title>Music player</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="description" content="" />
  <meta name="viewport" content="width=device-width" />
  <script src="./dist/id3-minimized.js" type="text/javascript"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="main.css" />


  <link rel="shortcut icon" href="./assets/favicon.png" type="image/x-icon" />


  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>

<body>
  <section id="notification-section">

  </section>

  <audio style="display: none;" id="audio" ontimeupdate="updateTime()" controls></audio>
  <aside class="aside-hide" id="menu">
    <div style="position: relative; width: 100%; height: 100%">
      <div class="container">
        <button onclick="openAside();" id="show-aside">
          <i class="fa-solid fa-angle-left"></i>
        </button>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i>Log out</a>
        <div id="profile-icon"><i class="fa-solid fa-person"></i></div>
        <h3>
          <?php echo strtoupper($_SESSION["user"]); ?>
        </h3>
      </div>
      <form method="post">

        <button name="reset" type="submit" value="<?php echo $currentUser['User_ID']; ?>" id="resetStats"
          onclick="return confirm('Do you really wish to reset your statistics? This action cannot be undone!');">Reset
          statistics</button>
      </form>
    </div>
  </aside>
  <!--  -->
  <!--  -->
  <!--  -->
  <main>
    <section id="player-section">



      <!-- metadata results -->
      <div class="card">
        <img id="picture" src="./assets/placeholder.png" alt="picture extracted from ID3" />
        <div>
          <h1 id="title">title</h1>
          <h2 id="artist">artist</h2>
          <h4 style="display: none"><span id="album">album</span></h4>
        </div>
      </div>

      <!-- timeline -->
      <div class="row">
        <p id="currentTime">00:00</p>
        <input type="range" id="slider" onmousedown=" audioElement.pause();" onchange="onSliderChange()"
          onmouseup=" audioElement.play();" value="0" />
        <p id="duration">00:00</p>
      </div>

      <!-- constrols -->

      <div class="row" id="controls">
        <button id="prev" onclick="prevSong()">
          <i class="fa-solid fa-backward-step"></i>
        </button>
        <button id="play" onclick="onPlayPause()">
          <i class="fa-regular fa-circle-play"></i>
          <!-- <i class="fa-regular fa-circle-pause"></i> -->
        </button>
        <button id="next" onclick="nextSong()">
          <i class="fa-solid fa-forward-step"></i>
        </button>
      </div>
    </section>
    <section id="volume-section">
      <i class="fa-solid fa-volume-low"></i>
      <input onchange="onVolumeChange()" type="range" id="volume-slider" max="1" min="0" step="0.01" />
      <p id="volume-number">50%</p>
    </section>
    <section id="playlist-section">
      <h1>playlist</h1>
      <div id="wrapper">
        <!-- file -->
        <input type="file" hidden id="file" onchange="loadFile(this)" accept="audio/mp3" />
        <label for="file">Choose file</label>
        <div style="overflow: scroll; height:85%;">
          <table id="playlist">
            <tr>
              <th onclick="toggleSort(this)">Title</th>
              <th onclick="toggleSort(this)">Artist</th>
              <th onclick="toggleSort(this)">Album</th>
              <th onclick="toggleSort(this)">Duration</th>
              <th>---</th>
            </tr>

          </table>
        </div>
      </div>
    </section>
  </main>
  <section id="stats-section">
    <h1>statistics</h1>
    <div class="row">
      <div class="stats-category">
        <h1>
          You have been listening for
          <br />
          <span id="record-time">
          </span>
          <br />
          minutes
          <br />
          Or <span id="record-time-sec"></span> seconds
        </h1>
      </div>
      <div class="stats-category">
        <h1>
          Your favourite artist is
          <br />
          <span id="record-artist"></span>
          <br />
          They appear in your playlist the most!
        </h1>
      </div>
      <div class="stats-category">
        <h1>
          You have listened to
          <br />
          <span id="record-song-count"></span>
          <br />
          songs
        </h1>
      </div>
    </div>
  </section>
  <!-- ------------------------- -->
  <script src="script.js"></script>
  <?php
  require 'script.php';
  ?>
</body>

</html>