<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Stores uploaded files on the "public" disk and mirrors them into
 * public/storage.
 *
 * Neither this app's local dev environment (Windows/XAMPP, no symlink
 * privileges) nor its production host (InfinityFree, FTP-only, no shell)
 * can rely on `php artisan storage:link`. Every upload feature must go
 * through this copy step instead of a real symlink — see DEPLOYMENT.md.
 */
class PublicUploadService
{
    /**
     * Store an uploaded file under the given folder on the "public" disk
     * and mirror it into public/storage so it's reachable without a symlink.
     *
     * @return string The stored path relative to the "public" disk (e.g. "reports/xyz.jpg").
     */
    public function store(UploadedFile $file, string $folder): string
    {
        $path = $file->store($folder, 'public');

        $source = storage_path('app/public/' . $path);
        $destination = public_path('storage/' . $path);

        // If public/storage is a real symlink (e.g. storage:link succeeded,
        // which it does on some local setups), $source and $destination are
        // already the same file — skip the copy instead of relying on
        // copy() to fail safely when asked to copy a file onto itself.
        if (realpath($source) === realpath($destination)) {
            return $path;
        }

        if (!file_exists(dirname($destination))) {
            mkdir(dirname($destination), 0755, true);
        }

        copy($source, $destination);

        return $path;
    }

    /**
     * Delete a previously stored file from both the "public" disk and its
     * public/storage mirror. Safe to call with null/empty paths.
     */
    public function delete(?string $path): void
    {
        if (!$path) {
            return;
        }

        Storage::disk('public')->delete($path);

        $mirrored = public_path('storage/' . $path);
        if (file_exists($mirrored)) {
            unlink($mirrored);
        }
    }
}
