<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class FileService
{
    public static function uploadFile($file, $folder = 'uploads')
    {
        return $file->store($folder, 'public');
    }

    public static function uploadMultipleFiles($files, $folder = 'uploads')
    {
        $paths = [];
        foreach ($files as $file) {
            $paths[] = self::uploadFile($file, $folder);
        }
        return json_encode($paths);
    }

    public static function deleteFile($filePath)
    {
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    }

    public static function deleteMultipleFiles($filePaths)
    {

        if (is_string($filePaths)) {
            $filePaths = json_decode($filePaths, true);
        }

        if (!is_array($filePaths)) {
            return;
        }

        foreach ($filePaths as $file) {
            self::deleteFile($file);
        }
    }
}
