<?php

namespace YoutubeThumbnailEnhancer;

require __DIR__ . '/../../vendor/autoload.php';

class InputManager
{
    public function isUrl($url)
    {
        return $this->startsWith($url, 'www.')
            || $this->startsWith($url, 'youtube.')
            || $this->startsWith($url, 'youtu.be')
            || $this->startsWith($url, 'http://')
            || $this->startsWith($url, 'https://');
    }

    public function hasProtocol($url)
    {
        return $this->startsWith($url, 'http://') || $this->startsWith($url, 'https://');
    }

    public function sanitizeUrl($url)
    {
        $sanitizedUrl = trim($url);

        if ($this->isUrl($url) && !$this->hasProtocol($url)) {
            $sanitizedUrl = 'http://' . $url;
        }

        return $sanitizedUrl;
    }

    private function startsWith($haystack, $needle) {
        return '' === $needle
            || false !== strrpos($haystack, $needle, -strlen($haystack));
    }
}