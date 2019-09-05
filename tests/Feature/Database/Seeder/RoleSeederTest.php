<?php

namespace Tests\Feature\Database\Seeder;

use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Artisan;
use RoleSeeder;
use Nonetallt\Laravel\Permission\Contracts\RolesBlueprint;

class RoleSeederTest extends TestCase
{
    private $roles;

    public function setUp() : void
    {
        parent::setUp();

        $this->roles = resolve(RolesBlueprint::class)->getRoles();
    }

    /**
     * Make sure that the testing heppens with clean database
     */
    public function testRolesDoNotExistBeforeSeeding()
    {
        $this->assertEquals(0, Role::count());
    }

    public function testRoleSeederCreatesAllRoles()
    {
        Artisan::call('db:seed', ['--class' => RoleSeeder::class]);

        $expected = $this->roles->map(function($role) {
            return $role->getName();
        });

        $roleNames = Role::get()->map(function($role) {
            return $role->name;
        })->toArray();

        $this->assertEquals($expected, $roleNames);
    }

    public function testCreatedRolesHavePermissions()
    {
        Artisan::call('db:seed', ['--class' => RoleSeeder::class]);

        $expected = [];
        foreach($this->roles as $role) {
            $expected[$role->getName()] = $role->getPermissions();
        }

        $actual = [];
        foreach(Role::get() as $role) {
            $actual[$role->name] = $role->permissions->map(function($permission) {
                return $permission->name;
            })->toArray();
        }

        $this->assertEquals($expected, $actual);
    }
}
