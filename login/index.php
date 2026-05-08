<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
    
</head>
<body>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/inserts/error_report.php'; ?>

<article>
    <h1><label for="form">Log in</label></h1>
    <form action="/login/login.php" method="POST" id="form">
        <p>
            <label for="username-or-email">Username or eMail</label>
            <input type="text" name="username-or-email" placeholder="Username or eMail" id="username-or-email" required>
        </p>
        <p>
            <label for="password">Username or eMail</label>
            <input type="password" name="password" placeholder="Password" id="password" required>
        </p>
        <input type="submit" value="Log in">
    </form>
</article>

</body>
</html>