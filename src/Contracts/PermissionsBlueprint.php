<?php

namespace Nonetallt\Laravel\Permission\Contracts;

interface PermissionsBlueprint
{
    public function getPermissions() : array;
}
