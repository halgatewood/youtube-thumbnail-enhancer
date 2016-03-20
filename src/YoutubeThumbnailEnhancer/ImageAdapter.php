<?php

namespace YoutubeThumbnailEnhancer;

require __DIR__ . '/../../vendor/autoload.php';

class ImageAdapter
{
    public function getImageWidth($image)
    {
        return imagesx($image);
    }

    public function getImageHeight($image)
    {
        return imagesy($image);
    }

    public function imagePng($image, $filename = null, $quality = null, $filters = null)
    {
        return imagepng($image, $filename, $quality, $filters);
    }

    public function createImageFromJpgPath($jpgPath)
    {
        return imagecreatefromjpeg($jpgPath);
    }

    public function imageJpeg($image, $filename = null, $quality = null)
    {
        return imagejpeg($image, $filename, $quality);
    }

    public function createImageFromPngPath($pngPath)
    {
        return imagecreatefrompng($pngPath);
    }

    public function createTrueColorImage($width, $height)
    {
        return imagecreatetruecolor($width, $height);
    }

    public function copyPartOfImage($dstIm, $srcIm, $dstX, $dstY, $srcX, $srcY, $srcW, $srcH)
    {
        return imagecopy($dstIm, $srcIm, $dstX, $dstY, $srcX, $srcY, $srcW, $srcH);
    }

    public function setBlendingMode($image, $blendingMode)
    {
        return imagealphablending($image, $blendingMode);
    }

    public function imageColorAllocate($image, $red, $green, $blue)
    {
        return imagecolorallocate($image, $red, $green, $blue);
    }

    public function imageFilledRectangle($image, $x1, $y1, $x2, $y2, $color)
    {
        return imagefilledrectangle($image, $x1, $y1, $x2, $y2, $color);
    }
}