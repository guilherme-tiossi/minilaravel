<?php

function dd()
{
    foreach (func_get_args() as $x) {
        var_dump($x);
    }
    die;
}

function app(string $string)
{
    # TODO
}