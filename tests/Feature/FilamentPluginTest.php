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

it('enables load time display')
    ->expect(fn () => EasyFooterPlugin::make()
        ->withLoadTime()
        ->isLoadTimeEnabled())
    ->toBeTrue();

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
