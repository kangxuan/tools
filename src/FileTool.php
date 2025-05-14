<?php
declare(strict_types = 1);

namespace Shanla\Tools;


class FileTool
{
    /**
     * 判断文件是否存在
     * @param string $fileName
     * @return bool
     */
    public static function exists(string $fileName) : bool
    {
        return file_exists($fileName);
    }

    /**
     * 递归创建目录
     * @param string $dirPath
     * @param int $permissions
     * @return bool
     */
    public static function createDir(string $dirPath, int $permissions = 0775) : bool
    {
        return mkdir($dirPath, $permissions, true);
    }

    /**
     * 递归删除目录
     * @param string $dirPath
     * @return bool
     */
    public static function deleteDir(string $dirPath) : bool
    {
        $files = array_diff(scandir($dirPath), ['.', '..']);
        foreach ($files as $file) {
            $filePath = $dirPath . DIRECTORY_SEPARATOR . $file;
            if (is_dir($filePath)) {
                // 如果是文件夹则继续删除
                self::deleteDir($filePath);
            } else {
                unlink($filePath);
            }
        }
        return rmdir($dirPath);
    }

    /**
     * 删除文件或目录
     * @param string $path
     * @return bool
     */
    public static function delete(string $path) : bool
    {
        if (is_file($path)) {
            return unlink($path);
        }
        if (is_dir($path)) {
            return self::deleteDir($path);
        }

        return false;
    }

    /**
     * 获取文件内容
     * @param string $fileName
     * @return bool|string
     */
    public static function readFile(string $fileName) : bool|string
    {
        if (self::exists($fileName)) {
            return file_get_contents($fileName);
        }

        return false;
    }

    /**
     * 写文件
     * @param string $fileName
     * @param string $content
     * @param bool $append
     * @return bool|int
     */
    public static function writeFile(string $fileName, string $content, bool $append = false) : bool|int
    {
        $flags = $append ? FILE_APPEND : 0;
        return file_put_contents($fileName, $content, $flags);
    }

    /**
     * 获取文件大小（字节）
     * @param string $fileName
     * @return int|bool
     */
    public static function getFileSize(string $fileName) : bool|int
    {
        if (self::exists($fileName)) {
            return filesize($fileName);
        }
        return false;
    }

    /**
     * 获取文件类型
     * @param string $fileName
     * @return bool|string
     */
    public static function getFileMimeType(string $fileName) : bool|string
    {
        if (self::exists($fileName)) {
            return mime_content_type($fileName);
        }
        return false;
    }

    /**
     * 获取文件扩展名
     * @param string $fileName
     * @return string|bool
     */
    public static function getFileExtension(string $fileName) : bool|string
    {
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        return $extension === '' ? false : $extension;
    }

    /**
     * 移动文件（用于测试）
     * @param string $source
     * @param string $destination
     * @return bool
     */
    public static function moveFile(string $source, string $destination) : bool
    {
        return rename($source, $destination);
    }

    /**
     * 上传文件
     * @param array $file $_FILES
     * @param string $destination 目标路径
     * @param int $maxSize 最大文件大小（字节）
     * @param array $allowedTypes 允许的文件类型（mime类型）
     * @return array
     */
    public static function uploadFile(array $file, string $destination, int $maxSize = 10485760, array $allowedTypes = []) : array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => 0, 'msg' => '上传错误: ' . self::uploadError($file['error'])];
        }

        // 检查文件大小
        if ($file['size'] > $maxSize) {
            return ['success' => 0, 'msg' => '文件过大'];
        }

        // 检查文件类型
        if (!empty($allowedTypes) && !in_array($file['type'], $allowedTypes)) {
            return ['success' => 0, 'msg' => '不允许的文件类型'];
        }

        // 创建目标目录
        $directory = dirname($destination);
        if (!self::exists($directory)) {
            self::createDir($directory);
        }

        // 移动文件到目标路径
        if (is_uploaded_file($file['tmp_name'])) {
            $result = move_uploaded_file($file['tmp_name'], $destination);
        } else {
            $result = self::moveFile($file['tmp_name'], $destination);
        }

        if ($result) {
            return ['success' => 1, 'path' => $destination, 'msg' => 'success'];
        }

        return ['success' => 0, 'msg' => '文件上传失败'];
    }

    /**
     * 远程文件下载到本地（适用于小文件）
     * @param string $fileUrl
     * @param string $savePath
     * @param int $timeout
     * @return bool
     */
    public static function downloadFileToLocal(string $fileUrl, string $savePath, int $timeout = 30) : bool
    {
        $contextOptions = [
            'http' => [
                'timeout' => $timeout,
            ]
        ];
        $context = stream_context_create($contextOptions);

        // 获取远程文件内容
        $fileData = file_get_contents($fileUrl, false, $context);
        // 下载失败
        if ($fileData === false) {
            return false;
        }
        // 写入到本地文件
        $result = file_put_contents($savePath, $fileData);
        return $result !== false;
    }

    /**
     * 通过curl方式远程文件下载到本地（适用于大文件）
     * @param string $fileUrl
     * @param string $savePath
     * @param int $timeout
     * @return bool
     */
    public static function downloadFileToLocalWithCurl(string $fileUrl, string $savePath, int $timeout = 30) : bool
    {
        $ch = curl_init($fileUrl);

        // 设置 cURL 选项
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 将结果返回而不是直接输出
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 跟随重定向
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); // 设置超时时间
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // 忽略 SSL 验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 忽略 SSL 验证

        // 打开本地文件以写入
        $fp = fopen($savePath, 'w');
        if ($fp === false) {
            curl_close($ch);
            return false;
        }

        // 将结果写入文件
        curl_setopt($ch, CURLOPT_FILE, $fp);

        // 执行会话
        $result = curl_exec($ch);
        // 关闭文件和curl会话
        fclose($fp);
        curl_close($ch);

        if ($result === false) {
            return false;
        }
        return true;
    }

    /**
     * 获取文件上传错误信息
     * @param int $errorCode
     * @return string
     */
    public static function uploadError(int $errorCode) : string
    {
        return match ($errorCode) {
            UPLOAD_ERR_OK => '没有错误',
            UPLOAD_ERR_INI_SIZE => '文件超过了 php.ini 中 upload_max_filesize 选项限制的大小',
            UPLOAD_ERR_FORM_SIZE => '文件超过了 HTML 表单中 MAX_FILE_SIZE 选项限制的大小',
            UPLOAD_ERR_PARTIAL => '文件只有部分被上传',
            UPLOAD_ERR_NO_FILE => '没有文件被上传',
            UPLOAD_ERR_NO_TMP_DIR => '缺少临时文件夹',
            UPLOAD_ERR_CANT_WRITE => '文件写入失败',
            UPLOAD_ERR_EXTENSION => '上传文件被 PHP 扩展停止',
            default => '未知错误',
        };
    }
}