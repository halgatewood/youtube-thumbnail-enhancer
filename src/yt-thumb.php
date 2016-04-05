<?php

require_once __DIR__.'/../vendor/autoload.php';

use YoutubeThumbnailEnhancer\YoutubeThumbnailer;

$youtbeThumbnailer = new YoutubeThumbnailer();
$youtbeThumbnailer->setRequestParams($_REQUEST);
$youtbeThumbnailer->generateThumbnail();
