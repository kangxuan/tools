<?php

namespace Shanla\Tools\Tests;

use PHPUnit\Framework\TestCase;
use Shanla\Tools\ImageTool;
use Shanla\Tools\FileTool;

class ImageToolTest extends TestCase
{
    private string $testDir;
    private string $testImage;
    private string $testWatermark;

    protected function setUp(): void
    {
        $this->testDir = __DIR__ . '/test_image_dir';
        $this->testImage = $this->testDir . '/test.jpg';
        $this->testWatermark = $this->testDir . '/watermark.png';
        
        // 确保测试目录存在
        if (!FileTool::exists($this->testDir)) {
            FileTool::createDir($this->testDir);
        }
        
        // 创建测试图片（使用 GD 生成一个简单的图片）
        $img = imagecreatetruecolor(100, 100);
        $bg = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $bg);
        imagejpeg($img, $this->testImage);
        imagedestroy($img);
        
        // 创建测试水印图片
        $watermark = imagecreatetruecolor(50, 50);
        $bg = imagecolorallocate($watermark, 0, 0, 0);
        imagefill($watermark, 0, 0, $bg);
        imagepng($watermark, $this->testWatermark);
        imagedestroy($watermark);
    }

    protected function tearDown(): void
    {
        // 清理测试文件和目录
        if (FileTool::exists($this->testDir)) {
            FileTool::deleteDir($this->testDir);
        }
    }

    public function testResize()
    {
        $outputPath = $this->testDir . '/resized.jpg';
        ImageTool::resize($this->testImage, $outputPath, 50, 50);
        $this->assertTrue(file_exists($outputPath));
        list($width, $height) = getimagesize($outputPath);
        $this->assertEquals(50, $width);
        $this->assertEquals(50, $height);
    }

    public function testCrop()
    {
        $outputPath = $this->testDir . '/cropped.jpg';
        ImageTool::crop($this->testImage, $outputPath, 0, 0, 50, 50);
        $this->assertTrue(file_exists($outputPath));
        list($width, $height) = getimagesize($outputPath);
        $this->assertEquals(50, $width);
        $this->assertEquals(50, $height);
    }

    public function testRotate()
    {
        $outputPath = $this->testDir . '/rotated.jpg';
        ImageTool::rotate($this->testImage, $outputPath, 90);
        $this->assertTrue(file_exists($outputPath));
        list($width, $height) = getimagesize($outputPath);
        $this->assertEquals(100, $width);
        $this->assertEquals(100, $height);
    }

    public function testAddWatermark()
    {
        $outputPath = $this->testDir . '/watermarked.jpg';
        // 确保水印图片存在且可读
        $this->assertTrue(file_exists($this->testWatermark));
        ImageTool::addWatermark($this->testImage, $outputPath, $this->testWatermark, 'bottom-right');
        $this->assertTrue(file_exists($outputPath));
        list($width, $height) = getimagesize($outputPath);
        $this->assertEquals(100, $width);
        $this->assertEquals(100, $height);
    }

    public function testGetImageSize()
    {
        $size = ImageTool::getImageSize($this->testImage);
        $this->assertIsArray($size);
        $this->assertEquals(100, $size[0]);
        $this->assertEquals(100, $size[1]);
    }

    public function testMergeAvatars()
    {
        // 使用本地图片路径模拟 URL，避免 FileTool::downloadFileToLocal 失败
        $image1 = 'http://gips3.baidu.com/it/u=1821127123,1149655687&fm=3028&app=3028&f=JPEG&fmt=auto?w=720&h=1280';
        $image2 = 'http://gips3.baidu.com/it/u=1821127123,1149655687&fm=3028&app=3028&f=JPEG&fmt=auto?w=720&h=1280';
        $imageUrls = [$image1, $image2];
        $outputPath = ImageTool::mergeAvatars($imageUrls, 300, 300, 2, 10);
        $this->assertTrue(file_exists($outputPath));
        list($width, $height) = getimagesize($outputPath);
        $this->assertEquals(300, $width);
        $this->assertEquals(300, $height);
    }

    public function testImageCreateFromAny()
    {
        $image = ImageTool::imageCreateFromAny($this->testImage);
        $this->assertNotFalse($image);
        imagedestroy($image);
    }

    public function testImageCreateFromAnyWithInvalidFormat()
    {
        $invalidImage = $this->testDir . '/invalid.txt';
        file_put_contents($invalidImage, 'not an image');
        // 使用 getimagesize 检查文件是否为有效图片，避免 getImageSize 返回 false
        $this->assertFalse(getimagesize($invalidImage));
        $this->assertFalse(ImageTool::imageCreateFromAny($invalidImage));
    }
} 