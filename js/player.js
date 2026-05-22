// Gets the HTML elements that are used for video playback
const player = document.getElementById('player');
const source = document.getElementsByTagName('source')[0];

// Gets the GET post URL search parameters
const urlParams = new URLSearchParams(window.location.search);
const video_id = urlParams.get("id");

// Don't use the interger checker because we use hexadecimals now
/*if (!Number.isInteger(video_id) || (0 > video_id)) {
    throwError("No or invalid id");
}*/

// Log buffer progress in the terminal
function updateBufferProgress(bufferProgress) {
    console.log(`Buffer progress: ${bufferProgress}`);
}

// Log a error message int the terminal
function throwError(message) {
    console.log(`ERROR: ${message}`);
}

// Load a video by modifying the HTML elements to link to the PHP media stream and then reload the HTML <Video> element
// It also checks for if you have requested to not have autoplay and not play it in that case 
function loadVideo(id, res) {
    source.src = `/watch/stream.php?id=${encodeURIComponent(id)}&res=${encodeURIComponent(res)}`;

    player.load();

    if (!urlParams.has("no-autoplay")) {
        // Impliment autoplay
        player.addEventListener('loadeddata', function() {
            player.play().catch(e => {
                console.log(`Autoplay failed: {e}`);
            });
        }, { once: true });
    }
}

// Video player analytics
player.addEventListener('progress', function() {
    const buffered = this.buffered;
    if (buffered.length > 0) {
        const endOfBuffer = buffered.end(buffered.length - 1);
        const duration = this.duration;
        const bufferProgress = endOfBuffer / duration;
        updateBufferProgress(bufferProgress);
    }  
});

// Load video by gathered video ID, as well as a resolution set simply test the code, the resolution should be selsectable by the user normally.
loadVideo(video_id, '720p');