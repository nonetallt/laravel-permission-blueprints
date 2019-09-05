<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

/**
 * Simply run the update command to make sure that the correct permissions
 * exist.
 *
 */
class PermissionSeeder extends Seeder
{
    /**
     * Create all permissions used by the application.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call('permissions:update', ['--yes' => true]);
    }
}
