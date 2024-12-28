<?php

namespace Devonab\FilamentEasyFooter;

use Devonab\FilamentEasyFooter\Services\GitHubService;
use Filament\Contracts\Plugin;
use Filament\Panel;

/**
 * EasyFooterPlugin - A Filament plugin to add a customizable footer to your admin panel
 *
 * @implements Plugin
 */
class EasyFooterPlugin implements Plugin
{
    private const MAX_LINKS = 3;

    private const AUTH_PATHS = ['admin/login', 'admin/register', 'admin/forgot-password'];

    protected bool $githubEnabled = false;

    protected bool $borderTopEnabled = true;

    protected bool $showLogo = true;

    protected bool $showUrl = true;

    protected bool $hideFromAuthPagesEnabled = false;

    protected bool $loadTimeEnabled = false;

    protected string $footerPosition = 'footer';

    protected array $links = [];

    /**
     * Get the unique identifier for the plugin
     */
    public function getId(): string
    {
        return 'filament-easy-footer';
    }

    /**
     * Register the plugin with the panel
     *
     * @param  Panel  $panel  The Filament panel instance
     */
    public function register(Panel $panel): void
    {
        if ($this->shouldSkipRendering()) {
            return;
        }

        $githubService = app(GitHubService::class);

        if ($this->githubEnabled) {
            $githubService->enable();
        } else {
            $githubService->disable();
        }

        $startTime = $this->loadTimeEnabled ? microtime(true) : 0;

        $panel->renderHook(
            $this->getRenderHook(),
            fn (): string => $this->renderFooter($startTime)
        );
    }

    /**
     * Check if the footer rendering should be skipped
     */
    public function shouldSkipRendering(): bool
    {
        return $this->hideFromAuthPagesEnabled && $this->isOnAuthPage();
    }

    /**
     * Render the footer view
     */
    protected function renderFooter(float $startTime): string
    {
        return view('filament-easy-footer::easy-footer', [
            'footerPosition' => $this->footerPosition,
            'githubEnabled' => $this->githubEnabled,
            'showLogo' => $this->showLogo,
            'showUrl' => $this->showUrl,
            'borderTopEnabled' => $this->borderTopEnabled,
            'loadTime' => $this->loadTimeEnabled ? $this->calculateLoadTime($startTime) : false,
            'links' => $this->links,
        ])->render();
    }

    /**
     * Check if the current page is an auth page
     */
    protected function isOnAuthPage(): bool
    {
        return in_array(request()->path(), self::AUTH_PATHS, true);
    }

    /**
     * Calculate the page load time
     */
    protected function calculateLoadTime(float $startTime): string
    {
        return number_format(microtime(true) - $startTime, 3);
    }

    /**
     * Get the render hook based on footer position
     */
    public function getRenderHook(): string
    {
        return match ($this->footerPosition) {
            'sidebar' => 'panels::sidebar.nav.end',
            'sidebar.footer' => 'panels::sidebar.footer',
            default => 'panels::footer',
        };
    }

    /**
     * Configure whether to hide the footer from auth pages
     */
    public function hideFromAuthPages(bool $enabled = true): static
    {
        $this->hideFromAuthPagesEnabled = $enabled;

        return $this;
    }

    /**
     * Configure GitHub integration
     *
     * @param  bool  $showLogo  Whether to show the GitHub logo
     * @param  bool  $showUrl  Whether to show the GitHub URL
     */
    public function withGithub(bool $showLogo = true, bool $showUrl = true): static
    {
        $this->githubEnabled = true;
        $this->showLogo = $showLogo;
        $this->showUrl = $showUrl;

        return $this;
    }

    /**
     * Configure if the footer has a border
     */
    public function withBorder(bool $enabled = true): static
    {
        $this->borderTopEnabled = $enabled;

        return $this;
    }

    /**
     * Configure the footer position
     */
    public function withFooterPosition(string $position): static
    {
        $this->footerPosition = $position;

        return $this;
    }

    /**
     * Enable load time display
     */
    public function withLoadTime(bool $enabled = true): static
    {
        $this->loadTimeEnabled = $enabled;

        return $this;
    }

    /**
     * Add custom links to the footer
     *
     * @param  array  $links  Array of links with 'title' and 'url' keys
     */
    public function withLinks(array $links): static
    {
        $this->links = array_slice(
            array_filter($links, fn ($link) => isset($link['title'], $link['url'])),
            0,
            self::MAX_LINKS
        );

        return $this;
    }

    /**
     * Check if load time is enabled
     */
    public function isLoadTimeEnabled(): bool
    {
        return $this->loadTimeEnabled;
    }

    /**
     * Get the current links
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * Boot the plugin
     */
    public function boot(Panel $panel): void
    {
        //
    }

    /**
     * Create a new instance of the plugin
     */
    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * Get the current instance of the plugin
     */
    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
