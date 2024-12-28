<?php

namespace Devonab\FilamentEasyFooter\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GitHubService
{
    public function getLatestTag(string $repository): string
    {
        if (! config('easy-footer.github.enabled')) {
            return '0.0';
        }

        $cacheKey = "easy-footer.github.{$repository}.latest-tag";

        return Cache::remember(
            $cacheKey,
            config('easy-footer.github.cache_ttl', 3600),
            fn () => $this->fetchLatestTag($repository) ?? config('easy-footer.github.default_version', '0.0')
        );
    }

    protected function fetchLatestTag(string $repository): ?string
    {
        $token = config('easy-footer.github.token');

        try {
            $response = Http::withToken($token)
                ->get("https://api.github.com/repos/{$repository}/releases/latest");

            if ($response->successful()) {
                return $response->json('tag_name');
            }

            $tagsResponse = Http::withToken($token)
                ->get("https://api.github.com/repos/{$repository}/tags");

            if ($tagsResponse->successful() && ! empty($tagsResponse->json())) {
                return $tagsResponse->json()[0]['name'];
            }

        } catch (\Exception $e) {
            report($e);
        }

        return null;
    }
}
