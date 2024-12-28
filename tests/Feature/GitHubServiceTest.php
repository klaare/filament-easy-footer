<?php

use Devonab\FilamentEasyFooter\Services\GitHubService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Cache::flush();
    Config::set('easy-footer.github.enabled', true);
    Config::set('easy-footer.github.token', 'fake-token');
    Config::set('easy-footer.github.repository', 'devonab/filament-easy-footer');
});

it('returns latest release tag when available', function () {
    Http::fake([
        'github.com/repos/*/releases/latest' => Http::response([
            'tag_name' => 'v1.2.3',
        ], 200),
    ]);

    $service = app(GitHubService::class);
    $tag = $service->getLatestTag(config('easy-footer.github.repository'));

    expect($tag)->toBe('v1.2.3');
});

it('falls back to tags when no releases exist', function () {
    Http::fake([
        'github.com/repos/*/releases/latest' => Http::response([], 404),
        'github.com/repos/*/tags' => Http::response([
            ['name' => 'v1.0.0'],
            ['name' => 'v0.9.0'],
        ], 200),
    ]);

    $service = app(GitHubService::class);
    $tag = $service->getLatestTag(config('easy-footer.github.repository'));

    expect($tag)->toBe('v1.0.0');
});

it('returns default version when no tags or releases exist', function () {
    Http::fake([
        'github.com/repos/*/releases/latest' => Http::response([], 404),
        'github.com/repos/*/tags' => Http::response([], 200),
    ]);

    $service = app(GitHubService::class);
    $tag = $service->getLatestTag(config('easy-footer.github.repository'));

    expect($tag)->toBe('0.0');
});

it('returns default version when github is disabled', function () {
    Config::set('easy-footer.github.enabled', false);

    $service = app(GitHubService::class);
    $tag = $service->getLatestTag(config('easy-footer.github.repository'));

    expect($tag)->toBe('0.0');
});

it('caches the github response', function () {
    Http::fake([
        'github.com/repos/*/releases/latest' => Http::response([
            'tag_name' => 'v1.2.3',
        ], 200),
    ]);

    $service = app(GitHubService::class);

    $tag1 = $service->getLatestTag(config('easy-footer.github.repository'));

    $tag2 = $service->getLatestTag(config('easy-footer.github.repository'));

    expect($tag1)->toBe('v1.2.3')
        ->and($tag2)->toBe('v1.2.3');

    Http::assertSentCount(1);
});

it('uses authorization token in requests', function () {
    Http::fake([
        'github.com/repos/*/releases/latest' => Http::response([
            'tag_name' => 'v1.2.3',
        ], 200),
    ]);

    $service = app(GitHubService::class);
    $service->getLatestTag(config('easy-footer.github.repository'));

    Http::assertSent(function ($request) {
        return $request->hasHeader('Authorization', 'Bearer fake-token');
    });
});

it('handles network errors gracefully', function () {
    Http::fake([
        'github.com/repos/*/releases/latest' => Http::response([], 500),
        'github.com/repos/*/tags' => Http::response([], 500),
    ]);

    $service = app(GitHubService::class);
    $tag = $service->getLatestTag(config('easy-footer.github.repository'));

    expect($tag)->toBe('0.0');
});
