<?php

namespace Arrtxp\Core;

class DTOOption
{
    public static array $options = [];

    public function __construct(private string $name)
    {
    }

    public static function set(string $key, mixed $value): void
    {
        self::$options[$key] = $value;
    }

    public function get(): mixed
    {
        return self::$options[$this->name];
    }
}