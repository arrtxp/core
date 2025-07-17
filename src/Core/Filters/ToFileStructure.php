<?php

namespace Core\Filters;

use Core\Filter;
use Core\Structures;
use Throwable;

class ToFileStructure extends Filter
{
    public function filter($file): ?Structures\FileUpload
    {
        if ($file === null) {
            return null;
        }

        try {
            if (empty($file['name']) || empty($file['tmp_name']) || !empty($file['error']) || empty($file['type'])) {
                return null;
            }

            $type = [
                'image/png' => 'png',
            ];

            if (empty($type[$file['type']])) {
                return null;
            }

            return new Structures\FileUpload($file['name'], $file['tmp_name'], $type[$file['type']], $file['size']);
        } catch (Throwable $e) {
            pre($e->getMessage());

            return null;
        }
    }
}