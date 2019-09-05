<?php

namespace Nonetallt\Laravel\Permission\Contracts;

use Nonetallt\Laravel\Permission\Blueprint\RoleCollection;

interface RolesBlueprint
{
    public function getRoles() : RoleCollection;
}
