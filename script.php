<script>
    let playlistSize = 0;
    let lastListened = <?php echo $stats['LastListened']; ?>;
    getCount();
    function display(i) {
        lastListened = i;
        updateLastListened(lastListened);
        var data;
        var xhr = new XMLHttpRequest();
        xhr.onload = () => {
            data = xhr.responseText.split("#@#");
            console.log(data);
            //0 - audiopath
            //1 - title
            //2 - artist
            //3 - album
            //4 - duration
            //5 - imagepath
            document.getElementById("title").textContent = data[1];
            document.getElementById("artist").textContent = data[2];
            document.getElementById("album").textContent = (data[3] == "undefined" ? "" : data[3]);
            audioElement.src = "./uploads/user_<?php echo $currentUser['User_ID']; ?>/" + data[0];
            document.getElementById('picture').src = data[5];
            isPlaceholder = false;
            document.getElementById('duration').textContent = formatDuration(data[4]);
            document.getElementById('slider').max = data[4];
            audioElement.play();

        }
        xhr.open("GET", "update_content.php?i=" + i + "&userId=" + <?php echo $currentUser['User_ID']; ?>, true);
        xhr.send();
    }
    function getCount() {
        const xhr = new XMLHttpRequest();
        xhr.onload = () => {
            playlistSize = parseInt(xhr.responseText);
            if (playlistSize != 0) {
                display(lastListened);
            }

        }
        xhr.open("GET", "update_content.php?current=" + lastListened + "&userId=" + <?php echo $currentUser['User_ID']; ?>, true);
        xhr.send();
    }
    function nextSong() {
        var count, index;
        const xhr = new XMLHttpRequest();
        xhr.onload = () => {
            count = parseInt(xhr.responseText);
            console.log("count", count);
            console.log("lastlistened", lastListened);
            if (lastListened == count) {
                lastListened--;
            }
            if (lastListened == (count - 1)) {
                index = 0;
            } else {
                index = lastListened + 1;
            }
            console.log("index", index);
            display(index);
            lastListened = index;
            console.log('lastListened', lastListened);
            updateLastListened(lastListened);
            playButton.children[0].classList.remove("fa-circle-play");
            playButton.children[0].classList.add("fa-circle-pause");
        }
        xhr.open("GET", "update_content.php?current=" + lastListened + "&userId=" + <?php echo $currentUser['User_ID']; ?>, true);
        xhr.send();
    }
    function prevSong() {
        var count, index;
        const xhr = new XMLHttpRequest();
        xhr.onload = () => {
            count = parseInt(xhr.responseText);
            // console.log("count", count);
            if (lastListened == 0) {
                index = count - 1;
            } else {
                index = lastListened - 1;
            }
            // console.log("index", index);
            display(index);
            lastListened = index;
            updateLastListened(lastListened);
            playButton.children[0].classList.remove("fa-circle-play");
            playButton.children[0].classList.add("fa-circle-pause");
        }
        xhr.open("GET", "update_content.php?current=" + lastListened + "&userId=" + <?php echo $currentUser['User_ID']; ?>, true);
        xhr.send();
    }
    makeTable();
    audioElement = document.getElementById("audio");
    const timerElement = document.getElementById("record-time");
    const timerElementSec = document.getElementById("record-time-sec");

    let timeBefore = <?php echo $stats['SecondsListened']; ?>;
    let time = <?php echo $stats['SecondsListened']; ?>;

    timerElementSec.textContent = time;
    timerElement.textContent = parseInt(time / 60);

    audioElement.addEventListener("timeupdate", () => {
        const currentTime = Math.floor(audioElement.currentTime);
        if (currentTime != timeBefore) {
            time++;
        }
        timeBefore = currentTime;
        // timerElementSec.textContent = time;
        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Update the content of the div with the response from the PHP script
                timerElementSec.textContent = xhr.responseText;
                timerElement.textContent = parseInt(parseInt(xhr.responseText) / 60);
            }
        };
        xhr.open("GET", "update_content.php?time=" + time + "&userId=" + <?php echo $currentUser['User_ID']; ?>, true);
        xhr.send();
    });


    audioElement.addEventListener("ended", onSongEnd);
    let songCount = <?php echo $stats['SongsListened'] ?>;
    const songCounterElement = document.getElementById("record-song-count");
    songCounterElement.textContent = songCount;
    function onSongEnd() {
        // console.log("song '", currentSong["title"], "' ended");
        songCount++;
        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Update the content of the div with the response from the PHP script
                songCounterElement.textContent = xhr.responseText;
            }
        };
        xhr.open("GET", "update_content.php?songs=" + songCount + "&userId=" + <?php echo $currentUser['User_ID']; ?>, true);
        xhr.send();
        nextSong();
    }

    const artistDisplay = document.getElementById("record-artist");
    getFavouriteArtist();

    function getFavouriteArtist() {
        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var newa = xhr.responseText;
                artistDisplay.textContent = newa;
                console.log(newa);
            }
        };
        xhr.open("GET", "update_content.php?artist=" + 1 + "&userId=" + <?php echo $currentUser['User_ID']; ?>, true);
        xhr.send();
    }


    //(table button) remove song
    function onDelete(button) {
        // const item = button.parentNode.parentNode;
        // item.remove();
        var table = button.parentNode.parentNode.parentNode;
        var row = button.parentNode.parentNode;

        var index = Array.prototype.indexOf.call(table.children, row);
        index--;

        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                makeTable();
                if (index == lastListened) {
                    nextSong();
                }
            }
        };
        xhr.open("GET", "update_content.php?userId=" + <?php echo $currentUser['User_ID']; ?> + "&fileIndex=" + index, true);

        xhr.send();
    }
    //(table button) move song higher
    function onUp(button) {
        <?php
        $sql_code = "SELECT * FROM uploaded_songs WHERE User_ID = " . $currentUser['User_ID'] . " ORDER BY Placement ASC";
        $result = mysqli_query($connection, $sql_code);
        $fp = 0;
        foreach ($result as $r):
            $fp++;
        endforeach;
        ?>
        var table = button.parentNode.parentNode.parentNode;
        var row = button.parentNode.parentNode;

        var index = Array.prototype.indexOf.call(table.children, row);
        index--;
        var newindex = (index == 0 ? (<?php echo $fp - 1; ?>) : (index - 1));
        console.log("index", index);
        console.log("newindex", newindex);
        updatePlacement(index, newindex);

    }

    //(table button) move song lower
    function onDown(button) {
        <?php
        $sql_code = "SELECT * FROM uploaded_songs WHERE User_ID = " . $currentUser['User_ID'] . " ORDER BY Placement ASC";
        $result = mysqli_query($connection, $sql_code);
        $fp = 0;
        foreach ($result as $r):
            $fp++;
        endforeach;
        ?>
        var table = button.parentNode.parentNode.parentNode;
        var row = button.parentNode.parentNode;

        var index = Array.prototype.indexOf.call(table.children, row);
        index--;
        let newindex = (index == <?php echo $fp - 1; ?> ? (0) : (index + 1));
        console.log("index", index);
        console.log("newindex", newindex);
        updatePlacement(index, newindex);
    }
    function makeTable() {
        const xhr = new XMLHttpRequest();
        const tr = `<tr><th onclick="toggleSort(this)">Title</th><th onclick="toggleSort(this)">Artist</th><th onclick="toggleSort(this)">Album</th><th onclick="toggleSort(this)">Duration</th><th>---</th></tr>`;
        var result;
        xhr.onload = () => {
            if (xhr.readyState == 4 && xhr.status == 200) {
                result = xhr.responseText;
                document.getElementById('playlist').innerHTML = result;
                getCount();
                getFavouriteArtist();
            }
        }
        xhr.open("GET", "update_content.php?tr=" + tr + "&userId=" + <?php echo $currentUser['User_ID']; ?>, true);
        xhr.send();
    }

    //show text tags in playlist card and get image for it too
    function displayTags(tags) {
        // document.getElementById("title").textContent = tags.title || "";
        // document.getElementById("artist").textContent = tags.artist || "";
        // document.getElementById("album").textContent = tags.album || "";
        isPlaceholder = false;

    }
    //get tags, create file url, wait for duration to be available and add song to playlist
    function getTags(url, input) {
        const tags = ID3.getAllTags(url);
        savedtags = tags;
        var file = input.files[0];
        const audioBlob = new Blob(input.files, { type: input.files[0].type });
        const audioURL = URL.createObjectURL(audioBlob);
        getDuration(audioURL, function (length) {
            durationDisplay.textContent = formatDuration(length);
            slider.min = 0;
            slider.max = length;

            // addToPlaylist(savedtags, length, audioURL);
            placement = playlistSize;

            if (file) {
                var formData = new FormData();
                formData.append("song", file);
                formData.append("title", savedtags.title);
                formData.append("artist", savedtags.artist);
                formData.append("album", savedtags.album);
                formData.append("duration", length);
                formData.append("placement", placement);

                var xhr = new XMLHttpRequest();
                xhr.open("POST", "upload.php", true);
                xhr.send(formData);
            }
            var image = tags.picture;
            if (image) {
                var base64String = "";
                for (var i = 0; i < image.data.length; i++) {
                    base64String += String.fromCharCode(image.data[i]);
                }
                var base64 =
                    "data:" + image.format + ";base64," + window.btoa(base64String);

                var imagePath;
                // Send the base64 data to the server
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "saveImage.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        imagePath = xhr.responseText;
                        console.log("Image saved successfully!", imagePath);
                        updateImagePath(imagePath, placement);
                        updateLastListened(placement);
                        makeTable();
                    }
                };
                xhr.send("image=" + encodeURIComponent(base64) + "&userId=" + <?php echo $currentUser['User_ID']; ?>);
            } else {
                document.getElementById("picture").style.display = "none";
            }
        });
    }
    function updateImagePath(path, placement) {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "update_content.php?path=" + path + "&placement=" + placement + "&userId=" + <?php echo $currentUser['User_ID']; ?>, true);
        xhr.send();
    }
    function updateLastListened(placement) {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "update_content.php?placement=" + placement + "&userId=" + <?php echo $currentUser['User_ID']; ?>, true);
        xhr.send();
    }
    function updatePlacement(oldp, newp) {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "update_content.php?newp=" + newp + "&oldp=" + oldp + "&userId=" + <?php echo $currentUser['User_ID']; ?>, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                console.log('pabeigts')
                // makeTable();
                makeTable();
                // location.reload();
            }
        };
        xhr.send();
    }
</script>