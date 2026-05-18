<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<article>
    <h1><label for="form">Video upload</label></h1>
    <form action="/upload/upload.php" method="POST" enctype="multipart/form-data" id="form">
        <p>
            <label for="video-upload">Upload video</label>
            <input type="file" value="Upload video" name="video-upload" id="video-upload">
        </p>
        <input type="submit" value="Upload">
    </form>
</article>

</body>
</html>