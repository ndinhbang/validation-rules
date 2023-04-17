<?php

if (!function_exists('blink')) {
    function blink(): \Illuminate\Contracts\Cache\Repository
    {
        return cache()->store('array');
    }
}

if (!function_exists('parseNamedParameters')) {
    /**
     * Parse named parameters to $key => $value items.
     *
     * @param array<int, int|string> $parameters
     * @return array
     */
    function parseNamedParameters(array $parameters): array
    {
        return array_reduce($parameters, function ($result, $item) {
            [$key, $value] = array_pad(explode('=', $item, 2), 2, null);

            $result[$key] = $value;

            return $result;
        });
    }
}

