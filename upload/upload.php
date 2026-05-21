<?php

//use FFMpeg\FFMpeg;

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/functions.php';

// This uploads the video information to the database
function upload_video_to_database($db, $title, $description, $max_resolution) {
    $request = $db->prepare("INSERT INTO video_videos (title, description, max_resolution) VALUES (?, ?, ?)");
    $request->bind_param("sss", $title, $description, $max_resolution);
    //$request->execute();
    $request_results = $request->execute();
    return [$request_results, $db->insert_id];
    //return $db->insert_id;
}

// This connects the video to the author in a database, allowing multiple video authors
function link_video_to_author($db, $author_id, $video_id) {
    $request = $db->prepare("INSERT INTO video_videos (author_id, video_id) VALUES (?, ?)");
    $request->bind_param("ii", $author_id, $video_id);
    return $request->execute();
}

// This is the primarily video uploading process, it does conversion, saving, as well as the functions of the functions used in it.
/**
 * @param bool $action_mode;
 * @param int $author_id;
 */
function upload_media($action_mode, $author_id, $title, $description) {
    $video_file = $_FILES['video-upload'];
    //$name = $video_file['name'];
    $temp_name = $video_file['tmp_name'];
    if ($video_file['errors'] == 0) {
        $stream = get_stream($temp_name);
        if ($stream->isVideo()) {
            $ffmpeg = FFMpeg\FFMpeg::create();
            $video = $ffmpeg->open($temp_name);

            // Getting data about the video
            $fps = $stream->getFrameRate();
            $dimensions = $stream->getDimensions();
            $height = $dimensions->getHeight();
            $width = $dimensions->getwidth();
            $resolution = get_resolution($height, $width);

            // Getting the filters of the video
            $video_filters = $video->filters();

            // Functionally applying action mode, limiting it ot 60 or 30 FPS depedning on if it's selected
            if ($fps > 30 && !$action_mode) {
                // https://www.reddit.com/r/AV1/comments/yf62wc/gop_size/
                //$video_filters->framerate(30, $seek_time*30);
                $video_filters->framerate(new FFMpeg\Coordinate\FrameRate(30), 300);
            } elseif ($fps > 60 && $action_mode) {
                $video_filters->framerate(new FFMpeg\Coordinate\FrameRate(60), 600);
            }

            // var_dump of commands given to the terminal by FFMPEG, to see if we can rapidly change as well as set stuff such as ratelimits, crf, and resizing. Prioritize resizing and frame-limits. It gets the filters
            // var_dump($video->filters->getIterator());

            // I may need to change the priority of the filters, they may over-write eachother if they are the same???
            // Src: https://github.com/Webbopwork/PHP-FFMpeg-Extended/blob/master/src/FFMpeg/Filters/FiltersCollection.php Look at "add()"

            // For the time being just save one resolution

            resolution_work($resolution, $video_filters);

            // Setting the quality level of the video, since it's AV1 then 35 is 11 higher than practically lossless, so it's a bit worse quality than practically lossless
            $video_filters->constantRateFactor('35');

            $video_filters->synchronize();

            // Getting resolution text fit for a media file
            $saved_resolution = save_resolution($resolution);

            // Getting pre-prepared database
            $db = get_database();

            // Uploading video to database
            list($video_success, $video_id) = upload_video_to_database($db, $title, $description, $saved_resolution);

            if ($video_success) {
                // Saving the video locally in the media database
                $video->save(new FFMpeg\Format\Video\WebM('libopus', 'libaom-av1'), $_SERVER['DOCUMENT_ROOT'] . "/media/userdata/videos/$author_id-$video_id-$saved_resolution.webm");
                // Linking video to author
                $link_success = link_video_to_author($db, $author_id, $video_id);
                if ($link_success) {
                    return $video_id;
                } else {
                    throw_error("Linking author to video failed");
                }
            } else {
                throw_error("Converting video failed");
            }

        } else {
            throw_error("Upload is not video");
        }
    } else {
        throw_error("File upload failure");
    }
}

// This gets the FFProbe video stream, it allows access to data about the video
/**
 * @param string $temp_name;
 */
function get_stream($temp_name) {
    $ffprobe = FFMpeg\FFProbe::create();
    return $ffprobe->streams($temp_name)->videos()->first();
}


// This is upposed to loop in order to cover all the resolutions including and lower than the max resolution
// This has not been impilmented yet
/**
 * @param string $resolution;
 */
function resolution_loop($resolution) {
    while (true) {
        // Here make and export a version of the video with 
        if ($resolution == 'under 480p') {
            return $resolution;
        } else {
            $resolution = lower_resolution($resolution);
        }
    }
}

// This gets a text representation of what resolution to use from the height and width
/**
 * @param int $height;
 * @param int $width;
 */
function get_resolution($height, $width) {
    // maybe modify later for support for 2:1 instead of 16:9
    if ($height > 2160) {
        return 'height overflow';
    } elseif ($width > 3584) {
        return 'width overflow';
    } elseif ($height > 1440) {
        return 'height 4k';
    } elseif ($width > 2560) {
        return 'width 4k';
    } elseif ($height > 1080) {
        return 'height 1440p';
    } elseif ($width > 1920) {
        return 'width 1440p';
    } elseif ($height > 720) {
        return 'height 1800p';
    } elseif ($width > 1280) {
        return 'width 1080p';
    } elseif ($height > 480) {
        return 'height 720p';
    } elseif ($width > 848) {
        return 'width 720p';
    } elseif ($height > 360) {
        return 'height 480p';
    } elseif ($width > 640) {
        return 'width 480p';
    } else {
        return 'under 480p';
    }
}

// This based on the resolution adds different filters
/**
 * @param string $resolution;
 * @param mixed $filters;
 */
