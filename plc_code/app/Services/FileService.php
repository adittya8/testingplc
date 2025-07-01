<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileService
{
    public static function uploadFile(UploadedFile $file, Model $modelObject, string|null $name = null, string $disk = 'public'): string
    {
        $path = getStoragePath($modelObject);

        if (!Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->makeDirectory($path);
        }
        $name = $name ? $name . '.' . $file->extension() : $file->getClientOriginalName();
        Storage::disk($disk)->putFileAs($path, $file, $name);

        return $name;
    }

    public static function deleteFile(string $path, string $disk = 'public'): bool
    {
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }

    public static function deleteFolder(string $path, string $disk = 'public'): bool
    {
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->deleteDirectory($path);
        }

        return false;
    }
}
