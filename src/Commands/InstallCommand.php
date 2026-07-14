<?php

namespace Arjunyuvanesh\CommonAuth\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'common-auth:install';

    protected $description = 'Install the Common Auth package';

    public function handle()
    {
        $this->info('Installing Common Auth...');

        $this->info('Publishing configuration...');
        
        $this->call('vendor:publish', [
            '--provider' => 'Arjunyuvanesh\CommonAuth\CommonAuthServiceProvider',
            '--tag' => 'common-auth-config'
        ]);

        $this->info('Publishing migrations...');
        
        $this->call('vendor:publish', [
            '--provider' => 'Arjunyuvanesh\CommonAuth\CommonAuthServiceProvider',
            '--tag' => 'common-auth-migrations'
        ]);

        $this->info('Common Auth installed successfully.');
    }
}
