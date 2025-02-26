<?php

use Devonab\FilamentEasyFooter\EasyFooterPlugin;
use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;

it('has correct plugin ID')
    ->expect(fn () => EasyFooterPlugin::make()->getId())
    ->toBe('filament-easy-footer');

it('enables footer by default')
    ->expect(fn () => EasyFooterPlugin::make()->isFooterEnabled())
    ->toBeTrue();

it('can disable footer', function () {
    $plugin = EasyFooterPlugin::make()
        ->footerEnabled(false);

    expect($plugin->isFooterEnabled())->toBeFalse();
});

it('can enable footer explicitly', function () {
    $plugin = EasyFooterPlugin::make()
        ->footerEnabled(false)
        ->footerEnabled(true);

    expect($plugin->isFooterEnabled())->toBeTrue();
});

it('skips rendering when footer is disabled', function () {
    $request = Mockery::mock(Request::class)->makePartial();
    $request->shouldReceive('path')->andReturn('admin/dashboard');
    app()->instance('request', $request);

    $plugin = EasyFooterPlugin::make()
        ->footerEnabled(false);

    expect($plugin)
        ->isFooterEnabled()->toBeFalse()
        ->shouldSkipRendering()->toBeFalse();
});

it('shows footer by default', function () {
    $request = Mockery::mock(Request::class)->makePartial();
    $request->shouldReceive('path')->andReturn('admin/dashboard');
    app()->instance('request', $request);

    $plugin = EasyFooterPlugin::make();

    expect($plugin->shouldSkipRendering())->toBeFalse();
});

it('shows footer on non-configured page', function () {
    $request = Mockery::mock(Request::class)->makePartial();
    $request->shouldReceive('path')->andReturn('admin/dashboard');
    app()->instance('request', $request);

    $plugin = EasyFooterPlugin::make()
        ->hiddenFromPagesEnabled()
        ->hiddenFromPages(['admin/users', 'admin/login']);

    expect($plugin->shouldSkipRendering())->toBeFalse();
});

it('hides footer on configured page', function () {
    $request = Mockery::mock(Request::class)->makePartial();
    $request->shouldReceive('path')->andReturn('admin/dashboard');
    app()->instance('request', $request);

    $plugin = EasyFooterPlugin::make()
        ->hiddenFromPagesEnabled()
        ->hiddenFromPages(['admin/dashboard', 'admin/login']);

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
        ->withLogo('/path/to/logo.png', 'https://example.com', 'Something', 25);

    expect($plugin)
        ->getLogoPath()->toBe('/path/to/logo.png')
        ->getLogoText()->toBe('Something')
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

it('can set plain text sentence', function () {
    $plugin = EasyFooterPlugin::make()
        ->withSentence('Custom Footer Text');

    expect($plugin->getSentence())->toBe('Custom Footer Text');
});

it('allows safe HTML tags', function () {
    $plugin = EasyFooterPlugin::make()
        ->withSentence(new HtmlString('<strong>Custom</strong> <em>Footer</em> Text'));

    expect($plugin->getSentence())->toBe('<strong>Custom</strong> <em>Footer</em> Text');
});

it('strips unsafe HTML tags but keeps content', function () {
    $plugin = EasyFooterPlugin::make()
        ->withSentence(new HtmlString('<script>alert("I\'m a hacker yiek yiek")</script><strong>Safe</strong>'));

    expect($plugin->getSentence())->toBe('alert("I\'m a hacker yiek yiek")<strong>Safe</strong>');
});

it('defaults to null sentence', function () {
    $plugin = EasyFooterPlugin::make();

    expect($plugin->getSentence())->toBeNull();
});

test('make creates new instance')
    ->expect(fn () => EasyFooterPlugin::make())
    ->toBeInstanceOf(EasyFooterPlugin::class);

afterEach(function () {
    Mockery::close();
});
