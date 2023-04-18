<?php

if (!function_exists('blink')) {
    function blink(): \Illuminate\Contracts\Cache\Repository
    {
        return cache()->store('array');
    }
}

