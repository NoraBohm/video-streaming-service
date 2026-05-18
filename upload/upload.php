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

function upload_media($action_mode) {
    $video_file = $_FILES['video-upload'];
    //$name = $video_file['name'];
    $temp_name = $video_file['tmp_name'];
    if ($video_file['errors'] == 0) {
        $stream = get_stream($temp_name);
        if ($stream->isVideo()) {
            $ffmpeg = FFMpeg\FFMpeg::create();
            $video = $ffmpeg->open($temp_name);
        
            $fps = $stream->getFrameRate();
            $dimensions = $stream->getDimensions();
            $height = $dimensions->getHeight();
            $width = $dimensions->getwidth();
            $resolution = get_resolution($height, $width);

            $video_filters = $video->filters();

            if ($fps > 30 && !$action_mode) {
                // https://www.reddit.com/r/AV1/comments/yf62wc/gop_size/
                //$video_filters->framerate(30, $seek_time*30);
                $video_filters->framerate(new FFMpeg\Coordinate\FrameRate(30), 300);
            } elseif ($fps > 60 && $action_mode) {
                $video_filters->framerate(new FFMpeg\Coordinate\FrameRate(60), 600);
            }

            $video_filters->synchronize();
        } else {
            throw_error("Upload is not video");
        }
    } else {
        throw_error("File upload failure");
    }
}

function get_stream($temp_name) {
    $ffprobe = FFMpeg\FFProbe::create();
    return $ffprobe->streams($temp_name)->videos()->first();
}

function get_resolution($height, $width) {
    // maybe modify later for support for 2:1 instead of 16:9
    if ($height > 2160) {
        return 'height overflow';
    } elseif ($width > 4096) {
        return 'width overflow';
    } elseif ($height > 1440 || $width > 5120) {
        return '4k';
    } elseif ($height > 1080 || $width > 1920) {
        return '1440p';
    } elseif ($height > 720 || $width > 1280) {
        return '1800p';
    } elseif ($height > 480 || $width > 848) {
        return '720p';
    } elseif ($height > 360 || $width > 640) {
        return '480p';
    } else {
        return 'under 480p';
    }
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