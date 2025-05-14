<?php

namespace Shanla\Tools\Tests;

use PHPUnit\Framework\TestCase;
use Shanla\Tools\FileTool;

class FileToolTest extends TestCase
{
    private string $testDir;
    private string $testFile;

    protected function setUp(): void
    {
        $this->testDir = __DIR__ . '/test_dir';
        $this->testFile = $this->testDir . '/test.txt';
        
        // 确保测试目录不存在
        if (FileTool::exists($this->testDir)) {
            FileTool::deleteDir($this->testDir);
        }
    }

    protected function tearDown(): void
    {
        // 清理测试文件和目录
        if (FileTool::exists($this->testDir)) {
            FileTool::deleteDir($this->testDir);
        }
    }

    public function testExists()
    {
        $this->assertFalse(FileTool::exists($this->testFile));
        FileTool::createDir($this->testDir);
        FileTool::writeFile($this->testFile, 'test content');
        $this->assertTrue(FileTool::exists($this->testFile));
    }

    public function testCreateDir()
    {
        $this->assertTrue(FileTool::createDir($this->testDir));
        $this->assertTrue(FileTool::exists($this->testDir));
    }

    public function testDeleteDir()
    {
        FileTool::createDir($this->testDir);
        FileTool::writeFile($this->testFile, 'test content');
        $this->assertTrue(FileTool::deleteDir($this->testDir));
        $this->assertFalse(FileTool::exists($this->testDir));
    }

    public function testDelete()
    {
        // 测试删除文件
        FileTool::createDir($this->testDir);
        FileTool::writeFile($this->testFile, 'test content');
        $this->assertTrue(FileTool::delete($this->testFile));
        $this->assertFalse(FileTool::exists($this->testFile));

        // 测试删除目录
        FileTool::createDir($this->testDir . '/subdir');
        $this->assertTrue(FileTool::delete($this->testDir));
        $this->assertFalse(FileTool::exists($this->testDir));
    }

    public function testReadFile()
    {
        $content = 'test content';
        FileTool::createDir($this->testDir);
        FileTool::writeFile($this->testFile, $content);
        $this->assertEquals($content, FileTool::readFile($this->testFile));
        $this->assertFalse(FileTool::readFile('non_existent_file.txt'));
    }

    public function testWriteFile()
    {
        $content = 'test content';
        FileTool::createDir($this->testDir);
        $this->assertNotFalse(FileTool::writeFile($this->testFile, $content));
        $this->assertEquals($content, FileTool::readFile($this->testFile));

        // 测试追加模式
        $additionalContent = ' additional content';
        FileTool::writeFile($this->testFile, $additionalContent, true);
        $this->assertEquals($content . $additionalContent, FileTool::readFile($this->testFile));
    }

    public function testGetFileSize()
    {
        $content = 'test content';
        FileTool::createDir($this->testDir);
        FileTool::writeFile($this->testFile, $content);
        $this->assertEquals(strlen($content), FileTool::getFileSize($this->testFile));
        $this->assertFalse(FileTool::getFileSize('non_existent_file.txt'));
    }

    public function testGetFileMimeType()
    {
        $content = 'test content';
        FileTool::createDir($this->testDir);
        FileTool::writeFile($this->testFile, $content);
        $this->assertEquals('text/plain', FileTool::getFileMimeType($this->testFile));
        $this->assertFalse(FileTool::getFileMimeType('non_existent_file.txt'));
    }

    public function testGetFileExtension()
    {
        $this->assertEquals('txt', FileTool::getFileExtension('test.txt'));
        $this->assertEquals('php', FileTool::getFileExtension('test.php'));
        $this->assertFalse(FileTool::getFileExtension('test'));
    }

    public function testUploadFile()
    {
        // 创建临时文件
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        if ($tempFile === false) {
            $this->fail('无法创建临时文件');
        }

        try {
            // 确保临时文件可写
            if (!is_writable($tempFile)) {
                $this->fail('临时文件不可写');
            }

            $content = 'test content';
            if (file_put_contents($tempFile, $content) === false) {
                $this->fail('无法写入临时文件');
            }

            // 确保文件权限正确
            if (!chmod($tempFile, 0644)) {
                $this->fail('无法设置文件权限');
            }

            $testFile = [
                'name' => 'test.txt',
                'type' => 'text/plain',
                'tmp_name' => $tempFile,
                'error' => UPLOAD_ERR_OK,
                'size' => filesize($tempFile)
            ];

            $destination = $this->testDir . '/uploaded.txt';
            
            // 确保测试目录可写
            if (!FileTool::createDir($this->testDir)) {
                $this->fail('无法创建测试目录');
            }

            // 测试正常上传
            $result = FileTool::uploadFile(
                $testFile,
                $destination,
                2048,
                ['text/plain']
            );

            $this->assertEquals(1, $result['success']);
            $this->assertEquals('success', $result['msg']);
            $this->assertTrue(file_exists($destination));

            // 测试文件过大
            $testFile['size'] = 4096;
            $result = FileTool::uploadFile($testFile, $destination, 2048);
            $this->assertEquals(0, $result['success']);
            $this->assertEquals('文件过大', $result['msg']);

            // 测试不允许的文件类型
            $testFile['size'] = filesize($tempFile); // 重置文件大小为正常值
            $testFile['type'] = 'application/pdf';
            $result = FileTool::uploadFile($testFile, $destination, 2048, ['text/plain']);
            $this->assertEquals(0, $result['success']);
            $this->assertEquals('不允许的文件类型', $result['msg']);
        } finally {
            // 确保临时文件被清理
            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }
            // 确保测试目录被清理
            if (FileTool::exists($this->testDir)) {
                FileTool::deleteDir($this->testDir);
            }
        }
    }

    public function testUploadError()
    {
        $this->assertEquals('没有错误', FileTool::uploadError(UPLOAD_ERR_OK));
        $this->assertEquals('文件超过了 php.ini 中 upload_max_filesize 选项限制的大小', FileTool::uploadError(UPLOAD_ERR_INI_SIZE));
        $this->assertEquals('文件超过了 HTML 表单中 MAX_FILE_SIZE 选项限制的大小', FileTool::uploadError(UPLOAD_ERR_FORM_SIZE));
        $this->assertEquals('文件只有部分被上传', FileTool::uploadError(UPLOAD_ERR_PARTIAL));
        $this->assertEquals('没有文件被上传', FileTool::uploadError(UPLOAD_ERR_NO_FILE));
        $this->assertEquals('缺少临时文件夹', FileTool::uploadError(UPLOAD_ERR_NO_TMP_DIR));
        $this->assertEquals('文件写入失败', FileTool::uploadError(UPLOAD_ERR_CANT_WRITE));
        $this->assertEquals('上传文件被 PHP 扩展停止', FileTool::uploadError(UPLOAD_ERR_EXTENSION));
    }
} 