let savedtags;
let currentSong;
// let d;
const durationDisplay = document.getElementById("duration");
const slider = document.getElementById("slider");
const playButton = document.getElementById("play");
audioElement = document.getElementById("audio");
let tis = false;
let ars = false;
let als = false;
let dus = false;
let lastPressed = "";

var fullPlaylist = [];

audioElement.addEventListener("loadedmetadata", function () {
  d = audioElement.duration;
});

//------------------------------------------ON FILE UPLOAD
// on file upload
function loadFile(input) {
  var file = input.files[0],
    url = file.name;
  ID3.loadTags(
    url,
    function () {
      getTags(url, input);
    },
    {
      tags: ["title", "artist", "album", "picture"],
      dataReader: ID3.FileAPIReader(file),
    }
  );
  // file = input.files[0];
}
//get duration data when audio's metadata is loaded
function getDuration(src, cb) {
  var audio = new Audio();
  $(audio).on("loadedmetadata", function () {
    cb(audio.duration);
  });
  audio.src = src;
}

//------------------------------------------TAG MANIPULATION

function dataURItoBlob(dataURI) {
  var byteString = atob(dataURI.split(",")[1]);
  var ab = new ArrayBuffer(byteString.length);
  var ia = new Uint8Array(ab);
  for (var i = 0; i < byteString.length; i++) {
    ia[i] = byteString.charCodeAt(i);
  }
  return new Blob([ab], {
    type: "image/" + dataURI.split(";")[0].split(":")[1],
  });
}
//------------------------------------------ADDING A SONG TO PLAYLIST
//create button elements for song order manipulation in table
function getButtons() {
  const songButtons = document.createElement("td");
  const buttonUp = document.createElement("button");
  const buttonDown = document.createElement("button");
  const buttonDelete = document.createElement("button");
  const iup = document.createElement("i");
  const idown = document.createElement("i");
  const idelete = document.createElement("i");

  iup.classList.add("fa-solid");
  iup.classList.add("fa-angle-up");
  idown.classList.add("fa-solid");
  idown.classList.add("fa-angle-down");
  idelete.classList.add("fa-solid");
  idelete.classList.add("fa-trash");

  buttonUp.appendChild(iup);
  buttonDown.appendChild(idown);
  buttonDelete.appendChild(idelete);

  buttonUp.addEventListener("click", onUp);
  buttonDown.addEventListener("click", onDown);
  buttonDelete.addEventListener("click", onDelete);

  songButtons.appendChild(buttonUp);
  songButtons.appendChild(buttonDown);
  songButtons.appendChild(buttonDelete);
  return songButtons;
}
//convert seconds to 00:00 format
function formatDuration(d) {
  min = Math.floor(d / 60);
  s = Math.floor(d % 60);
  return (min < 10 ? "0" : "") + min + ":" + (s < 10 ? "0" : "") + s;
}

//create <tr> element with file's tag info
function getNewSong(tags, d) {
  const newSong = document.createElement("tr");
  const newSongTitle = document.createElement("td");
  const newSongArtist = document.createElement("td");
  const newSongAlbum = document.createElement("td");
  const newSongDuration = document.createElement("td");

  newSongTitle.textContent = tags.title || "";
  newSongArtist.textContent = tags.artist || "";
  newSongAlbum.textContent = tags.album || "";
  newSongDuration.textContent = d;
  newSong.appendChild(newSongTitle);
  newSong.appendChild(newSongArtist);
  newSong.appendChild(newSongAlbum);
  newSong.appendChild(newSongDuration);
  newSong.appendChild(getButtons());
  return newSong;
}

//check two songs' tags
function areObjectsEqual(obj1, obj2) {
  return obj1.title === obj2.title && obj1.artist === obj2.artist;
}
// //add song to table and to array
// function addToPlaylist(tags, d, url) {
//   const playlist = document.getElementById("playlist");

//   newSong = getNewSong(tags, formatDuration(d));

//   const input = document.getElementById("file");

