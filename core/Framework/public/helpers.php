<?php

function dd()
{
    foreach (func_get_args() as $x) {
        var_dump($x);
    }
    die;
}

function app(string $path)
{
    $dir = getcwd();

    $i = 0;
    while (basename($dir) !== 'core') {
        $i++;
        if (is_dir($dir . '/core')) {
            chdir($dir . '/core');
        } else {
            chdir(dirname($dir));
        }
        $dir = getcwd();

        if ($i >= 10) {
            throw new Exception('Core folder not found!');
        }
     }

    include $path;

    chdir(__DIR__);
}