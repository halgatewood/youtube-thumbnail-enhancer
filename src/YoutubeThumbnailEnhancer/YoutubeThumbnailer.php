<?php

namespace YoutubeThumbnailEnhancer;

require __DIR__ . '/../../vendor/autoload.php';

class YoutubeThumbnailer
{
    const THUMBNAILS_DIRECTORY = 'i/';
    const DEFAULT_QUALITY = 'mq';
    const HIGH_QUALITY = 'hq';
    const MEDIUM_QUALITY = 'mq';
    const DEFAULT_SHOW_PLAY = false;
    const SHOW_PLAY = true;
    const NOT_SHOW_PLAY = false;
    const DEFAULT_APPLY_REFRESH = false;
    const APPLY_REFRESH = true;
    const NOT_APPLY_REFRESH = false;
    const JPG_EXTENSION = '.jpg';
    const PNG_EXTENSION = '.png';
    const IMAGE_UTILS_DIR = 'imageUtils/';

    private $quality;
    private $input;
    private $play;
    private $refresh;
    /** @var InputManager */
    private $inputManager;
    /** @var ImageAdapter */
    private $imageAdapter;
    /** @var FileAdapter */
    private $fileAdapter;
    /** @var NetworkAdapter */
    private $networkAdapter;

    public function __construct()
    {
        $this->quality = self::DEFAULT_QUALITY;
        $this->play = self::DEFAULT_SHOW_PLAY;
        $this->refresh = self::DEFAULT_APPLY_REFRESH;
        $this->inputManager = new InputManager();
        $this->imageAdapter = new ImageAdapter();
        $this->fileAdapter = new FileAdapter();
        $this->networkAdapter = new NetworkAdapter();
    }

    public function getQuality()
    {
        return $this->quality;
    }

    public function getInput()
    {
        return $this->input;
    }

    public function getPlay()
    {
        return $this->play;
    }

    public function getRefresh()
    {
        return $this->refresh;
    }

    public function setRequestParams($requestParams)
    {
        if (array_key_exists('quality', $requestParams)) {
            $this->quality = $requestParams['quality'];
        }

        if (array_key_exists('inpt', $requestParams)) {
            $this->input = $this->inputManager->sanitizeUrl($requestParams['inpt']);
        }

        if (array_key_exists('play', $requestParams)) {
            $this->play = true;
        }

        if (array_key_exists('refresh', $requestParams)) {
            $this->refresh = true;
        }
    }

    public function getVideoId()
    {
        if (!$this->inputManager->isUrl($this->input)) {
            return $this->input;
        }

        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i';
        preg_match($pattern, $this->input, $matches);

        return isset($matches[1]) ? $matches[1] : false;
    }

    public function getFileName()
    {
        $fileName = $this->getVideoId();

        if (self::MEDIUM_QUALITY === $this->quality) {
            $fileName .= '-'. self::MEDIUM_QUALITY;
        }

        if (self::SHOW_PLAY == $this->play) {
            $fileName .= '-play';
        }

        return $fileName;
    }

