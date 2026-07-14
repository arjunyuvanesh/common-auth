<?php

namespace Arjunyuvanesh\CommonAuth\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'common-auth:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Common Auth package';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info('Installing Common Auth...');

        $this->info('Publishing configuration...');
        
        $this->call('vendor:publish', [
            '--provider' => 'Arjunyuvanesh\CommonAuth\Providers\CommonAuthServiceProvider',
            '--tag'      => 'common-auth-config'
        ]);

        $this->info('Publishing migrations...');
        
        $this->call('vendor:publish', [
            '--provider' => 'Arjunyuvanesh\CommonAuth\Providers\CommonAuthServiceProvider',
            '--tag'      => 'common-auth-migrations'
        ]);

        $this->info('Common Auth installed successfully.');
    }
}
