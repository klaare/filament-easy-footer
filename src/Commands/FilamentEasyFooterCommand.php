<?php

namespace Devonab\FilamentEasyFooter\Commands;

use Illuminate\Console\Command;

class FilamentEasyFooterCommand extends Command
{
    public $signature = 'filament-easy-footer';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
