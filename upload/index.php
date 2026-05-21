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
            <input type="file" value="Upload video" name="video-upload" id="video-upload" accept="video/*" required>
        </p>
        <p>
            <label for="title">Title</label>
            <input type="text" name="title" placeholder="Title" id="title" required>
        </p>
        <p>
            <label for="description">Description</label>
            <textarea id="description" name="description" placeholder="Description" required></textarea>
        </p>
        <p>
            <label for="action-mode">Action mode</label>
            <input type="checkbox" name="action-mode" value="action mode" id="action-mode">
        </p>
        <input type="submit" value="Upload">
    </form>
</article>

</body>
</html>
