<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

/**
 * Simply run the update command to make sure that the correct permissions
 * exist.
 *
 */
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call('roles:update', ['--yes' => true]);
    }
}
