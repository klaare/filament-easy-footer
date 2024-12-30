<?php

use Devonab\FilamentEasyFooter\EasyFooterPlugin;
use Illuminate\Http\Request;

it('has correct plugin ID')
    ->expect(fn () => EasyFooterPlugin::make()->getId())
    ->toBe('filament-easy-footer');

it('skips rendering on auth pages', function () {
    $request = Mockery::mock(Request::class)->makePartial();
    $request->shouldReceive('path')->andReturn('admin/login');
    app()->instance('request', $request);

    $plugin = EasyFooterPlugin::make()
        ->hideFromAuthPages();

    expect($plugin->shouldSkipRendering())->toBeTrue();
});

it('sets footer position correctly')
    ->expect(fn () => EasyFooterPlugin::make()
        ->withFooterPosition('sidebar.footer')
        ->getRenderHook())
    ->toBe('panels::sidebar.footer');

it('enables load time display without prefix')
    ->expect(fn () => EasyFooterPlugin::make()
        ->withLoadTime()
        ->isLoadTimeEnabled())
    ->toBeTrue();

it('enables load time with prefix', function () {
    $plugin = EasyFooterPlugin::make()
        ->withLoadTime('Page loaded in');

    expect($plugin)
        ->isLoadTimeEnabled()->toBeTrue()
        ->getLoadTimePrefix()->toBe('Page loaded in');
});

it('can disable load time explicitly')
    ->expect(fn () => EasyFooterPlugin::make()
        ->withLoadTime(enabled: false)
        ->isLoadTimeEnabled())
    ->toBeFalse();

it('keeps load time enabled when setting prefix', function () {
    $plugin = EasyFooterPlugin::make()
        ->withLoadTime(enabled: false)
        ->withLoadTime('Page loaded in');

    expect($plugin)
        ->isLoadTimeEnabled()->toBeTrue()
        ->getLoadTimePrefix()->toBe('Page loaded in');
});

it('can update prefix while keeping enabled state', function () {
    $plugin = EasyFooterPlugin::make()
        ->withLoadTime('Initial prefix')
        ->withLoadTime('New prefix');

    expect($plugin)
        ->isLoadTimeEnabled()->toBeTrue()
        ->getLoadTimePrefix()->toBe('New prefix');
});

it('can add logo without URL', function () {
    $plugin = EasyFooterPlugin::make()
        ->withLogo('/path/to/logo.png');

    expect($plugin)
        ->getLogoPath()->toBe('/path/to/logo.png')
        ->getLogoUrl()->toBeNull()
        ->getLogoHeight()->toBe(20);
});

it('can add logo with URL', function () {
    $plugin = EasyFooterPlugin::make()
        ->withLogo('/path/to/logo.png', 'https://example.com', 25);

    expect($plugin)
        ->getLogoPath()->toBe('/path/to/logo.png')
        ->getLogoUrl()->toBe('https://example.com')
        ->getLogoHeight()->toBe(25);
});

it('limits and filters links correctly', function () {
    $links = [
        ['title' => 'Link 1', 'url' => 'url1'],
        ['title' => 'Link 2', 'url' => 'url2'],
        ['title' => 'Link 3', 'url' => 'url3'],
        ['title' => 'Link 4', 'url' => 'url4'],
        ['invalid' => 'Invalid Link'],
    ];

    $plugin = EasyFooterPlugin::make()->withLinks($links);

    expect($plugin->getLinks())
        ->toHaveCount(3)
        ->toEqual([
            ['title' => 'Link 1', 'url' => 'url1'],
            ['title' => 'Link 2', 'url' => 'url2'],
            ['title' => 'Link 3', 'url' => 'url3'],
        ]);
});

test('make creates new instance')
    ->expect(fn () => EasyFooterPlugin::make())
    ->toBeInstanceOf(EasyFooterPlugin::class);

afterEach(function () {
    Mockery::close();
});
