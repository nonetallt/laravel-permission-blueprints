<?php

namespace Tests\Feature\Console\Commands;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Nonetallt\Laravel\Permission\Contracts\PermissionsBlueprint;

class UpdatePermissionsCommandTest extends TestCase
{
    private $permissions;

    public function setUp() : void
    {
        parent::setUp();
        $this->permissions = resolve(PermissionsBlueprint::class)->getPermissions();
    }

    /** 
     * Make sure that there are at least some permissions in the json file 
     */
    public function testPermissionsFileHasSomePermissions()
    {
        $this->assertNotEmpty($this->permissions);
    }

    public function testCommandRemovesExtraPermissions()
    {
        Permission::create(['name' => 'foobar']);
        Artisan::call('permissions:update', ['--yes' => true]);

        $permissionNames = Permission::get()->map(function($permission) {
            return $permission->name;
        })->toArray();

        $this->assertEquals($this->permissions, $permissionNames);
    }
}
