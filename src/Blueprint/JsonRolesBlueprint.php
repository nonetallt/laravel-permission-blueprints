<?php

namespace Nonetallt\Laravel\Permission\Blueprint;

use Nonetallt\Helpers\Filesystem\Json\JsonParser;
use Nonetallt\Laravel\Permission\Providers\ServiceProvider;
use Nonetallt\Laravel\Permission\Contracts\RolesBlueprint;
use Nonetallt\Laravel\Permission\Blueprint\RoleCollection;

class JsonRolesBlueprint implements RolesBlueprint
{
    private $parser;
    private $roles;

    public function __construct(string $filepath)
    {
        $this->parser = new JsonParser();
        $this->roles = $this->loadRoles($filepath);
    }

    /**
     * @throws Nonetallt\Helpers\Filesystem\Json\Exceptions\JsonParsingException
     */ 
    private function loadRoles(string $filepath) : RoleCollection
    {
        $parser = new JsonParser();
        $roles = new RoleCollection();

        foreach($parser->decodeFile($filepath, true) as $role) {
            $roles->push($this->createRole($role));
        }

        return $roles;
    }


    public function getRoles() : RoleCollection
    {
        return $this->roles;
    }

    private function createRole(array $data)
    {
        $name = $data['name'];
        $permissions = $data['permissions'];
        return Role::fromArray($data);
    }
}
