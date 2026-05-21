const player = document.getElementById('player');
const source = document.getElementsByTagName('source')[0];

console.log(player, source);
// https://stackoverflow.com/questions/901115/how-can-i-get-query-string-values-in-javascript
/*const urlParams = new Proxy(new URLSearchParams(window.location.search), {
    get: (searchParams, prop) => searchParams.get(prop),
});*/
const urlParams = new URLSearchParams(window.location.search);
const video_id = urlParams.get("id");

// Don't use the interger checker because we use hexadecimals now
/*if (!Number.isInteger(video_id) || (0 > video_id)) {
    throwError("No or invalid id");
}*/

function updateBufferProgress(bufferProgress) {
    console.log(`Buffer progress: ${bufferProgress}`);
}

function throwError(message) {
    console.log(`ERROR: ${message}`);
}

function loadVideo(id, res) {
    /*
    res {
    0 = audio only (maybe impliment)
    1 = 480p
    2 = 720p
    3 = 1080p
    4 = 4k
    }

    Nvm just make "res" a string, because that's how it's stored in the database
    */
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

loadVideo(video_id, '720p');