//   currentSong = {
//     file: input.files[0],
//     title: tags.title,
//     artist: tags.artist,
//     album: tags.album,
//     duration: d,
//     tags: tags,
//   };
//   console.log("currentSong");
//   console.log(currentSong);
//   if (!fullPlaylist.some((song) => areObjectsEqual(song, currentSong))) {
//     fullPlaylist.push(currentSong);
//     playlist.appendChild(newSong);
//     displayTags(tags);
//     audioElement.src = url;
//     playButton.children[0].classList.add("fa-circle-play");
//     playButton.children[0].classList.remove("fa-circle-pause");
//   }
//   console.log(fullPlaylist);
//   createNotification("Song '" + currentSong.title + "' was added!");
//   // getFavouriteArtist(currentSong);
// }
//get index by table record
function check(a) {
  obj = {
    title: a.parentNode.parentNode.children[0].textContent,
    artist: a.parentNode.parentNode.children[1].textContent,
  };
  let index;
  for (i = 0; i < fullPlaylist.length; i++) {
    const current = fullPlaylist[i];
    if (areObjectsEqual(obj, current)) {
      index = i;
    }
  }
  return index;
}

//------------------------------------------BUTTON CLICK HANDLERS

let isPlaceholder = true;
function setPlaceholders() {
  document.getElementById("title").textContent = "title";
  document.getElementById("artist").textContent = "artist";
  document.getElementById("album").textContent = "album";
  document
    .getElementById("picture")
    .setAttribute("src", "./assets/placeholder.png");
  isPlaceholder = true;
  audioElement.src = "";
  audioElement.pause();
  slider.max = 0;
  playButton.children[0].classList.remove("fa-circle-pause");
  playButton.children[0].classList.add("fa-circle-play");
  durationDisplay.textContent = "00:00";
}

//(controls button && eventhandler) next song in the queue
// function nextSong() {
// var index = fullPlaylist.indexOf(currentSong);
// if (index + 1 == fullPlaylist.length) {
//   index = 0;
// } else {
//   index++;
// }
// playByIndex(index);
// }

//(controls button) previous song in the queue
// function prevSong() {
//   var index = fullPlaylist.indexOf(currentSong);
//   if (index == 0) {
//     index = fullPlaylist.length - 1;
//   } else {
//     index--;
//   }
//   playByIndex(index);
// }

// play a song by index
function playByIndex(index) {
  if (fullPlaylist.length > 0) {
    // const audioURL = URL.createObjectURL(fullPlaylist[index].file);
    // audioElement.src = audioURL;
    audioElement.src = fullPlaylist[index].path;
    displayTags(fullPlaylist[index].tags);
    currentSong = fullPlaylist[index];
    durationDisplay.textContent = formatDuration(fullPlaylist[index].duration);
    slider.max = fullPlaylist[index].duration;
    audioElement.volume = volumeSlider.value;
    audioElement.play();
    playButton.children[0].classList.remove("fa-circle-play");
    playButton.children[0].classList.add("fa-circle-pause");
  } else {
    alert("playlist is empty");
  }
}

//(controls button) play button event
function onPlayPause() {
  if (!isPlaceholder) {
    if (audioElement.paused) {
      audioElement.play();
      playButton.children[0].classList.remove("fa-circle-play");
      playButton.children[0].classList.add("fa-circle-pause");
    } else {
      audioElement.pause();
      playButton.children[0].classList.remove("fa-circle-pause");
      playButton.children[0].classList.add("fa-circle-play");
    }
  }
}

//current time for <p> and slider
function updateTime() {
  const currentTimeDisplay = document.getElementById("currentTime");

  slider.value = audioElement.currentTime;

  var currentMinutes = Math.floor(audioElement.currentTime / 60);
  var currentSeconds = Math.floor(audioElement.currentTime % 60);

  currentMinutes = (currentMinutes < 10 ? "0" : "") + currentMinutes;
  var formattedTime =
    currentMinutes + ":" + (currentSeconds < 10 ? "0" : "") + currentSeconds;

  currentTimeDisplay.textContent = formattedTime;
}

//slider seek functionality
function onSliderChange() {
  audioElement.currentTime = slider.value;
  playButton.children[0].classList.remove("fa-circle-play");
  playButton.children[0].classList.add("fa-circle-pause");
}

