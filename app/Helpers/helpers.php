<?php

use Illuminate\Support\Facades\Config;

if (!function_exists('dizionario')) {
    function dizionario($key = null, $default = null)
    {
        $dizionario = Config::get('custom.dizionario', []);

        if (is_null($key)) {
            return $dizionario;
        }

        return $dizionario[$key] ?? $default;
    }
}
