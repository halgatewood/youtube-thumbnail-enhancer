<?php

namespace YoutubeThumbnailEnhancer\Test;

use YoutubeThumbnailEnhancer\YoutubeThumbnailer;

require __DIR__ . '/../../../vendor/autoload.php';

class YoutubeThumbnailerTest extends \PHPUnit_Framework_TestCase
{
    /** @var YoutubeThumbnailer */
    private $youtubeThumbnailer;


    protected function setUp()
    {
        $this->youtubeThumbnailer = new YoutubeThumbnailer();
    }

    public function testYoutubeThumbnailerInstance()
    {
        $this->assertInstanceOf(YoutubeThumbnailer::class, $this->youtubeThumbnailer);
    }

    public function testCustomRequestParams()
    {
        $params = [
            'quality' => 'hq',
            'inpt' => 'http://www.youtube.com/watch?v=XZ4X1wcZ1GE',
            'play' => '',
            'refresh' => ''
        ];
        $this->youtubeThumbnailer->setRequestParams($params);

        $this->assertEquals('hq', $this->youtubeThumbnailer->getQuality());
        $this->assertEquals('http://www.youtube.com/watch?v=XZ4X1wcZ1GE', $this->youtubeThumbnailer->getInput());
        $this->assertTrue($this->youtubeThumbnailer->getPlay());
        $this->assertTrue($this->youtubeThumbnailer->getRefresh());
    }

    public function testDefaultRequestParams()
    {
        $params = [
            'inpt' => 'http://www.youtube.com/watch?v=XZ4X1wcZ1GE'
        ];
        $this->youtubeThumbnailer->setRequestParams($params);

        $this->assertEquals('mq', $this->youtubeThumbnailer->getQuality());
        $this->assertEquals('http://www.youtube.com/watch?v=XZ4X1wcZ1GE', $this->youtubeThumbnailer->getInput());
        $this->assertFalse($this->youtubeThumbnailer->getPlay());
        $this->assertFalse($this->youtubeThumbnailer->getRefresh());
    }

    public function testGetYoutubeIdFromInput()
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
