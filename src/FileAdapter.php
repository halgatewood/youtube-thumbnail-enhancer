<?php

namespace YoutubeThumbnailEnhancer;

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