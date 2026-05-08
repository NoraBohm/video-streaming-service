<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/inserts/error_report.php'; ?>

<article>
    <h1><label for="form">Register</label></h1>
    <form action="/register/register.php" method="POST" id="form">
        <p>
            <label for="displayname">Displayname</label>
            <input type="text" name="displayname" placeholder="Displayname" id="displayname">
        </p>
        <p>
            <label for="username">Username</label>
            <input type="text" name="username" placeholder="Username" id="username" required>
        </p>
        <p>
            <label for="email">eMail</label>
            <input type="text" name="email" placeholder="eMail" id="email" required>
        </p>
        <p>
            <label for="password">Password</label>
            <input type="password" name="password" placeholder="Password" id="password" required>
        </p>
        <input type="submit" value="Register">
    </form>
</article>

</body>
</html>