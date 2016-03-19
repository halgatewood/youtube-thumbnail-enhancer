<?php

namespace YoutubeThumbnailEnhancer;

require __DIR__ . '/../../vendor/autoload.php';

class YoutubeThumbnailer
{
    public function getYouTubeIdFromInput($input)
    {
        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i';
        preg_match($pattern, $input, $matches);

        return isset($matches[1]) ? $matches[1] : false;
    }
}