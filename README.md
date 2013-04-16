YouTube Thumbnail Enchancer
==========================

Easily create youtube thumbnails with play buttons watermarked on the top.

License: The just-use-it license. Have fun!

Setup:
*   Add this script to your server
*   Create folder named 'i (this is where the youtube videos are stored)


Dependances:
*   curl
*   GD Library
*   coffee

Parameters:
*   inpt = YouTube URL or YouTube ID
*   quality = hq or mq
*   refresh = skips the cache to grab a fresh one
*   play = show play button in middle

Usage:

http://example.com/yt-thumb.php?quality=hq&inpt=http://www.youtube.com/watch?v=XZ4X1wcZ1GE

http://example.com/yt-thumb.php?quality=mq&inpt=http://www.youtube.com/watch?v=XZ4X1wcZ1GE

http://example.com/yt-thumb.php?quality=hq&inpt=XZ4X1wcZ1GE

http://example.com/yt-thumb.php?quality=mq&inpt=XZ4X1wcZ1GE

http://example.com/yt-thumb.php?quality=hq&inpt=XZ4X1wcZ1GE&play

http://example.com/yt-thumb.php?quality=hq&inpt=XZ4X1wcZ1GE&play&refresh

