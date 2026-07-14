<?php

namespace Arjunyuvanesh\CommonAuth\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Arjunyuvanesh\CommonAuth\Providers\CommonAuthServiceProvider;
use Spatie\Permission\PermissionServiceProvider;

class TestCase extends Orchestra
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // This command runs the default Laravel migrations (like the users table)
        // entirely in memory (SQLite) so we can test our package's migrations against it!
        $this->loadLaravelMigrations();
    }

    /**
     * Load the package service providers for testing.
     * This mimics a host Laravel application registering our package.
     */
    protected function getPackageProviders($app)
    {
        return [
            PermissionServiceProvider::class,
            CommonAuthServiceProvider::class,
        ];
    }

    /**
     * Set up the environment configuration for testing.
     */
    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
