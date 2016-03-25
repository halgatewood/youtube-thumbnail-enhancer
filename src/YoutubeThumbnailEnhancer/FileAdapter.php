<?php

namespace YoutubeThumbnailEnhancer;

require __DIR__ . '/../../vendor/autoload.php';

class FileAdapter
{
    public function fileExists($filename)
    {
        return file_exists($filename);
    }

    public function removeFile($filename, $ignoreErrors = true)
    {
        return $ignoreErrors ? @unlink($filename) : unlink($filename);
    }
}