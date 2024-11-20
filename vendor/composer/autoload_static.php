<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit90f1a9bf5ffbaefc1b49fa69a0428067
{
    public static $prefixLengthsPsr4 = array (
        'K' => 
        array (
            'Kx\\Tools\\' => 9,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Kx\\Tools\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit90f1a9bf5ffbaefc1b49fa69a0428067::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit90f1a9bf5ffbaefc1b49fa69a0428067::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit90f1a9bf5ffbaefc1b49fa69a0428067::$classMap;

        }, null, ClassLoader::class);
    }
}
