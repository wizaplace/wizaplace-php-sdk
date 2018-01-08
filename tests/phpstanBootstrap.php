<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

if (!function_exists('apcu_entry')) {
    /**
     * @param string $key
     * @param callable $generator
     * @param int $ttl
     * @return mixed
     */
    function apcu_entry(string $key, callable $generator, int $ttl = 0)
    {
        return null; // dummy func
    }
}

if (!function_exists('apcu_cache_info')) {
    /**
     * @param bool $limited
     * @return array|bool
     */
    function apcu_cache_info(bool $limited = false)
    {
        return []; // dummy func
    }
}

if (!function_exists('apcu_clear_cache')) {
    function apcu_clear_cache(): bool
    {
        return true; // dummy func
    }
}
