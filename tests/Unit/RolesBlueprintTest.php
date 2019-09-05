<?php

namespace Tests\Unit;

use Tests\TestCase;
use Nonetallt\Laravel\Permission\Contracts\RolesBlueprint;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesBlueprintTest extends TestCase
{

    public function testBlueprintCanBeResolved()
    {
        $this->assertInstanceOf(RolesBlueprint::class, resolve(RolesBlueprint::class));
    }

    public function testBlueprintLoadsRolesCorrectly()
    {
        $expected = [
            [
                'name' => 'superadmin',
                'permissions' => [
                    'permission-1',
                    'permission-2',
                    'permission-3',
                    'permission-4',
                ]
            ],
            [
                'name' => 'admin',
                'permissions' => [
                    'permission-1',
                    'permission-2',
                    'permission-3',
                ],
            ],
            [
                'name' => 'user',
                'permissions' => [
                    'permission-1'
                ]
            ]
        ];

        /* Serialize loaded role collection recursively */
        $serialized = resolve(RolesBlueprint::class)->getRoles()->toArray(true);
        $this->assertEquals($expected, $serialized);
    }
}
