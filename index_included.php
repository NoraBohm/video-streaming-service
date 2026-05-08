<?php

// Load installed packages
require_once 'vendor/autoload.php';

// Load secrets from the file .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Connect to database
$mysqli = new mysqli(
    'ostrawebb.se', 
    $_ENV['DB_USER'], 
    $_ENV['DB_PASS'],
    $_ENV['DB_USER']
);

// Get all movies
$result = $mysqli->query("SELECT * FROM movies");
$movies = $result->fetch_all(MYSQLI_ASSOC);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/main.css" rel="stylesheet">
    <title>Document</title>
</head>

<body>

    <?php

    foreach ($movies as $movie) {
        echo '<p>' . $movie['title'] . '</p>';
    }

    ?>

</body>

</html>