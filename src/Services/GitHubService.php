<?php

namespace Devonab\FilamentEasyFooter\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GitHubService
{
    protected bool $enabled = false;
    protected ?string $token = null;
    protected ?string $repository = null;
    protected int $cacheTtl;
    protected string $defaultVersion;

    public function __construct(
        string $repository = null,
        string $token = null,
        int $cacheTtl = 3600,
        string $defaultVersion = '0.0'
    ) {
        $this->repository = $repository ?? config('filament-easy-footer.github.repository');
        $this->token = $token ?? config('filament-easy-footer.github.token');
        $this->cacheTtl = $cacheTtl;
        $this->defaultVersion = $defaultVersion;
    }

    public function enable(): self
    {
        $this->enabled = true;
        return $this;
    }

    public function disable(): self
    {
        $this->enabled = false;
        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getLatestTag(string $repository = null): string
    {
        if (! $this->enabled) {
            return $this->defaultVersion;
        }

        $repository = $repository ?? $this->repository;
        $cacheKey = "filament-easy-footer.github.{$repository}.latest-tag";

        return Cache::remember(
            $cacheKey,
            $this->cacheTtl,
            fn () => $this->fetchLatestTag($repository) ?? $this->defaultVersion
        );
    }

    protected function fetchLatestTag(string $repository): ?string
    {
        try {
            $response = Http::withToken($this->token)
                ->get("https://api.github.com/repos/{$repository}/releases/latest");

            if ($response->successful()) {
                return $response->json('tag_name');
            }

            $tagsResponse = Http::withToken($this->token)
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