    public function generateThumbnail()
    {
        if (!($this->getVideoId())) {
            $this->returnResponse('Status: 404 Not Found', 'YouTube ID not found');
        }

        if (!$this->getRefresh() && $this->availableInCache()) {
            $this->returnResponse(
                'Location: ' . YoutubeThumbnailer::THUMBNAILS_DIRECTORY . $this->getFileName() . self::JPG_EXTENSION
            );
        }

        if (!$this->youtubeVideoExists($this->getVideoId())) {
            $this->returnResponse(
                'Status: 404 Not Found', 'No YouTube video found or YouTube timed out. Try again soon.'
            );
        }

        $image = $this->createImageFromYoutubeThumb();
        if (self::HIGH_QUALITY === $this->getQuality()) {
            $image = $this->convertImageToHighQuality($image);
        }


        // ADD THE PLAY ICON
        $play_icon = $this->getPlay() ? "play-" : "noplay-";
        $play_icon .= $this->getQuality() . self::PNG_EXTENSION;
        $logoImage = $this->imageAdapter->createImageFromPngPath(self::IMAGE_UTILS_DIR . $play_icon);

        $this->imageAdapter->setBlendingMode($logoImage, true);

        $logoWidth = $this->imageAdapter->getImageWidth($logoImage);
        $logoHeight = $this->imageAdapter->getImageHeight($logoImage);

        // CENTER PLAY ICON
        $left = round($this->imageAdapter->getImageWidth($image) / 2) - round($logoWidth / 2);
        $top = round($this->imageAdapter->getImageHeight($image) / 2) - round($logoHeight / 2);


        // CONVERT TO PNG SO WE CAN GET THAT PLAY BUTTON ON THERE
        $this->imageAdapter->copyPartOfImage($image, $logoImage, $left, $top, 0, 0, $logoWidth, $logoHeight);
        $this->imageAdapter->imagePng($image, $this->getFileName() . self::PNG_EXTENSION, 9);


        // MASHUP FINAL IMAGE AS A JPEG
        $input = $this->imageAdapter->createImageFromPngPath($this->getFileName() . self::PNG_EXTENSION);
        $output = $this->imageAdapter->createTrueColorImage($this->imageAdapter->getImageWidth($image), $this->imageAdapter->getImageHeight($image));
        $white = $this->imageAdapter->imageColorAllocate($output, 255, 255, 255);
        $this->imageAdapter->imageFilledRectangle($output, 0, 0, $this->imageAdapter->getImageWidth($image), $this->imageAdapter->getImageHeight($image), $white);
        $this->imageAdapter->copyPartOfImage($output, $input, 0, 0, 0, 0, $this->imageAdapter->getImageWidth($image), $this->imageAdapter->getImageHeight($image));

        // OUTPUT TO 'i' FOLDER
        $this->imageAdapter->imageJpeg($output, self::THUMBNAILS_DIRECTORY . $this->getFileName() . self::JPG_EXTENSION, 95);

        // UNLINK PNG VERSION
        $this->fileAdapter->removeFile($this->getFileName() . self::PNG_EXTENSION);

        // REDIRECT TO NEW IMAGE
        $this->returnResponse('Location: ' . self::THUMBNAILS_DIRECTORY . $this->getFileName() . self::JPG_EXTENSION);
    }

    private function returnResponse($header, $message = '')
    {
        header($header);
        die($message);
    }

    private function availableInCache()
    {
        return $this->fileAdapter->fileExists(
            YoutubeThumbnailer::THUMBNAILS_DIRECTORY . $this->getFileName() . self::JPG_EXTENSION
        );
    }

    private function youtubeVideoExists($videoId)
    {
        $handle = $this->networkAdapter->curlInit('https://www.youtube.com/watch/?v=' . $videoId);
        $this->networkAdapter->curlSetOption($handle, CURLOPT_RETURNTRANSFER, TRUE);
        $response = $this->networkAdapter->curlExec($handle);
        $httpCode = $this->networkAdapter->curlGetInfo($handle, CURLINFO_HTTP_CODE);
        $this->networkAdapter->curlClose($handle);

        return $httpCode !== 404 && $response;
    }

    private function createImageFromYoutubeThumb()
    {
        return $this->imageAdapter->createImageFromJpgPath(
            'http://img.youtube.com/vi/' . $this->getVideoId() . '/' . $this->getQuality() . 'default' . self::JPG_EXTENSION);
    }

    private function convertImageToHighQuality($image)
    {
        $cleft = 0;
        $ctop = 45;
        $canvas = $this->imageAdapter->createTrueColorImage(480, 270);
        $this->imageAdapter->copyPartOfImage($canvas, $image, 0, 0, $cleft, $ctop, 480, 360);

        return $canvas;
    }
}