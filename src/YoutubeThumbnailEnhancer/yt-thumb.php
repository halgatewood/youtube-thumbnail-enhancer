<?php

namespace YoutubeThumbnailEnhancer;

require __DIR__ . '/../../vendor/autoload.php';

$youtbeThumbnailer = new YoutubeThumbnailer();
$youtbeThumbnailer->setRequestParams($_REQUEST);

$isUrl = $youtbeThumbnailer->inputIsUrl();
$quality = $youtbeThumbnailer->getQuality();
$inpt = $youtbeThumbnailer->getInput();
$showPlayIcon = $youtbeThumbnailer->getPlay();
$playButtonFIleName = ($showPlayIcon) ? "-play" : "";


// ADD HTTP
if(substr($inpt, 0, 4) == "www."){ $inpt = "http://" . $inpt;}
if(substr($inpt, 0, 8) == "youtube."){ $inpt = "http://" . $inpt;}
if(substr($inpt, 0, 8) == "youtu.be"){ $inpt = "http://" . $inpt;}

// IF URL GET ID
if(substr($inpt, 0, 7) == "http://" OR substr($inpt, 0, 8) == "https://")
{
	$id = $youtbeThumbnailer->getVideoId($inpt);
}


// IF NOT URL TRY ID AS INPUT
if(!$isUrl) { $id = $inpt; }


// FILENAME
$filename = ($quality == YoutubeThumbnailer::MEDIUM_QUALITY) ? $id . "-mq": $id;
$filename .= $playButtonFIleName;


// IF EXISTS, GO
if(file_exists("i/" . $filename . ".jpg") AND !$youtbeThumbnailer->getRefresh())
{
	header("Location: i/" . $filename . ".jpg");
	die;
}


// CHECK IF YOUTUBE VIDEO
$handle = curl_init("https://www.youtube.com/watch/?v=" . $id);
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


// IF NOT ID THROW AN ERROR
if(!$id) 
{
	header("Status: 404 Not Found");
	die("YouTube ID not found");
}


// CREATE IMAGE FROM YOUTUBE THUMB
$image = imagecreatefromjpeg( "http://img.youtube.com/vi/" . $id . "/" . $quality . "default.jpg" );


// IF HIGH QUALITY WE CREATE A NEW CANVAS WITHOUT THE BLACK BARS
if($quality == YoutubeThumbnailer::HIGH_QUALITY)
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
$play_icon = $showPlayIcon ? "play-" : "noplay-";
$play_icon .= $quality . ".png";
$logoImage = imagecreatefrompng( $play_icon );

imagealphablending($logoImage, true);

$logoWidth 		= imagesx($logoImage);
$logoHeight 	= imagesy($logoImage);

// CENTER PLAY ICON
$left = round($imageWidth / 2) - round($logoWidth / 2);
$top = round($imageHeight / 2) - round($logoHeight / 2);


// CONVERT TO PNG SO WE CAN GET THAT PLAY BUTTON ON THERE
imagecopy( $image, $logoImage, $left, $top, 0, 0, $logoWidth, $logoHeight);
imagepng( $image, $filename .".png", 9);


// MASHUP FINAL IMAGE AS A JPEG
$input = imagecreatefrompng($filename .".png");
$output = imagecreatetruecolor($imageWidth, $imageHeight);
$white = imagecolorallocate($output,  255, 255, 255);
imagefilledrectangle($output, 0, 0, $imageWidth, $imageHeight, $white);
imagecopy($output, $input, 0, 0, 0, 0, $imageWidth, $imageHeight);

// OUTPUT TO 'i' FOLDER
imagejpeg($output, "i/" . $filename . ".jpg", 95);

// UNLINK PNG VERSION
@unlink($filename .".png");

// REDIRECT TO NEW IMAGE
header("Location: i/" . $filename . ".jpg");
die;

?>
