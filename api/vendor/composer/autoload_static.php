<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit430b0d0e92b425a37ffeae294c032e96
{
    public static $files = array (
        '253c157292f75eb38082b5acb06f3f01' => __DIR__ . '/..' . '/nikic/fast-route/src/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Slim\\PDO\\' => 9,
            'Slim\\' => 5,
        ),
        'P' => 
        array (
            'Psr\\Http\\Message\\' => 17,
            'Psr\\Container\\' => 14,
            'Picqer\\Barcode\\' => 15,
        ),
        'I' => 
        array (
            'Interop\\Container\\' => 18,
        ),
        'F' => 
        array (
            'FastRoute\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Slim\\PDO\\' => 
        array (
            0 => __DIR__ . '/..' . '/slim/pdo/src/PDO',
        ),
        'Slim\\' => 
        array (
            0 => __DIR__ . '/..' . '/slim/slim/Slim',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'Psr\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/container/src',
        ),
        'Picqer\\Barcode\\' => 
        array (
            0 => __DIR__ . '/..' . '/picqer/php-barcode-generator/src',
        ),
        'Interop\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/container-interop/container-interop/src/Interop/Container',
        ),
        'FastRoute\\' => 
        array (
            0 => __DIR__ . '/..' . '/nikic/fast-route/src',
        ),
    );

    public static $fallbackDirsPsr4 = array (
        0 => __DIR__ . '/../..' . '/classes',
    );

    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'Pimple' => 
            array (
                0 => __DIR__ . '/..' . '/pimple/pimple/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit430b0d0e92b425a37ffeae294c032e96::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit430b0d0e92b425a37ffeae294c032e96::$prefixDirsPsr4;
            $loader->fallbackDirsPsr4 = ComposerStaticInit430b0d0e92b425a37ffeae294c032e96::$fallbackDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit430b0d0e92b425a37ffeae294c032e96::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}