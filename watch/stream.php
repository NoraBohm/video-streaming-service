<?php
$video_id = isset($_GET['id']) ? hexdec($_GET['id']) : null;
$resolution = isset($_GET['res']) ? $_GET['res'] : null;

if (empty($video_id) || empty($resolution)) {
    header("HTTPS/1.1 400 Bad Request");
    exit;
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/functions.php';

$db = get_database();

//$video_data = get_from_where_is_type($db, "video_videos", "id", $video_id, "i");
$author_data = get_from_where_is_type($db, "video_videos_authors", "video_id", $video_id, "i");

$author_id = $author_data['author_id'];
//$video_res = $video_data['max'];

// Recreates the filename that is used to store data, as well as using basename() to stop directory traversal attacks
$file_name = basename("$author_id-$video_id-$resolution.webm");

// Create absolute file path using file name
$file_path = $_SERVER['DOCUMENT_ROOT'] . "/media/userdata/videos/$file_name";

// Aquiring and running the class that makes streaming possible
require_once 'stream_class.php';

$stream = new VideoStream($file_path);
$stream->start();
?>