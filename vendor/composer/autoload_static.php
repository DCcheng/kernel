<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3a6130ad212a7dd339756d3d6f189f78
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Ftoken\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Ftoken\\' => 
        array (
            0 => __DIR__ . '/../..' . '/vender/ftoken',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3a6130ad212a7dd339756d3d6f189f78::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3a6130ad212a7dd339756d3d6f189f78::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
