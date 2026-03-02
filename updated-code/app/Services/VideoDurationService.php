<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class VideoDurationService
{
    /**
     * Fetch video duration from link. Returns formatted string (e.g. "12 min") or null.
     * Supports Vimeo (oEmbed, no key) and YouTube (optional API key in config).
     */
    public function getDurationFromUrl(?string $url): ?string
    {
        if (empty($url) || !Str::isUrl($url)) {
            return null;
        }

        $url = trim($url);

        if ($this->isVimeoUrl($url)) {
            return $this->fetchVimeoDuration($url);
        }

        if ($this->isYoutubeUrl($url)) {
            return $this->fetchYoutubeDuration($url);
        }

        return null;
    }

    protected function isVimeoUrl(string $url): bool
    {
        return preg_match('#^https?://(www\.)?vimeo\.com/#i', $url) === 1;
    }

    protected function isYoutubeUrl(string $url): bool
    {
        return preg_match('#^https?://(www\.)?(youtube\.com/watch\?v=|youtu\.be/)#i', $url) === 1;
    }

    protected function fetchVimeoDuration(string $url): ?string
    {
        try {
            $response = Http::timeout(10)->get('https://vimeo.com/api/oembed.json', [
                'url' => $url,
            ]);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();
            $seconds = $data['duration'] ?? null;

            if ($seconds === null || !is_numeric($seconds)) {
                return null;
            }

            return $this->formatSeconds((int) $seconds);
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function fetchYoutubeDuration(string $url): ?string
    {
        $videoId = $this->extractYoutubeVideoId($url);
        if (!$videoId) {
            return null;
        }

        $apiKey = config('services.youtube.api_key');
        if (empty($apiKey)) {
            return null;
        }

        try {
            $response = Http::timeout(10)->get('https://www.googleapis.com/youtube/v3/videos', [
                'id' => $videoId,
                'part' => 'contentDetails',
                'key' => $apiKey,
            ]);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();
            $items = $data['items'] ?? [];
            if (empty($items)) {
                return null;
            }

            $durationIso = $items[0]['contentDetails']['duration'] ?? null;
            if (empty($durationIso)) {
                return null;
            }

            $seconds = $this->parseIso8601Duration($durationIso);
            return $seconds !== null ? $this->formatSeconds($seconds) : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function extractYoutubeVideoId(string $url): ?string
    {
        if (preg_match('#(?:youtube\.com/watch\?v=|youtu\.be/)([a-zA-Z0-9_-]{11})#i', $url, $m)) {
            return $m[1];
        }
        return null;
    }

    /**
     * Parse ISO 8601 duration (e.g. PT12M30S, PT1H2M10S) to seconds.
     */
    protected function parseIso8601Duration(string $duration): ?int
    {
        if (!preg_match('/^PT(?:(\d+)H)?(?:(\d+)M)?(?:(\d+)S)?$/i', $duration, $m)) {
            return null;
        }
        $hours = (int) ($m[1] ?? 0);
        $minutes = (int) ($m[2] ?? 0);
        $seconds = (int) ($m[3] ?? 0);
        return $hours * 3600 + $minutes * 60 + $seconds;
    }

    protected function formatSeconds(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds . ' sec';
        }
        $minutes = (int) floor($seconds / 60);
        $secs = $seconds % 60;
        if ($secs === 0) {
            return $minutes . ' min';
        }
        return $minutes . ' min ' . $secs . ' sec';
    }
}
