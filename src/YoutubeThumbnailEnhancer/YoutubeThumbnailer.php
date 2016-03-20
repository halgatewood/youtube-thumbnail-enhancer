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

    private $quality;
    private $input;
    private $play;
    private $refresh;
    /** @var InputManager */
    private $inputManager;
    /** @var ImageAdapter */
    private $imageAdapter;

    public function __construct()
    {
        $this->quality = self::DEFAULT_QUALITY;
        $this->play = self::DEFAULT_SHOW_PLAY;
        $this->refresh = self::DEFAULT_APPLY_REFRESH;
        $this->inputManager = new InputManager();
        $this->imageAdapter = new ImageAdapter();
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
            header('Status: 404 Not Found');
            die('YouTube ID not found');
        }


        if (file_exists(YoutubeThumbnailer::THUMBNAILS_DIRECTORY . $this->getFileName() . self::JPG_EXTENSION)
            && !$this->getRefresh()
        ) {
            header('Location: ' . YoutubeThumbnailer::THUMBNAILS_DIRECTORY . $this->getFileName() . self::JPG_EXTENSION);
            die;
        }


        // CHECK IF YOUTUBE VIDEO
        $handle = curl_init("https://www.youtube.com/watch/?v=" . $this->getVideoId());
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($handle);


        // CHECK FOR 404 OR NO RESPONSE
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        if ($httpCode == 404 OR !$response) {
            header("Status: 404 Not Found");
            die("No YouTube video found or YouTube timed out. Try again soon.");
        }

        curl_close($handle);


        // CREATE IMAGE FROM YOUTUBE THUMB
        $image = $this->imageAdapter->createImageFromJpgPath("http://img.youtube.com/vi/" . $this->getVideoId() . "/" . $this->getQuality() . "default" . self::JPG_EXTENSION);


        // IF HIGH QUALITY WE CREATE A NEW CANVAS WITHOUT THE BLACK BARS
        if ($this->getQuality() == self::HIGH_QUALITY) {
            $cleft = 0;
            $ctop = 45;
            $canvas = $this->imageAdapter->createTrueColorImage(480, 270);
            $this->imageAdapter->copyPartOfImage($canvas, $image, 0, 0, $cleft, $ctop, 480, 360);
            $image = $canvas;
        }


        $imageWidth = $this->imageAdapter->getImageWidth($image);
        $imageHeight = $this->imageAdapter->getImageHeight($image);


        // ADD THE PLAY ICON
        $play_icon = $this->getPlay() ? "play-" : "noplay-";
        $play_icon .= $this->getQuality() . self::PNG_EXTENSION;
        $logoImage = $this->imageAdapter->createImageFromPngPath($play_icon);

        $this->imageAdapter->setBlendingMode($logoImage, true);

        $logoWidth = $this->imageAdapter->getImageWidth($logoImage);
        $logoHeight = $this->imageAdapter->getImageHeight($logoImage);

        // CENTER PLAY ICON
        $left = round($imageWidth / 2) - round($logoWidth / 2);
        $top = round($imageHeight / 2) - round($logoHeight / 2);


        // CONVERT TO PNG SO WE CAN GET THAT PLAY BUTTON ON THERE
        $this->imageAdapter->copyPartOfImage($image, $logoImage, $left, $top, 0, 0, $logoWidth, $logoHeight);
        $this->imageAdapter->imagePng($image, $this->getFileName() . self::PNG_EXTENSION, 9);


        // MASHUP FINAL IMAGE AS A JPEG
        $input = $this->imageAdapter->createImageFromPngPath($this->getFileName() . self::PNG_EXTENSION);
        $output = $this->imageAdapter->createTrueColorImage($imageWidth, $imageHeight);
        $white = $this->imageAdapter->imageColorAllocate($output, 255, 255, 255);
        $this->imageAdapter->imageFilledRectangle($output, 0, 0, $imageWidth, $imageHeight, $white);
        $this->imageAdapter->copyPartOfImage($output, $input, 0, 0, 0, 0, $imageWidth, $imageHeight);

        // OUTPUT TO 'i' FOLDER
        $this->imageAdapter->imageJpeg($output, self::THUMBNAILS_DIRECTORY . $this->getFileName() . self::JPG_EXTENSION, 95);

        // UNLINK PNG VERSION
        @unlink($this->getFileName() . self::PNG_EXTENSION);

        // REDIRECT TO NEW IMAGE
        header('Location: ' . self::THUMBNAILS_DIRECTORY . $this->getFileName() . self::JPG_EXTENSION);
        die;
    }
}