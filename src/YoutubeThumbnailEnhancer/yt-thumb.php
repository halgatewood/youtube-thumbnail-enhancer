<?php

namespace YoutubeThumbnailEnhancer;

require __DIR__ . '/../../vendor/autoload.php';

$youtbeThumbnailer = new YoutubeThumbnailer();
$youtbeThumbnailer->setRequestParams($_REQUEST);
$youtbeThumbnailer->generateThumbnail();
