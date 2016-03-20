<?php

namespace YoutubeThumbnailEnhancer;

require __DIR__ . '/../../vendor/autoload.php';

$youtbeThumbnailer = new YoutubeThumbnailer();
$youtbeThumbnailer->setRequestParams($_REQUEST);

// IF NOT ID THROW AN ERROR
if(!($youtbeThumbnailer->getVideoId()))
{
    header("Status: 404 Not Found");
    die("YouTube ID not found");
}

// IF EXISTS, GO
if(file_exists(YoutubeThumbnailer::THUMBNAILS_DIRECTORY . $youtbeThumbnailer->getFileName() . ".jpg") AND !$youtbeThumbnailer->getRefresh())
{
	header('Location: '. YoutubeThumbnailer::THUMBNAILS_DIRECTORY . $youtbeThumbnailer->getFileName() . '.jpg');
	die;
}


// CHECK IF YOUTUBE VIDEO
$handle = curl_init("https://www.youtube.com/watch/?v=" . $youtbeThumbnailer->getVideoId());
curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
$response = curl_exec($handle);


// CHECK FOR 404 OR NO RESPONSE
$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
if($httpCode == 404 OR !$response) 
{
	header("Status: 404 Not Found");
	die("No YouTube video found or YouTube timed out. Try again soon."); 
}

curl_close($handle);


// CREATE IMAGE FROM YOUTUBE THUMB
$image = imagecreatefromjpeg( "http://img.youtube.com/vi/" . $youtbeThumbnailer->getVideoId() . "/" . $youtbeThumbnailer->getQuality() . "default.jpg" );


// IF HIGH QUALITY WE CREATE A NEW CANVAS WITHOUT THE BLACK BARS
if($youtbeThumbnailer->getQuality() == YoutubeThumbnailer::HIGH_QUALITY)
{
	$cleft = 0;
	$ctop = 45;
	$canvas = imagecreatetruecolor(480, 270);
	imagecopy($canvas, $image, 0, 0, $cleft, $ctop, 480, 360);
	$image = $canvas;
}


$imageWidth 	= imagesx($image);
$imageHeight 	= imagesy($image);



// ADD THE PLAY ICON
$play_icon = $youtbeThumbnailer->getPlay() ? "play-" : "noplay-";
$play_icon .= $youtbeThumbnailer->getQuality() . ".png";
$logoImage = imagecreatefrompng( $play_icon );

imagealphablending($logoImage, true);

$logoWidth 		= imagesx($logoImage);
$logoHeight 	= imagesy($logoImage);

// CENTER PLAY ICON
$left = round($imageWidth / 2) - round($logoWidth / 2);
$top = round($imageHeight / 2) - round($logoHeight / 2);


// CONVERT TO PNG SO WE CAN GET THAT PLAY BUTTON ON THERE
imagecopy( $image, $logoImage, $left, $top, 0, 0, $logoWidth, $logoHeight);
imagepng( $image, $youtbeThumbnailer->getFileName() .".png", 9);


// MASHUP FINAL IMAGE AS A JPEG
$input = imagecreatefrompng($youtbeThumbnailer->getFileName() .".png");
$output = imagecreatetruecolor($imageWidth, $imageHeight);
$white = imagecolorallocate($output,  255, 255, 255);
imagefilledrectangle($output, 0, 0, $imageWidth, $imageHeight, $white);
imagecopy($output, $input, 0, 0, 0, 0, $imageWidth, $imageHeight);

// OUTPUT TO 'i' FOLDER
imagejpeg($output, YoutubeThumbnailer::THUMBNAILS_DIRECTORY . $youtbeThumbnailer->getFileName() . ".jpg", 95);

// UNLINK PNG VERSION
@unlink($youtbeThumbnailer->getFileName() .".png");

// REDIRECT TO NEW IMAGE
header('Location: '. YoutubeThumbnailer::THUMBNAILS_DIRECTORY . $youtbeThumbnailer->getFileName() . '.jpg');
die;
