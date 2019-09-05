<?php

namespace Tests;

use Hash;
use Nonetallt\Laravel\Permission\Providers\PermissionServiceProvider;
use Nonetallt\Helpers\Testing\Traits\TestsFiles;

trait CreatesApplication
{
    use TestsFiles;

    protected function getPackageProviders($app)
    {
        return [PermissionServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            /* 'Facade' => 'Package\Facade' */
        ];
    }

    public function setUp() : void
    {
        parent::setUp();

        /* Load migrations from the spatie permissions package */
        $this->loadMigrationsFrom([ 
            '--database' => 'sqlite',
            '--realpath' => $this->getTestPath('migrations') 
        ]);

        $this->artisan('migrate', ['--database' => 'sqlite']);
        $this->loadLaravelMigrations(['--database' => 'sqlite']);
        $this->withFactories(__DIR__.'/factories');

        /* Set config paths to base templates for testing purposes */
        config([
            PermissionServiceProvider::CONFIG_FILE_NAME . '.roles_path' => $this->getBasePath('src/templates/roles.json'),
            PermissionServiceProvider::CONFIG_FILE_NAME . '.permissions_path' => $this->getBasePath('src/templates/permissions.json'),
        ]);
    }

    /**
     * Creates the application.
     *
     * Needs to be implemented by subclasses.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = parent::createApplication();

        Hash::setRounds(4);

        return $app;
    }
}
