<?php

namespace Core\Filters;

use Core\Structures;
use Throwable;

class ToImageStructure extends ToFileStructure
{
    public function filter($file): ?Structures\Image
    {
        $file = parent::filter($file);

        if (!$file) {
            return null;
        }

        try {
            $data = getimagesize($file->path);

            if (empty($data[0]) || empty($data[1])) {
                return null;
            }

            return new Structures\Image(
                $file->name,
                $file->path,
                $file->ext,
                $file->size,
                $data[1],
                $data[2],
            );
        } catch (Throwable $e) {
            return null;
        }
    }
}