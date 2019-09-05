<?php

namespace Nonetallt\Laravel\Permission\Blueprint;

use Nonetallt\Helpers\Filesystem\Json\JsonParser;
use Nonetallt\Laravel\Permission\Providers\ServiceProvider;
use Nonetallt\Laravel\Permission\Contracts\PermissionsBlueprint;

class JsonPermissionsBlueprint implements PermissionsBlueprint
{
    private $parser;
    private $permissions;

    public function __construct(string $filepath)
    {
        $this->parser = new JsonParser();
        $this->permissions = $this->loadPermissions($filepath);
    }

    /**
     * @throws Nonetallt\Helpers\Filesystem\Json\Exceptions\JsonParsingException
     */ 
    private function loadPermissions(string $filepath) : array
    {
        $parser = new JsonParser();
        return $parser->decodeFile($filepath);
    }

    public function getPermissions() : array
    {
        return $this->permissions;
    }
}
