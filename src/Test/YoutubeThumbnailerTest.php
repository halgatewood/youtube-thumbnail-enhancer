<?php

namespace Test;

use YoutubeThumbnailer;

class YoutubeThumbnailerTest extends \PHPUnit_Framework_TestCase
{
    private $youtubeThumbnailer;


    protected function setUp()
    {
        $this->youtubeThumbnailer = new YoutubeThumbnailer();
    }

    /** @test */
    public function should_be_instance_of_youtube_thumbnailer()
    {
        $this->assertInstanceOf('YoutubeThumbnailer', $this->youtubeThumbnailer);
    }
}
