<?php

namespace App\Project\_Src;

class Registry
{
    public static function all(): array
    {
        return json_decode(file_get_contents(__DIR__.'/registry.json'), true, 512, JSON_THROW_ON_ERROR);
    }

    public static function routes(string $channel): array
    {
        return self::all()['routes'][$channel] ?? [];
    }

    public static function modules(): array
    {
        return self::all()['modules'];
    }
}
