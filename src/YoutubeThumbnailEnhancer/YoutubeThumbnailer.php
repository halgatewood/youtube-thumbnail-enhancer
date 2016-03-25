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
        $playIcon = $this->createPlayImage();
        $finalImage = $this->createFinalImage($image, $playIcon);
        $this->saveThumbnailToDisk($finalImage);
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

    private function createPlayImage()
    {
        $playIconPath = $this->getPlay() ? 'play-' : 'noplay-';
        $playIconPath .= $this->getQuality() . self::PNG_EXTENSION;
        $playImage = $this->imageAdapter->createImageFromPngPath(self::IMAGE_UTILS_DIR . $playIconPath);
        $this->imageAdapter->setBlendingMode($playImage, true);

        return $playImage;
    }

    private function createFinalImage($image, $playIcon)
    {
        $imageWidth = $this->imageAdapter->getImageWidth($image);
        $imageHeight = $this->imageAdapter->getImageHeight($image);

        $playIconWidth = $this->imageAdapter->getImageWidth($playIcon);
        $playIconHeight = $this->imageAdapter->getImageHeight($playIcon);
        $playIconLeft = round($imageWidth / 2) - round($playIconWidth / 2);
        $playIconTop = round($imageHeight / 2) - round($playIconHeight / 2);

        $this->imageAdapter->copyPartOfImage(
            $image,
            $playIcon,
            $playIconLeft,
            $playIconTop,
            0,
            0,
            $playIconWidth,
            $playIconHeight
        );

        return $image;
    }

    private function saveThumbnailToDisk($image)
    {
        $this->imageAdapter->imageJpeg(
            $image,
            self::THUMBNAILS_DIRECTORY . $this->getFileName() . self::JPG_EXTENSION,
            95
        );
    }
}