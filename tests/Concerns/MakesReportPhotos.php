<?php

namespace Tests\Concerns;

use Illuminate\Http\UploadedFile;

/**
 * A report needs a real photo, and UploadedFile::fake()->image() needs GD,
 * which isn't always installed. This is a valid 1×1 JPEG (134 bytes).
 */
trait MakesReportPhotos
{
    protected function reportPhoto(string $name = 'photo.jpg'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, base64_decode(
            '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA='
        ));
    }
}