function resolution_work($resolution, $filters) {
    switch ($resolution) {
        // Setting the resolution filters here, intentionally left non-blocking to allow the bitrate filters
        case 'height overflow':
        case 'height 4k':
            $filters->resize(new FFMpeg\Coordinate\Dimension(-1, 2160), FFMpeg\Filters\Video\ResizeFilter::RESIZEMODE_SCALE_HEIGHT);
        case 'width overflow':
        case 'width 4k':
            $filters->resize(new FFMpeg\Coordinate\Dimension(3584, -1), FFMpeg\Filters\Video\ResizeFilter::RESIZEMODE_SCALE_WIDTH);

        case 'height 1440p':
            $filters->resize(new FFMpeg\Coordinate\Dimension(-1, 1440), FFMpeg\Filters\Video\ResizeFilter::RESIZEMODE_SCALE_HEIGHT);
        case 'width 1440p':
            $filters->resize(new FFMpeg\Coordinate\Dimension(2560, -1), FFMpeg\Filters\Video\ResizeFilter::RESIZEMODE_SCALE_WIDTH);

        case 'height 1080p':
            $filters->resize(new FFMpeg\Coordinate\Dimension(-1, 1080), FFMpeg\Filters\Video\ResizeFilter::RESIZEMODE_SCALE_HEIGHT);
        case 'width 1080p':
            $filters->resize(new FFMpeg\Coordinate\Dimension(1920, -1), FFMpeg\Filters\Video\ResizeFilter::RESIZEMODE_SCALE_WIDTH);

        case 'height 720p':
            $filters->resize(new FFMpeg\Coordinate\Dimension(-1, 720), FFMpeg\Filters\Video\ResizeFilter::RESIZEMODE_SCALE_HEIGHT);
        case 'width 720p':
            $filters->resize(new FFMpeg\Coordinate\Dimension(1280, -1), FFMpeg\Filters\Video\ResizeFilter::RESIZEMODE_SCALE_WIDTH);

        case 'height 480p':
            $filters->resize(new FFMpeg\Coordinate\Dimension(-1, 480), FFMpeg\Filters\Video\ResizeFilter::RESIZEMODE_SCALE_HEIGHT);
        case 'width 480p':
            $filters->resize(new FFMpeg\Coordinate\Dimension(848, -1), FFMpeg\Filters\Video\ResizeFilter::RESIZEMODE_SCALE_WIDTH);

        
        // Setting the bitrate filters here
        // Numbers are based on this website: https://www.videosdk.live/developer-hub/media-server/video-bitrate-for-streaming
        case 'height overflow':
        case 'height 4k':
        case 'width overflow':
        case 'width 4k':
            // 500Kb over site 30fps for max
            return $filters->bitRateRange('14000Kb', '34500Kb');
        
        case 'height 1440p':
        case 'width 1440p':
            // Based on 1080p and 4k values
            return $filters->bitRateRange('9000Kb', '12000Kb');

        case 'height 1080p':
        case 'width 1080p':
            // 300Kb over site 30fps for max
            return $filters->bitRateRange('4500Kb', '6300Kb');

        case 'height 720p':
        case 'width 720p':
            // 200Kb over site 30fps for max
            return $filters->bitRateRange('2500Kb', '4200Kb');

        case 'height 480p':
        case 'width 480p':
            // 200Kb over site 30fps for max
            return $filters->bitRateRange('1000Kb', '2200Kb');

        case 'under 480p':
            // 200Kb over site 30fps for max
            return $filters->bitRateRange('600Kb', '1100Kb');
    }
}

// This converts a text representation of a resolution to a text representation of a lower resolution, used for creating multiple resolution options
/**
 * @param string $resolution;
 */
function lower_resolution($resolution) {
    switch ($resolution) {
        case 'height overflow':
        case 'height 4k':
            return 'height 1440p';
        case 'width overflow':
        case 'width 4k':
            return 'width 1440p';
        
        case 'height 1440p':
            return 'height 1080p';
        case 'width 1440p':
            return 'width 1080p';

        case 'height 1080p':
            return 'height 720p';
        case 'width 1080p':
            return 'width 720p';

        case 'height 720p':
            return 'height 480p';
        case 'width 720p':
            return 'width 480p';

        case 'height 480p':
        case 'width 480p':
            return 'under 480p';
    }
}

// This converts the text representation of a resolution to a text representation that doesn't care for if it's width of height based, making it better for media upload.
/**
 * @param string $resolution;
 */
function save_resolution($resolution) {
    switch ($resolution) {
        case 'height overflow':
        case 'height 4k':
        case 'width overflow':
        case 'width 4k':
            return '4k';
        
        case 'height 1440p':
        case 'width 1440p':
            return '1440p';

        case 'height 1080p':
        case 'width 1080p':
            return '1080p';

        case 'height 720p':
        case 'width 720p':
            return '720p';

        case 'height 480p':
        case 'width 480p':
            return '480p';

        case 'under 480p':
            return 'u480p';
    }
}

// Getting details from the webpage before
$title = $_POST['title'];
$description = $_POST['description'];
// Action mode allows up to 60 FPS ina video, while it's normally limited to up to 30 FPS
$action_mode = $_POST['action-mode'] == 'action mode';

// Checking that the data is there
$precheck = (!is_null($title) && !is_null($description));

$video_id = null;

session_start();
$author_id = $_SESSION['id'];
if ($precheck && !is_null($author_id)) {
    $video_id = upload_media($action_mode, $author_id, $title, $description);
}

// If the video is successfully uploaded send 
if (!is_null($video_id)) {
    // Use hex version of video id in link to shorten it and make it neater
    header("Location: /watch?id=" . urlencode(dechex($video_id)));
} else {
    header("Location: /upload");
}
?>
