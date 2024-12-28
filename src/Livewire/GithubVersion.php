<?php

namespace Devonab\FilamentEasyFooter\Livewire;

use Devonab\FilamentEasyFooter\Services\GitHubService;
use Livewire\Component;

class GitHubVersion extends Component
{
    public bool $showLogo = true;

    public bool $showUrl = true;

    public ?string $version = null;

    public ?string $repository = null;

    public function mount(): void
    {
        if (! config('easy-footer.github.enabled')) {
            return;
        }

        $this->repository = config('easy-footer.github.repository');
        $this->version = app(GitHubService::class)->getLatestTag($this->repository);
    }

    public function getGithubUrl(): string
    {
        return "https://github.com/{$this->repository}";
    }

    public function render()
    {
        return view('filament-easy-footer::github-version');
    }
}
