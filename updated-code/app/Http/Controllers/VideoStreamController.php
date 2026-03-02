<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoStreamController extends Controller
{
    /**
     * Stream a video file from storage with Range request support (206 Partial Content)
     * so that duration is shown and the seek slider works.
     */
    public function __invoke(Request $request)
    {
        $path = $request->query('path');
        if (! $path || ! is_string($path)) {
            abort(404);
        }
        // Path must be relative to storage (e.g. "hero/videos/xyz.mp4", "interviews/videos/abc.mp4")
        $path = ltrim(str_replace('\\', '/', $path), '/');
        if (preg_match('/\.\./u', $path) || ! preg_match('/\.(mp4|webm|mov|mpeg|ogg)$/i', $path)) {
            abort(404);
        }

        $disk = Storage::disk('public');
        if (! $disk->exists($path)) {
            abort(404);
        }

        $fullPath = $disk->path($path);
        if (! is_file($fullPath) || ! is_readable($fullPath)) {
            abort(404);
        }

        $size = filesize($fullPath);
        $mime = $this->mimeForPath($path);

        $rangeHeader = $request->header('Range');
        if (! $rangeHeader || ! preg_match('/^bytes=(\d*)-(\d*)$/i', trim($rangeHeader), $m)) {
            // No range or invalid: return full file with Accept-Ranges so client can seek later
            return response()->file($fullPath, [
                'Content-Type' => $mime,
                'Content-Length' => $size,
                'Accept-Ranges' => 'bytes',
            ]);
        }

        $start = $m[1] === '' ? 0 : (int) $m[1];
        $end = $m[2] === '' ? $size - 1 : (int) $m[2];
        $end = min($end, $size - 1);
        if ($start > $end) {
            $start = 0;
            $end = $size - 1;
        }
        $length = $end - $start + 1;

        $stream = fopen($fullPath, 'rb');
        if ($stream === false) {
            abort(500);
        }
        fseek($stream, $start, SEEK_SET);

        return response()->stream(function () use ($stream, $length) {
            $chunkSize = 8192;
            $read = 0;
            while (! feof($stream) && $read < $length) {
                $toRead = min($chunkSize, $length - $read);
                echo fread($stream, $toRead);
                $read += $toRead;
            }
            fclose($stream);
        }, 206, [
            'Content-Type' => $mime,
            'Content-Length' => $length,
            'Content-Range' => sprintf('bytes %d-%d/%d', $start, $end, $size),
            'Accept-Ranges' => 'bytes',
        ]);
    }

    private function mimeForPath(string $path): string
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $map = [
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'mov' => 'video/quicktime',
            'mpeg' => 'video/mpeg',
            'ogg' => 'video/ogg',
        ];

        return $map[$ext] ?? 'video/mp4';
    }
}
