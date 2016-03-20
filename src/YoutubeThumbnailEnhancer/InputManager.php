<?php

namespace YoutubeThumbnailEnhancer;

require __DIR__ . '/../../vendor/autoload.php';

class InputManager
{
    public function isUrl($url)
    {
        return substr($url, 0, 4) == "www."
            || substr($url, 0, 8) == "youtube."
            || substr($url, 0, 8) == "youtu.be"
            || substr($url, 0, 7) == "http://"
            || substr($url, 0, 8) == "https://";
    }

    public function hasProtocol($url)
    {
        return substr($url, 0, 7) == 'http://'
        || substr($url, 0, 8) == 'https://';
    }

    public function sanitizeUrl($url)
    {
        $sanitizedUrl = trim($url);

        if ($this->isUrl($url) && !$this->hasProtocol($url)) {
            $sanitizedUrl = 'http://' . $url;
        }

        return $sanitizedUrl;
    }
}