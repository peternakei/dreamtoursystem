<?php

namespace App\Project\Modules\System\Library\Services;

use Illuminate\Http\UploadedFile;

/**
 * Normalizes raster image uploads to WebP when GD or Imagick can decode and encode.
 * Non-raster files (e.g. PDF) are stored unchanged.
 */
class RasterUploadNormalizer
{
    public const DEFAULT_WEBP_QUALITY = 88;

    /**
     * @return array{binary: string, extension: string, size: int}
     */
    public function normalize(UploadedFile $file, int $webpQuality = self::DEFAULT_WEBP_QUALITY): array
    {
        $mime = strtolower((string) ($file->getMimeType() ?: ''));
        $mimeBase = strtolower(strtok($mime, ';') ?: '');

        $binary = @file_get_contents($file->getRealPath() ?: '');
        if ($binary === false) {
            $binary = '';
        }

        if ($binary === '' || !$this->isRasterImageMime($mimeBase)) {
            return $this->passthrough($file, $binary);
        }

        $webp = $this->encodeToWebp($binary, $webpQuality);
        if ($webp !== null && $webp !== '') {
            return [
                'binary' => $webp,
                'extension' => 'webp',
                'size' => strlen($webp),
            ];
        }

        return $this->passthrough($file, $binary);
    }

    /**
     * @return array{binary: string, extension: string, size: int}
     */
    protected function passthrough(UploadedFile $file, string $binary): array
    {
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');

        return [
            'binary' => $binary,
            'extension' => $ext,
            'size' => $binary === '' ? (int) $file->getSize() : strlen($binary),
        ];
    }

    protected function isRasterImageMime(string $mimeBase): bool
    {
        return in_array($mimeBase, [
            'image/jpeg',
            'image/jpg',
            'image/pjpeg',
            'image/png',
            'image/x-png',
            'image/gif',
            'image/webp',
            'image/bmp',
            'image/x-ms-bmp',
            'image/x-windows-bmp',
        ], true);
    }

    protected function encodeToWebp(string $binary, int $quality): ?string
    {
        $quality = max(0, min(100, $quality));

        if (function_exists('imagecreatefromstring') && function_exists('imagewebp')) {
            $im = @imagecreatefromstring($binary);
            if ($im !== false) {
                if (function_exists('imagepalettetotruecolor') && !imageistruecolor($im)) {
                    imagepalettetotruecolor($im);
                }
                if (function_exists('imagealphablending')) {
                    imagealphablending($im, true);
                }
                if (function_exists('imagesavealpha')) {
                    imagesavealpha($im, true);
                }

                ob_start();
                $ok = @imagewebp($im, null, $quality);
                if (function_exists('imagedestroy')) {
                    imagedestroy($im);
                } else {
                    unset($im);
                }
                $out = ob_get_clean();

                if ($ok && is_string($out) && $out !== '') {
                    return $out;
                }
            }
        }

        return $this->encodeToWebpImagick($binary, $quality);
    }

    protected function encodeToWebpImagick(string $binary, int $quality): ?string
    {
        if (!extension_loaded('imagick') || !class_exists(\Imagick::class)) {
            return null;
        }

        try {
            $img = new \Imagick();
            $img->readImageBlob($binary);
            $img->setImageFormat('webp');
            $img->setImageCompressionQuality($quality);
            $blob = $img->getImageBlob();
            $img->clear();
            $img->destroy();

            return is_string($blob) && $blob !== '' ? $blob : null;
        } catch (\Throwable) {
            return null;
        }
    }
}