//(table headings) toggle between asc and desc sorting for each tag
function toggleSort(element) {
  console.log(element.textContent);
  switch (element.textContent.trim()) {
    case "Title":
      console.log("title");
      if (tis) {
        tis = false;
      } else {
        tis = true;
      }
      console.log(tis);
      lastPressed = "title";
      sortPlaylist(lastPressed, tis);
      break;
    case "Artist":
      if (ars) {
        ars = false;
      } else {
        ars = true;
      }
      lastPressed = "artist";
      sortPlaylist(lastPressed, ars);
      break;
    case "Album":
      if (als) {
        als = false;
      } else {
        als = true;
      }
      lastPressed = "album";
      sortPlaylist(lastPressed, als);
      break;
    case "Duration":
      if (dus) {
        dus = false;
      } else {
        dus = true;
      }
      lastPressed = "duration";
      sortPlaylist(lastPressed, dus);
      break;
  }
}

//sorts array elements and table rows
function sortPlaylist(key, asc) {
  console.log("time to sort!");
  fullPlaylist.sort((a, b) => {
    if (typeof a[key] === "string") {
      a[key] = a[key].toLowerCase();
      b[key] = b[key].toLowerCase();
    }
    if (asc) {
      if (a[key] < b[key]) return -1;
      if (a[key] > b[key]) return 1;
      return 0;
    } else {
      if (a[key] > b[key]) return -1;
      if (a[key] < b[key]) return 1;
      return 0;
    }
  });
  displayPlaylist();
  console.log("new");
  console.log(fullPlaylist);
}

//updates table after sorting
function displayPlaylist() {
  const playlist = document.getElementById("playlist");
  playlist.innerHTML = `<tr>
  <th onclick="toggleSort(this)">Title</th>
  <th onclick="toggleSort(this)">Artist</th>
  <th onclick="toggleSort(this)">Album</th>
  <th onclick="toggleSort(this)">Duration</th>
  <th>---</th>
</tr>`;
  for (i = 0; i < fullPlaylist.length; i++) {
    newSong = getNewSong(
      fullPlaylist[i].tags,
      formatDuration(fullPlaylist[i].duration)
    );
    playlist.appendChild(newSong);
  }
}

function openAside() {
  const aside = document.getElementById("menu");
  const asidebtn = document.getElementById("show-aside");
  if (aside.classList.contains("aside-hide")) {
    aside.classList.remove("aside-hide");
    asidebtn.style.transform = "rotate(180deg)";
    asidebtn.style.left = "-4rem";
  } else {
    aside.classList.add("aside-hide");
    asidebtn.style.transform = "rotate(0)";
    asidebtn.style.left = "-6rem";
  }
}

// VOLUME FUNCTIONALITY
const volumeSlider = document.getElementById("volume-slider");
volumeSlider.value = 0.5;
const volumeNumber = document.getElementById("volume-number");
var retrievedValue = localStorage.getItem("volume");
if (retrievedValue !== null) {
  volumeSlider.value = retrievedValue;
  onVolumeChange();
}
function onVolumeChange() {
  audioElement.volume = volumeSlider.value;
  localStorage.setItem("volume", volumeSlider.value);
  var percent = Math.floor(volumeSlider.value * 100);
  volumeNumber.textContent = percent + "%";
}

// NOTIFICATIONS
function createNotification(message) {
  /**<div class="notification">
      test notification
      <button onclick="this.parentElement.remove()" class="close-notification"><i
          class="fa-solid fa-xmark"></i></button>
    </div> */
  const notificationSection = document.getElementById("notification-section");

  const notifDiv = document.createElement("div");
  const notifBtn = document.createElement("button");
  const notifI = document.createElement("i");

  notifDiv.classList.add("notification");
  notifBtn.classList.add("close-notification");
  notifI.classList.add("fa-solid");
  notifI.classList.add("fa-xmark");

  notifBtn.appendChild(notifI);
  notifBtn.addEventListener("click", function () {
    notifBtn.parentElement.remove();
  });
  notifDiv.innerText = message;
  notifDiv.appendChild(notifBtn);

  if (notificationSection.childElementCount == 3) {
    notificationSection.removeChild(notificationSection.children[0]);
  }
  console.log(notifDiv);
  notificationSection.appendChild(notifDiv);

  const removeElements = () => {
    notifDiv.classList.add("notification-disappear");
    setTimeout(() => {
      notifDiv.parentNode.removeChild(notifDiv);
    }, 100);
  };

  setTimeout(removeElements, 10000);
}

// STATS ----------------------------
