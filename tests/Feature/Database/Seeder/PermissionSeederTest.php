<?php

namespace Tests\Feature\Database\Seeder;

use Tests\TestCase;
use Spatie\Permission\Models\Permission;
use Nonetallt\Laravel\Permission\Contracts\PermissionsBlueprint;
use Illuminate\Support\Facades\Artisan;
use PermissionSeeder;

class PermissionSeederTest extends TestCase
{
    private $permissions;

    public function setUp() : void
    {
        parent::setUp();
        $this->permissions = resolve(PermissionsBlueprint::class)->getPermissions();
    }

    /**
     * Make sure that the testing heppens with clean database
     */
    public function testPermissionsDoNotExistBeforeSeeding()
    {
        $this->assertEquals(0, Permission::count());
    }

    public function testPermissionSeederCreatesAllPermissions()
    {
        Artisan::call('db:seed', ['--class' => PermissionSeeder::class]);

        $permissionNames = Permission::get()->map(function($permission) {
            return $permission->name;
        })->toArray();

        $this->assertEquals($this->permissions, $permissionNames);
    }
}
