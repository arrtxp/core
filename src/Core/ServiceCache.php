<?php

namespace Core;

use Throwable;

use const TIME;

abstract class ServiceCache
{
    private function getFilePathCache(string $key): string
    {
        $key = str_replace('\\', '_', $key);

        return DIR_TMP . "cache" . DIRECTORY_SEPARATOR . "{$key}";
    }

    protected function getCache(string $key, int $lifetime = 86400): mixed
    {
        $path = $this->getFilePathCache($key);
        if (!file_exists($path)) {
            return null;
        }

        if (filemtime($path) < (TIME - $lifetime)) {
            $this->removeCache($key);

            return null;
        }

        try {
            return unserialize(file_get_contents($path));
        } catch (Throwable $e) {
            $this->removeCache($key);

            throw $e;
        }
    }

    protected function setCache(string $key, $data): void
    {
        $parts = explode('/', $key);
        array_pop($parts);

        $dir = $this->getFilePathCache('') . implode('/', $parts);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($this->getFilePathCache($key), serialize($data));
    }

    public function removeCache(string $key): void
    {
        $path = $this->getFilePathCache($key);
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
}