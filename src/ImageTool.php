<?php
declare(strict_types = 1);

namespace Shanla\Tools;

use Exception;
use GdImage;

class ImageTool
{
    /**
     * 生成缩略图，按照指定的宽度和高度调整图片大小，保持宽高比
     * @param string $imagePath
     * @param string $outputPath
     * @param int $width
     * @param int $height
     * @throws Exception
     */
    public static function resize(string $imagePath, string $outputPath, int $width, int $height) : void
    {
        // 获取图片的原始宽度、高度和类型
        list($originalWidth, $originalHeight, $imageType) = getimagesize($imagePath);

        // 计算高宽比，保持图片的高宽比不变
        $aspectRatio = $originalWidth / $originalHeight;
        if ($width / $height > $aspectRatio) {
            // 如果目标宽高比大于原始宽高比，按高度计算宽度
            $width = $height * $aspectRatio;
        } else {
            // 如果目标宽高比小于原始宽高比，按宽度计算高度
            $height = $width / $aspectRatio;
        }

        // 创建图片资源，支持多种图片类型
        $sourceImage = self::createImageFromFile($imagePath, $imageType);
        $newImage = imagecreatetruecolor($width, $height);

        // 为透明图像启用透明度
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);

        // 调整图片大小
        imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);

        // 保存生成的缩略图
        self::saveImageToFile($newImage, $outputPath, $imageType);

        // 释放内存
        imagedestroy($sourceImage);
        imagedestroy($newImage);
    }

    /**
     * 裁剪图片，指定裁剪区域的起始位置和目标宽高
     * @param string $imagePath
     * @param string $outputPath
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     * @throws Exception
     */
    public static function crop(string $imagePath, string $outputPath, int $x, int $y, int $width, int $height) : void
    {
        // 获取图片的原始宽度、高度和类型
        list(, , $imageType) = getimagesize($imagePath);

        // 创建图片资源
        $sourceImage = self::createImageFromFile($imagePath, $imageType);
        $croppedImage = imagecreatetruecolor($width, $height);

        // 保持透明背景（对于PNG等有透明通道的图片）
        imagealphablending($croppedImage, false);
        imagesavealpha($croppedImage, true);

        // 执行裁剪操作
        imagecopy($croppedImage, $sourceImage, 0, 0, $x, $y, $width, $height);

        // 保存裁剪后的图片
        self::saveImageToFile($croppedImage, $outputPath, $imageType);

        // 释放内存
        imagedestroy($sourceImage);
        imagedestroy($croppedImage);
    }

    /**
     * 旋转图片，指定旋转角度
     * @param string $imagePath
     * @param string $outputPath
     * @param int $angle
     * @throws Exception
     */
    public static function rotate(string $imagePath, string $outputPath, int $angle) : void
    {
        // 获取图片的原始宽度、高度和类型（JPEG、PNG等）
        list(, , $imageType) = getimagesize($imagePath);

        // 创建图片资源
        $sourceImage = self::createImageFromFile($imagePath, $imageType);

        // 旋转图片
        $rotatedImage = imagerotate($sourceImage, $angle, 0);

        // 保存旋转后的图片
        self::saveImageToFile($rotatedImage, $outputPath, $imageType);

        // 释放内存
        imagedestroy($sourceImage);
        imagedestroy($rotatedImage);
    }

    /**
     * 给图片添加水印
     * @param string $imagePath
     * @param string $outputPath
     * @param string $watermarkPath
     * @param string $position
     * @throws Exception
     */
    public static function addWatermark(string $imagePath, string $outputPath, string $watermarkPath, string $position = 'bottom-right') : void
    {
        // 获取图片的尺寸和水印的尺寸
        list($originalWidth, $originalHeight, $imageType) = getimagesize($imagePath);
        list($watermarkWidth, $watermarkHeight, $waterImageType) = getimagesize($watermarkPath);

        // 创建图片和水印资源
        $sourceImage = self::createImageFromFile($imagePath, $imageType);
        $watermarkImage = self::createImageFromFile($watermarkPath, $waterImageType);

        // 根据位置确定水印的位置
        switch ($position) {
            case 'top-left':
                $x = 0;
                $y = 0;
                break;
            case 'top-right':
                $x = $originalWidth - $watermarkWidth;
                $y = 0;
                break;
            case 'bottom-left':
                $x = 0;
                $y = $originalHeight - $watermarkHeight;
                break;
            case 'bottom-right':
            default:
                $x = $originalWidth - $watermarkWidth;
                $y = $originalHeight - $watermarkHeight;
                break;
        }

        // 合并水印到原始图片上
        if ($watermarkImage === false || !($watermarkImage instanceof GdImage)) {
            throw new Exception('水印图片创建失败');
        }
        imagecopy($sourceImage, $watermarkImage, $x, $y, 0, 0, $watermarkWidth, $watermarkHeight);

        // 保存带水印的图片
        self::saveImageToFile($sourceImage, $outputPath, $imageType);

        // 释放内存
        imagedestroy($sourceImage);
        imagedestroy($watermarkImage);
    }

    /**
     * 获取图片的尺寸
     * @param string $imagePath
     * @return array
     */
    public static function getImageSize(string $imagePath) : array
    {
        // 获取图片尺寸（宽度和高度）
        $size = getimagesize($imagePath);
        if ($size === false) {
            throw new Exception('无法获取图片尺寸');
        }
        return $size;
    }

    /**
     * 将图片保存到指定路径
     * @param $image
     * @param string $outputPath
     * @param int $imageType
     * @throws Exception
     */
    private static function saveImageToFile($image, string $outputPath, int $imageType) : void
    {
        // 根据图片类型保存图片
        match ($imageType) {
            IMAGETYPE_GIF => imagegif($image, $outputPath),
            IMAGETYPE_JPEG => imagejpeg($image, $outputPath),
            IMAGETYPE_PNG => imagepng($image, $outputPath),
            IMAGETYPE_BMP => imagebmp($image, $outputPath),
            IMAGETYPE_WBMP => imagewbmp($image, $outputPath),
            IMAGETYPE_WEBP => imagewebp($image, $outputPath),
            default => throw new Exception('不支持的图片类型'),
        };
    }

    /**
     * 将图片保存到指定路径
     * @param string $imagePath
     * @param int $imageType
     * @return bool|GdImage
     * @throws Exception
     */
    private static function createImageFromFile(string $imagePath, int $imageType) : bool|GdImage
    {
        return match ($imageType) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($imagePath),
            IMAGETYPE_PNG => imagecreatefrompng($imagePath),
            IMAGETYPE_GIF => imagecreatefromgif($imagePath),
            default => throw new Exception('不支持的图片类型'),
        };
    }

    /**
     * 将多个头像合并为一张图
     * @param array $imageUrls 图片列表
     * @param int $finalWidth 合并图的宽
     * @param int $finalHeight 合并图的高
     * @param int $cols 每行图片多少张
     * @param int $margin 每张图片之间的间隙
     * @return string
     * @throws Exception
     */
    public static function mergeAvatars(array $imageUrls, int $finalWidth = 300, int $finalHeight = 300, int $cols = 3, int $margin = 10) : string
    {
        // 将图片下载到本地
        $localImages = [];
        foreach ($imageUrls as $imageUrl) {
            $imageExtension = FileTool::getFileExtension($imageUrl);
            $tmpDir = __DIR__ . '/tmp/' . date("Ymd");
            if (!FileTool::exists($tmpDir)) {
                FileTool::createDir($tmpDir);
            }
            $tmpSavePath = $tmpDir . '/' . uniqid() . md5($imageUrl) . $imageExtension;
            if (!FileTool::downloadFileToLocal($imageUrl, $tmpSavePath) || !file_exists($tmpSavePath)) {
                throw new Exception('图片下载失败.');
            }
            $localImages[] = $tmpSavePath;
        }
        if (empty($localImages) || count($imageUrls) != count($localImages)) {
            throw new Exception('图片下载失败');
        }

        // 计算行数
        $rows = ceil(count($localImages) / $cols);

        // 计算每张图的宽度和高度
        $imageWidth = intval(($finalWidth - ($cols - 1) * $margin) / $cols);  // 每张图的宽度
        $imageHeight = intval(($finalHeight - ($rows - 1) * $margin) / $rows); // 每张图的高度

        // 创建一个空白图像
        $mergedImage = imagecreatetruecolor($finalWidth, $finalHeight);
        // 为合并图设置背景
        $backgroundColor = imagecolorallocate($mergedImage, 255, 255, 255);
        imagefill($mergedImage, 0, 0, $backgroundColor);

        // 依次将图片放到合并图中
        foreach ($localImages as $index => $localImage) {
            // 给图片创建一个新的图像
            $image = self::imageCreateFromAny($localImage);
            // 计算图片的宽高
            $originalWidth = imagesx($image);
            $originalHeight = imagesy($image);
            // 计算缩放比例，保持图片比例
            $scale = min($imageWidth / $originalWidth, $imageHeight / $originalHeight);
            $scaledWidth = intval($originalWidth * $scale);
            $scaledHeight = intval($originalHeight * $scale);
            // 创建一个缩放后的空白图像
            $scaledImage = imagecreatetruecolor($scaledWidth, $scaledHeight);
            // 将图片按照新的尺寸复制到空白图像
            imagecopyresampled($scaledImage, $image, 0, 0, 0, 0, $scaledWidth, $scaledHeight, $originalWidth, $originalHeight);

            // 计算每张图的位置，确保水平和垂直居中
            $x = intval(($index % $cols) * ($imageWidth + $margin) + ($imageWidth - $scaledWidth) / 2);  // 横向居中
            $y = intval(floor($index / $cols) * ($imageHeight + $margin) + ($imageHeight - $scaledHeight) / 2);  // 纵向居中

            // 将缩放后的图像复制到合并图上
            imagecopy($mergedImage, $scaledImage, $x, $y, 0, 0, $scaledWidth, $scaledHeight);

            // 释放内存
            imagedestroy($image);
            imagedestroy($scaledImage);
        }
        // 将生成的临时文件删除
        unset($imagePath);
        foreach ($localImages as $imagePath) {
            // 删除临时文件
            @unlink($imagePath);
        }

        // 保存合并后的图像到本地
        $outputPath = __DIR__ . '/tmp/' . date("Ymd") . '/' . uniqid() . md5(implode(',', $localImages)) . '.jpg';
        imagejpeg($mergedImage, $outputPath, 100);
        // 释放内存
        imagedestroy($mergedImage);
        return $outputPath;
    }

    /**
     * 由文件或URL创建一个新图像
     * @param string $filepath
     * @return GdImage|bool
     * @throws Exception
     */
    public static function imageCreateFromAny(string $filepath) : GdImage|bool
    {
        if (!self::isImage($filepath)) {
            return false;
        }

        list(, , $imgType) = self::getImageSize($filepath);

        $allowedTypes = array(
            IMAGETYPE_GIF,  // [] gif
            IMAGETYPE_JPEG,  // [] jpg
            IMAGETYPE_PNG,  // [] png
            IMAGETYPE_BMP,  // [] bmp
            IMAGETYPE_WBMP, // [] WBMP
            IMAGETYPE_WEBP, // [] webp
        );

        if (!in_array($imgType, $allowedTypes)) {
            return false;
        }

        return match ($imgType) {
            IMAGETYPE_GIF => imageCreateFromGif($filepath),
            IMAGETYPE_JPEG => imageCreateFromJpeg($filepath),
            IMAGETYPE_PNG => imageCreateFromPng($filepath),
            IMAGETYPE_BMP => imageCreateFromBmp($filepath),
            IMAGETYPE_WBMP => imageCreateFromWbmp($filepath),
            IMAGETYPE_WEBP => imageCreateFromWebp($filepath),
            default => throw new Exception('暂不支持的图片格式'),
        };
    }

    /**
     * 判断文件是否为图片
     * @param string $filepath
     * @return bool
     */
    public static function isImage(string $filepath) : bool
    {
        return getimagesize($filepath) !== false;
    }
}