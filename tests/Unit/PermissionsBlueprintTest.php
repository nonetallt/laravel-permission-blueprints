<?php

namespace Tests\Unit;

use Tests\TestCase;
use Nonetallt\Laravel\Permission\Contracts\PermissionsBlueprint;

class PermissionsBlueprintTest extends TestCase
{

    public function testBlueprintCanBeResolved()
    {
        $this->assertInstanceOf(PermissionsBlueprint::class, resolve(PermissionsBlueprint::class));
    }

    public function testBlueprintLoadsPermissionsCorrectly()
    {
        $expected = [
            'permission-1',
            'permission-2',
            'permission-3',
            'permission-4',
        ];

        $this->assertEquals($expected, resolve(PermissionsBlueprint::class)->getPermissions());
    }
}
