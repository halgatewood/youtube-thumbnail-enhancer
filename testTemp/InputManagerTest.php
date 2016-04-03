<?php

use YoutubeThumbnailEnhancer\InputManager;

class InputManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var InputManager */
    private $inputManager;

    protected function setUp()
    {
        $this->inputManager = new InputManager();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(InputManager::class, $this->inputManager);
    }

    public function testIsUrl()
    {
        $this->assertFalse($this->inputManager->isUrl('XZ4X1wcZ1GE'));
        $this->assertTrue($this->inputManager->isUrl('www.youtube.com/watch?v=XZ4X1wcZ1GE'));
        $this->assertTrue($this->inputManager->isUrl('youtube.com/watch?v=XZ4X1wcZ1GE'));
        $this->assertTrue($this->inputManager->isUrl('youtu.be/XZ4X1wcZ1GE'));
        $this->assertTrue($this->inputManager->isUrl('http://www.youtube.com/watch?v=XZ4X1wcZ1GE'));
        $this->assertTrue($this->inputManager->isUrl('https://www.youtube.com/watch?v=XZ4X1wcZ1GE'));
    }

    public function testHasProtocol()
    {
        $this->assertFalse($this->inputManager->hasProtocol('XZ4X1wcZ1GE'));
        $this->assertFalse($this->inputManager->hasProtocol('www.youtube.com/watch?v=XZ4X1wcZ1GE'));
        $this->assertTrue($this->inputManager->hasProtocol('http://youtube.com/watch?v=XZ4X1wcZ1GE'));
        $this->assertTrue($this->inputManager->hasProtocol('https://youtu.be/XZ4X1wcZ1GE'));
    }

    public function testSanitizeUrl()
    {
        $this->assertEquals('XZ4X1wcZ1GE', $this->inputManager->sanitizeUrl(' XZ4X1wcZ1GE '));
        $this->assertEquals('XZ4X1wcZ1GE', $this->inputManager->sanitizeUrl('XZ4X1wcZ1GE'));
        $this->assertEquals(
            'http://www.youtube.com/watch?v=XZ4X1wcZ1GE',
            $this->inputManager->sanitizeUrl('http://www.youtube.com/watch?v=XZ4X1wcZ1GE')
        );
        $this->assertEquals(
            'https://www.youtube.com/watch?v=XZ4X1wcZ1GE',
            $this->inputManager->sanitizeUrl('https://www.youtube.com/watch?v=XZ4X1wcZ1GE')
        );
        $this->assertEquals(
            'http://www.youtube.com/watch?v=XZ4X1wcZ1GE',
            $this->inputManager->sanitizeUrl('www.youtube.com/watch?v=XZ4X1wcZ1GE')
        );
    }
}
