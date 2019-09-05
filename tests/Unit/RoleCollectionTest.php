<?php

namespace Tests\Unit;

use Tests\TestCase;
use Nonetallt\Laravel\Permission\Contracts\RolesBlueprint;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleCollectionTest extends TestCase
{
    public function testGetExistingItems()
    {
        $expected = [
            [
                'name' => 'admin',
                'permissions' => [
                    'permission-1'
                ]
            ]
        ];

        Permission::create(['name' => 'permission-1']);
        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo('permission-1');

        /* Serialize loaded role collection recursively */
        $roles = resolve(RolesBlueprint::class)->getRoles();
        $serialized = $roles->getExistingItems(Role::get())->toArray(true);

        $this->assertEquals($expected, $serialized);
    }

    public function testGetExtraItems()
    {
        $expected = [
            [
                'name' => 'test_role',
                'permissions' => []
            ]
        ];

        $role = Role::create(['name' => 'admin']);
        $role = Role::create(['name' => 'test_role']);

        /* Serialize loaded role collection recursively */
        $roles = resolve(RolesBlueprint::class)->getRoles();
        $serialized = $roles->getExtraItems(Role::get())->toArray(true);

        $this->assertEquals($expected, $serialized);
    }

    public function testGetMissingItems()
    {
        $expected = [
            [
                'name' => 'user',
                'permissions' => [
                    'permission-1'
                ]
            ]
        ];

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'superadmin']);

        /* Serialize loaded role collection recursively */
        $roles = resolve(RolesBlueprint::class)->getRoles();
        $serialized = $roles->getMissingItems(Role::get())->toArray(true);

        $this->assertEquals($expected, $serialized);

    }
}
