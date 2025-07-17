<?php

namespace Core;

use Throwable;

final class Cache
{
    private static string $dirTemp;

    private int $lifetime;

    public function __construct(private readonly string $key)
    {
    }

    public static function setDirTemp(string $path): void
    {
        self::$dirTemp = $path;
    }

    private static function getBasePath(): string
    {
        return self::$dirTemp;
    }

    private function getPath(string $key): string
    {
        $key = str_replace('\\', '_', $key);

        return self::getBasePath() . "{$key}";
    }

    public static function clearAll(): void
    {
        $path = self::getBasePath();

        exec("rm -r {$path}/*");
    }

    public function has(): bool
    {
        $path = $this->getPath($this->key);

        if (!file_exists($path)) {
            return false;
        }

        if (isset($this->lifetime) && filemtime($path) < (time() - $this->lifetime)) {
            $this->delete();

            return false;
        }

        return true;
    }

    public function get(): mixed
    {
        try {
            return unserialize(file_get_contents($this->getPath($this->key)));
        } catch (Throwable $e) {
            $this->delete();

            throw $e;
        }
    }

    public function set(mixed $data): void
    {
        $parts = explode(DIRECTORY_SEPARATOR, $this->getPath($this->key));
        $filename = array_pop($parts);

        $dir = implode(DIRECTORY_SEPARATOR, $parts);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($dir . DIRECTORY_SEPARATOR . $filename, serialize($data));
    }

    public function lifetime(int $seconds): void
    {
        $this->lifetime = $seconds;
    }

    public function delete(): void
    {
        $path = $this->getPath($this->key);
        if (is_file($path)) {
            @unlink($path);
        } else {
            $files = glob("{$path}*");
            foreach ($files as $file) {
                if ($file === "." || $file === "..") {
                    continue;
                }

                @unlink($file);
            }
        }
    }

    public static function clear(string $key): void
    {
        $cache = new self($key);
        $cache->delete();
    }
}