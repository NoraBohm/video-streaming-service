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

/**
 * @param bool $action_mode;
 */
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

            // var_dump of commands given to the terminal by FFMPEG, to see if we can rapidly change as well as set stuff such as ratelimits, crf, and resizing. Prioritize resizing and frame-limits. It gets the filters
            // var_dump($video->filters->getIterator());

            // I may need to change the priority of the filters, they may over-write eachother if they are the same???
            // Src: https://github.com/Webbopwork/PHP-FFMpeg-Extended/blob/master/src/FFMpeg/Filters/FiltersCollection.php Look at "add()"


        } else {
            throw_error("Upload is not video");
        }
    } else {
        throw_error("File upload failure");
    }
}

/**
 * @param string $temp_name;
 */
function get_stream($temp_name) {
    $ffprobe = FFMpeg\FFProbe::create();
    return $ffprobe->streams($temp_name)->videos()->first();
}

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
        return 'width 1800p';
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

/**
 * @param string $resolution;
 * @param mixed $filters;
 */
function resolution_work($resolution, $filters) {
    switch ($resolution) {
        case 'height overflow':
        case 'height 4k':
            return $filters->resize(new FFMpeg\Coordinate\Dimension(-1, 2160), FFMpeg\Filters\Video\ResizeFilter::RESIZEMODE_SCALE_HEIGHT);
        case 'width overflow':
        case 'width 4k':
            return $filters->resize(new FFMpeg\Coordinate\Dimension(3584, -1), FFMpeg\Filters\Video\ResizeFilter::RESIZEMODE_SCALE_WIDTH);

        case 'height 1440p':
            return $filters->resize(new FFMpeg\Coordinate\Dimension(-1, 1440), FFMpeg\Filters\Video\ResizeFilter::RESIZEMODE_SCALE_HEIGHT);
        case 'width 1440p':
            return $filters->resize(new FFMpeg\Coordinate\Dimension(2560, -1), FFMpeg\Filters\Video\ResizeFilter::RESIZEMODE_SCALE_WIDTH);

        case 'height 1080p':
            return $filters->resize(new FFMpeg\Coordinate\Dimension(-1, 1080), FFMpeg\Filters\Video\ResizeFilter::RESIZEMODE_SCALE_HEIGHT);
        case 'width 1080p':
            return $filters->resize(new FFMpeg\Coordinate\Dimension(1920, -1), FFMpeg\Filters\Video\ResizeFilter::RESIZEMODE_SCALE_WIDTH);

        case 'height 720p':
            return $filters->resize(new FFMpeg\Coordinate\Dimension(-1, 720), FFMpeg\Filters\Video\ResizeFilter::RESIZEMODE_SCALE_HEIGHT);
        case 'width 720p':
            return $filters->resize(new FFMpeg\Coordinate\Dimension(1280, -1), FFMpeg\Filters\Video\ResizeFilter::RESIZEMODE_SCALE_WIDTH);

        case 'height 480p':
            return $filters->resize(new FFMpeg\Coordinate\Dimension(-1, 480), FFMpeg\Filters\Video\ResizeFilter::RESIZEMODE_SCALE_HEIGHT);
        case 'width 480p':
            return $filters->resize(new FFMpeg\Coordinate\Dimension(848, -1), FFMpeg\Filters\Video\ResizeFilter::RESIZEMODE_SCALE_WIDTH);
    }
}

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

session_start();
if ($_SESSION['id']) {
    
}

if ($video_id) {
    header("Location: /watch?id=" . urlencode(dechex($video_id)));
} else {
    header("Location: /upload");
}
?>