<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/functions.php';

// Modify to upload video
function upload_video_to_database($db, $title, $description) {
    $request = $db->prepare("INSERT INTO video_videos (title, description) VALUES (?, ?)");
    $request->bind_param("ss", $title, $description);
    //$request->execute();
    $request_results = $request->execute();
    return [$request_results, $db->insert_id];
    //return $db->insert_id;
}

function link_video_to_author($db, $author_id, $video_id) {
    $request = $db->prepare("INSERT INTO video_videos (author_id, video_id) VALUES (?, ?)");
    $request->bind_param("ii", $author_id, $video_id);
    return $request->execute();
}

function upload_media() {
    $video_file = $_FILES['video-upload'];
    //$name = $video_file['name'];
    $temp_name = $video_file['tmp_name'];
    if ($video_file['errors'] == 0) {
        $ffmpeg = FFMpeg\FFMpeg::create();
        $video = $ffmpeg->open($temp_name);
        
        $stream = get_stream($temp_name);
        $fps = $stream->getFrameRate();

        $video_filters = $video->filters();

        if ($fps > 30) {
            // https://www.reddit.com/r/AV1/comments/yf62wc/gop_size/
            //$video_filters->framerate(30, $seek_time*30);
            $video_filters->framerate(new FFMpeg\Coordinate\FrameRate(30), 300);
        }

        $video_filters->synchronize();
    } else {
        throw_error("File upload failure");
    }
}

function get_stream($temp_name) {
    $ffprobe = FFMpeg\FFProbe::create();
    return $ffprobe->streams($temp_name)->videos()->first();
}

session_start();
if ($_SESSION['id']) {
    
}

if ($video_id) {
    header("Location: /watch?id=" . urlencode(dechex($video_id)));
} else {
    header("Location: /upload");
}
?>