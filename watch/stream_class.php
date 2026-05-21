<?php
// https://codesamplez.com/programming/php-html5-video-streaming-tutorial
class VideoStream
{
    private $path = "";
    private $stream = null;
    private $buffer = 262144; // 256KB buffer for optimal performance
    private $start = -1;
    private $end = -1;
    private $size = 0;
    /**
     * @param string $filePath;
     */
    function __construct($filePath) 
    {
        $this->path = $filePath;
    }
    
    /**
     * Initialize and validate the video file
     */
    private function init()
    {
        // Validate file exists and is readable
        if (!file_exists($this->path) || !is_readable($this->path)) {
            header("HTTP/1.1 404 Not Found");
            exit;
        }
        
        $this->size = sprintf("%u", filesize($this->path));
        $this->start = 0;
        $this->end = $this->size - 1;
    }
    
    /**
     * Parse Range header and set start/end positions
     */
    private function parseRange()
    {
        if (!isset($_SERVER['HTTP_RANGE']) || 
            !preg_match('/bytes=(\d*)-(\d*)/', $_SERVER['HTTP_RANGE'], $matches)) {
            return;
        }
        
        $start = $matches[1];
        $end = $matches[2];
        
        if (!empty($start)) {
            $this->start = intval($start);
        }
        
        if (!empty($end)) {
            $this->end = min(intval($end), $this->size - 1);
        }
    }
    
    /**
     * Set appropriate headers for video streaming
     */
    private function setHeaders()
    {
        // Prevent caching issues
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        // Set content type based on file extension
        //$mimeType = $this->getMimeType();
        $mimeType = 'video/webm';
        header("Content-Type: {$mimeType}");
        
        // Set range headers
        header("Accept-Ranges: bytes");
        header("Content-Length: " . ($this->end - $this->start + 1));
        
        if ($this->start > 0 || $this->end < ($this->size - 1)) {
            header('HTTP/1.1 206 Partial Content');
            header("Content-Range: bytes {$this->start}-{$this->end}/{$this->size}");
        }
    }
    
    /**
     * Stream the video content to browser
     */
    public function start()
    {
        $this->init();
        $this->parseRange();
        $this->setHeaders();
        
        // Create stream context for better performance
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 30
            ]
        ]);
        
        if (!($this->stream = fopen($this->path, 'rb', false, $context))) {
            header("HTTP/1.1 500 Internal Server Error");
            exit;
        }
        
        // Seek to start position
        if ($this->start > 0) {
            fseek($this->stream, $this->start);
        }
        
        // Stream the content in chunks
        $this->readBuffer();
        fclose($this->stream);
    }
    
    /**
     * Read and output file buffer
     */
    private function readBuffer()
    {
        $bytesToRead = $this->end - $this->start + 1;
        
        while (!feof($this->stream) && $bytesToRead > 0) {
            $chunkSize = min($this->buffer, $bytesToRead);
            $chunk = fread($this->stream, $chunkSize);
            
            if ($chunk === false) {
                break;
            }
            
            echo $chunk;
            flush();
            
            $bytesToRead -= strlen($chunk);
            
            // Prevent timeout on slow connections
            if (connection_status() !== CONNECTION_NORMAL) {
                break;
            }
        }
    }
}
?>