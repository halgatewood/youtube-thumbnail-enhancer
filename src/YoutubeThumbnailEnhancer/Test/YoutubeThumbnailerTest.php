<?php

namespace YoutubeThumbnailEnhancer\Test;

use YoutubeThumbnailEnhancer\YoutubeThumbnailer;

require __DIR__ . '/../../vendor/autoload.php';

class YoutubeThumbnailerTest extends \PHPUnit_Framework_TestCase
{
    private $youtubeThumbnailer;


    protected function setUp()
    {
        $this->youtubeThumbnailer = new YoutubeThumbnailer();
    }

    /** @test */
    public function should_be_instance_of_youtubethumbnailer()
    {
        $this->assertInstanceOf(YoutubeThumbnailer::class, $this->youtubeThumbnailer);
    }

    /** @test */
    public function should_returns_youtube_id()
    {
        $youtubeId = 'XZ4X1wcZ1GE';

        $this->assertEquals($youtubeId, $this->youtubeThumbnailer->getYouTubeIdFromInput('http://www.youtube.com/watch?v=XZ4X1wcZ1GE'));
        $this->assertEquals($youtubeId, $this->youtubeThumbnailer->getYouTubeIdFromInput('http://youtube.com/watch?v=XZ4X1wcZ1GE'));
        $this->assertEquals($youtubeId, $this->youtubeThumbnailer->getYouTubeIdFromInput('http://www.youtu.be/XZ4X1wcZ1GE'));
        $this->assertEquals($youtubeId, $this->youtubeThumbnailer->getYouTubeIdFromInput('http://youtu.be/XZ4X1wcZ1GE'));
        $this->assertEquals($youtubeId, $this->youtubeThumbnailer->getYouTubeIdFromInput('https://www.youtube.com/watch?v=XZ4X1wcZ1GE'));
        $this->assertEquals($youtubeId, $this->youtubeThumbnailer->getYouTubeIdFromInput('https://youtube.com/watch?v=XZ4X1wcZ1GE'));
        $this->assertEquals($youtubeId, $this->youtubeThumbnailer->getYouTubeIdFromInput('https://www.youtu.be/XZ4X1wcZ1GE'));
        $this->assertEquals($youtubeId, $this->youtubeThumbnailer->getYouTubeIdFromInput('https://youtu.be/XZ4X1wcZ1GE'));
    }
}
