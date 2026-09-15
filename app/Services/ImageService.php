<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    public static function upload(UploadedFile $file, string $folder): string
    {
        if (! static::enabled()) {
            return Storage::url($file->store($folder, 'public'));
        }

        static::configure();

        $result = \Cloudinary\Uploader::upload($file->getRealPath(), [
            'folder' => $folder,
            'resource_type' => 'auto',
        ]);

        return $result['secure_url'];
    }

    public static function uploadContents(string $contents, string $folder): string
    {
        if (! static::enabled()) {
            $name = $folder.'/'.Str::random(40).'.png';
            Storage::disk('public')->put($name, $contents);

            return Storage::url($name);
        }

        static::configure();

        $path = tempnam(sys_get_temp_dir(), 'img');

        try {
            file_put_contents($path, $contents);

            $result = \Cloudinary\Uploader::upload($path, [
                'folder' => $folder,
                'resource_type' => 'image',
            ]);

            return $result['secure_url'];
        } finally {
            @unlink($path);
        }
    }

    public static function delete(?string $url): void
    {
        if (! $url) {
            return;
        }

        if (! str_starts_with($url, 'http')) {
            $relative = str_starts_with($url, '/storage/')
                ? substr($url, strlen('/storage/'))
                : $url;

            Storage::disk('public')->delete($relative);

            return;
        }

        if (static::enabled() && str_contains($url, 'res.cloudinary.com')) {
            static::deleteCloudinary($url);
        }
    }

    public static function enabled(): bool
    {
        return (bool) config('services.cloudinary.cloud_name');
    }

    protected static function configure(): void
    {
        \Cloudinary::config([
            'cloud_name' => config('services.cloudinary.cloud_name'),
            'api_key' => config('services.cloudinary.api_key'),
            'api_secret' => config('services.cloudinary.api_secret'),
            'secure' => true,
        ]);
    }

    protected static function deleteCloudinary(string $url): void
    {
        $publicId = static::publicIdFromUrl($url);

        if (! $publicId) {
            return;
        }

        static::configure();

        try {
            (new \Cloudinary\Api())->delete_resources([$publicId], ['resource_type' => 'image']);
        } catch (\Throwable) {
            // best effort : en cas d'échec la ressource reste dans le cloud
        }
    }

    protected static function publicIdFromUrl(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);

        if (! $path) {
            return null;
        }

        $parts = explode('/', $path);
        $position = array_search('upload', $parts, true);

        if ($position === false) {
            return null;
        }

        $rest = array_slice($parts, $position + 1);

        if (isset($rest[0]) && preg_match('/^v\d+$/', $rest[0])) {
            array_shift($rest);
        }

        if (empty($rest)) {
            return null;
        }

        $publicId = implode('/', $rest);

        return preg_replace('/\.[a-z0-9]{1,6}$/', '', $publicId) ?: $publicId;
    }
}