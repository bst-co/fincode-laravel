<?php

namespace Fincode\Laravel\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'fincode:publish', description: 'Publish the Laravel Fincode files.')]
class PublishCommand extends Command
{
    protected $signature = 'fincode:publish';

    protected $description = 'Publish the Laravel Fincode files.';

    public function handle(): void
    {
        $this->call('vendor:publish', ['--tag' => 'fincode-config']);
        $this->call('vendor:publish', ['--tag' => 'fincode-migrations']);
    }
}
