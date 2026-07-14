<?php

namespace Arjunyuvanesh\CommonAuth\Console;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class SeedRolesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'common-auth:seed-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the default roles required for Common Auth';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // 1. Fetch the dynamic default role from the config
        $defaultRole = config('common-auth.default_role', 'User');

        // 2. Ensure Spatie is actually installed and reachable
        if (!class_exists(Role::class)) {
            $this->error('Spatie Permission package is not fully installed or configured.');
            return;
        }

        // 3. Create the default role safely without duplicating it
        Role::firstOrCreate(['name' => $defaultRole, 'guard_name' => 'web']);
        $this->info("Role '{$defaultRole}' created or already exists.");

        // 4. Create an 'Admin' role as a helpful convenience for the host app
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $this->info("Role 'Admin' created or already exists.");

        $this->info('Common Auth Roles seeded successfully!');
    }
}
