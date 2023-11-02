<?php

namespace App\Http\Helpers;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FileHelper
{
    public static function moveFile($value, $file_path): string
    {
        $storage = self::getStorage();

        $attribute_file = null;
        if ($storage->exists("temp/{$value}")) {
            if ($storage->move("temp/{$value}", $file_path . $value)) {
                $attribute_file = $value;
            }
        } else if ($storage->exists($file_path . $value)) {
            $attribute_file = $value;
        }

        if ($attribute_file === null) {
            throw new BadRequestHttpException(__('error.failed_to_upload'));
        }

        return $attribute_file;
    }

    public static function deleteFile(array|string $file_path): bool
    {
        return self::getStorage()->delete($file_path);
    }

    public static function copyFile(string $from_file, string $to_file): bool
    {
        $storage = self::getStorage();

        if ($storage->exists($from_file)) {
            return $storage->copy($from_file, $to_file);
        }
        return false;
    }

    public static function getTemporaryUrl($path, $expire): string
    {
        $storage = self::getStorage();

        return $storage->temporaryUrl($path, $expire);
    }

    private static function getStorage(): Filesystem
    {
        return Storage::disk('gcs');
    }
}
