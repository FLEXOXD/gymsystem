<?php

namespace App\Support;

use Illuminate\Filesystem\Filesystem;
use RuntimeException;

class TestingFilesystem extends Filesystem
{
    /**
     * Windows-safe replace for testing.
     * Uses retries and fallback direct write when rename is temporarily locked.
     */
    public function replace($path, $content, $mode = null): void
    {
        clearstatcache(true, $path);
        $path = realpath($path) ?: $path;

        $directory = dirname($path);
        if (! is_dir($directory)) {
            @mkdir($directory, 0777, true);
        }

        $tempPath = tempnam($directory, basename($path));

        if ($tempPath === false) {
            throw new RuntimeException('No se pudo crear archivo temporal para replace: '.$path);
        }

        if (! is_null($mode)) {
            @chmod($tempPath, $mode);
        } else {
            @chmod($tempPath, 0777 - umask());
        }

        file_put_contents($tempPath, $content);

        for ($attempt = 0; $attempt < 20; $attempt++) {
            if (@rename($tempPath, $path)) {
                return;
            }

            usleep(25_000);
        }

        if (is_file($path)) {
            @unlink($path);
            if (@rename($tempPath, $path)) {
                return;
            }
        }

        if (@file_put_contents($path, $content, LOCK_EX) !== false) {
            @unlink($tempPath);

            return;
        }

        throw new RuntimeException('No se pudo reemplazar el archivo compilado: '.$path);
    }
}

