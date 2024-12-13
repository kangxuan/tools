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
        list($watermarkWidth, $watermarkHeight) = getimagesize($watermarkPath);

        // 创建图片和水印资源
        $sourceImage = self::createImageFromFile($imagePath, $imageType);
        $watermarkImage = self::createImageFromFile($watermarkPath, $imageType);

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
        return getimagesize($imagePath);
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
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                imagejpeg($image, $outputPath); // 保存JPEG格式
                break;
            case IMAGETYPE_PNG:
                imagepng($image, $outputPath);  // 保存PNG格式
                break;
            case IMAGETYPE_GIF:
                imagegif($image, $outputPath);  // 保存GIF格式
                break;
            default:
                throw new Exception('不支持的图片类型'); // 异常处理：不支持的类型
        }
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
}