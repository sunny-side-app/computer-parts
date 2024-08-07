<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitdabcac6156c7843ab49fa8df21ad6fd7
{
    public static $files = array (
        '6e3fae29631ef280660b3cdad06f25a8' => __DIR__ . '/..' . '/symfony/deprecation-contracts/function.php',
    );

    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\Container\\' => 14,
        ),
        'F' => 
        array (
            'Faker\\' => 6,
        ),
        'C' => 
        array (
            'Commands\\' => 9,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/container/src',
        ),
        'Faker\\' => 
        array (
            0 => __DIR__ . '/..' . '/fakerphp/faker/src/Faker',
        ),
        'Commands\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Commands',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitdabcac6156c7843ab49fa8df21ad6fd7::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitdabcac6156c7843ab49fa8df21ad6fd7::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitdabcac6156c7843ab49fa8df21ad6fd7::$classMap;

        }, null, ClassLoader::class);
    }
}
