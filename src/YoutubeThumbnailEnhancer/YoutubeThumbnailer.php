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

    public function __construct()
    {
        $this->inputManager = new InputManager();
        $this->quality = self::DEFAULT_QUALITY;
        $this->play = self::DEFAULT_SHOW_PLAY;
        $this->refresh = self::DEFAULT_APPLY_REFRESH;
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
}