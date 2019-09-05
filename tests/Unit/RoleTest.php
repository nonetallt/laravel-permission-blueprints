<?php

namespace Tests\Unit;

use Tests\TestCase;
use Nonetallt\Laravel\Permission\Contracts\RolesBlueprint;
use Spatie\Permission\Models\Role as Model;
use Spatie\Permission\Models\Permission;
use Nonetallt\Laravel\Permission\Blueprint\Role;
use Nonetallt\Laravel\Permission\Exceptions\PermissionException;

class RoleTest extends TestCase
{
    public function testTryingToUsePermissionNameThatDoesNotExistInThePermissionsBlueprintThrowsException()
    {
        $this->expectException(PermissionException::class);
        $role = new Role('foo', ['bar']);
    }
}
