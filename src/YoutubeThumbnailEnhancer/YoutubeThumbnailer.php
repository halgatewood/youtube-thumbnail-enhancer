<?php

namespace YoutubeThumbnailEnhancer;

require __DIR__ . '/../../vendor/autoload.php';

class YoutubeThumbnailer
{
    const DEFAULT_QUALITY = 'mq';
    const HIGH_QUALITY = 'hq';
    const MEDIUM_QUALITY = 'mq';
    const DEFAULT_SHOW_PLAY = false;
    const SHOW_PLAY = true;
    const NOT_SHOW_PLAY = false;
    const DEFAULT_APPLY_REFRESH = false;
    const APPLY_REFRESH = true;
    const NOT_APPLY_REFRESH = false;

    private $quality;
    private $input;
    private $play;
    private $refresh;

    public function __construct()
    {
        $this->quality = self::DEFAULT_QUALITY;
        $this->play = self::DEFAULT_SHOW_PLAY;
        $this->refresh = self::DEFAULT_APPLY_REFRESH;
    }

    public function setRequestParams($requestParams)
    {
        if (array_key_exists('quality', $requestParams)) {
            $this->quality = $requestParams['quality'];
        }

        if (array_key_exists('inpt', $requestParams)) {
            $this->input = trim($requestParams['inpt']);
        }

        if (array_key_exists('play', $requestParams)) {
            $this->play = true;
        }

        if (array_key_exists('refresh', $requestParams)) {
            $this->refresh = true;
        }
    }

    public function getYouTubeIdFromInput($input)
    {
        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i';
        preg_match($pattern, $input, $matches);

        return isset($matches[1]) ? $matches[1] : false;
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
}