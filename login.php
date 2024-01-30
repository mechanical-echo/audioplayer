<?php
require "database.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music player - login</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <h1>Log in</h1>
    <section id="login-container">
        <h1>Welcome back!</h1>
        <form method="post">
            <input type="text" name="login-username" id="login-username" placeholder="username">
            <input type="password" name="login-password" id="login-password" placeholder="password">
            <input type="submit" value="Log in" name="login">
        </form>
        <a href="signup.php">Sign up</a>
    </section>
</body>

</html>