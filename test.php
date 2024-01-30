<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div id="something">0</div>
    <button onclick="myFunction()">press me</button>

    <script>
        function myFunction() {
            const something = document.getElementById('something');
            const counter = parseInt(something.innerText);

            // Use AJAX to send the counter value to a PHP script
            const xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Update the content of the div with the response from the PHP script
                    something.innerText = xhr.responseText;
                }
            };
            xhr.open("GET", "update_content.php?counter=" + counter, true);
            xhr.send();
        }
    </script>
</body>

</html>