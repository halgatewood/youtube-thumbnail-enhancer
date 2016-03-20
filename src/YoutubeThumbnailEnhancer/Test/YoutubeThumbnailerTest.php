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
            'quality' => YoutubeThumbnailer::HIGH_QUALITY,
            'inpt' => 'www.youtube.com/watch?v=XZ4X1wcZ1GE',
            'play' => '',
            'refresh' => ''
        ];
        $this->youtubeThumbnailer->setRequestParams($params);

        $this->assertEquals(YoutubeThumbnailer::HIGH_QUALITY, $this->youtubeThumbnailer->getQuality());
        $this->assertEquals('http://www.youtube.com/watch?v=XZ4X1wcZ1GE', $this->youtubeThumbnailer->getInput());
        $this->assertEquals(YoutubeThumbnailer::SHOW_PLAY, $this->youtubeThumbnailer->getPlay());
        $this->assertEquals(YoutubeThumbnailer::APPLY_REFRESH, $this->youtubeThumbnailer->getRefresh());
    }

    public function testDefaultRequestParams()
    {
        $params = ['inpt' => 'XZ4X1wcZ1GE'];
        $this->youtubeThumbnailer->setRequestParams($params);

        $this->assertEquals(YoutubeThumbnailer::DEFAULT_QUALITY, $this->youtubeThumbnailer->getQuality());
        $this->assertEquals('XZ4X1wcZ1GE', $this->youtubeThumbnailer->getInput());
        $this->assertEquals(YoutubeThumbnailer::DEFAULT_SHOW_PLAY, $this->youtubeThumbnailer->getPlay());
        $this->assertEquals(YoutubeThumbnailer::DEFAULT_APPLY_REFRESH, $this->youtubeThumbnailer->getRefresh());
    }

    public function testGetVideoId()
    {
        $youtubeId = 'XZ4X1wcZ1GE';

        $params = ['inpt' => 'XZ4X1wcZ1GE'];
        $this->youtubeThumbnailer->setRequestParams($params);
        $this->assertEquals($youtubeId, $this->youtubeThumbnailer->getVideoId());

        $params = ['inpt' => 'http://www.youtube.com/watch?v=XZ4X1wcZ1GE'];
        $this->youtubeThumbnailer->setRequestParams($params);
        $this->assertEquals($youtubeId, $this->youtubeThumbnailer->getVideoId());

        $params = ['inpt' => 'http://youtube.com/watch?v=XZ4X1wcZ1GE'];
        $this->youtubeThumbnailer->setRequestParams($params);
        $this->assertEquals($youtubeId, $this->youtubeThumbnailer->getVideoId());

        $params = ['inpt' => 'http://www.youtu.be/XZ4X1wcZ1GE'];
        $this->youtubeThumbnailer->setRequestParams($params);
        $this->assertEquals($youtubeId, $this->youtubeThumbnailer->getVideoId());

        $params = ['inpt' => 'http://youtu.be/XZ4X1wcZ1GE'];
        $this->youtubeThumbnailer->setRequestParams($params);
        $this->assertEquals($youtubeId, $this->youtubeThumbnailer->getVideoId());

        $params = ['inpt' => 'https://www.youtube.com/watch?v=XZ4X1wcZ1GE'];
        $this->youtubeThumbnailer->setRequestParams($params);
        $this->assertEquals($youtubeId, $this->youtubeThumbnailer->getVideoId());

        $params = ['inpt' => 'https://youtube.com/watch?v=XZ4X1wcZ1GE'];
        $this->youtubeThumbnailer->setRequestParams($params);
        $this->assertEquals($youtubeId, $this->youtubeThumbnailer->getVideoId());

        $params = ['inpt' => 'https://www.youtu.be/XZ4X1wcZ1GE'];
        $this->youtubeThumbnailer->setRequestParams($params);
        $this->assertEquals($youtubeId, $this->youtubeThumbnailer->getVideoId());

        $params = ['inpt' => 'https://youtu.be/XZ4X1wcZ1GE'];
        $this->youtubeThumbnailer->setRequestParams($params);
        $this->assertEquals($youtubeId, $this->youtubeThumbnailer->getVideoId());
    }
}
