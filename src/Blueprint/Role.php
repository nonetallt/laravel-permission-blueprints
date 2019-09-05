<?php

namespace Nonetallt\Laravel\Permission\Blueprint;

use Nonetallt\Helpers\Arrays\TypedArray;
use Nonetallt\Helpers\Arrays\Traits\ConstructedFromArray;
use Nonetallt\Laravel\Permission\Contracts\PermissionsBlueprint;
use Spatie\Permission\Models\Role as Model;
use Nonetallt\Laravel\Permission\Exceptions\PermissionException;
use Nonetallt\Helpers\Describe\DescribeObject;

class Role
{
    use ConstructedFromArray;

    private $name;
    private $permissions;

    public function __construct(string $name, $permissions)
    {
        $this->setName($name);
        $this->setPermissions($permissions);
    }

    public static function fromModel(Model $model)
    {
        $name = $model->name;

        $permissions = $model->permissions->map(function($permission) {
            return $permission->name;
        })->toArray();

        return new self($name, $permissions);
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function setPermissions($permissions)
    {
        if(is_array($permissions)) {
            $permissions = TypedArray::create('string', $permissions);
        }
        elseif(strtolower($permissions) === 'all') {
            $permissions = resolve(PermissionsBlueprint::class)->getPermissions();
        }
        else {
            $type = (new DescribeObject($permissions))->describeType();
            $msg = "Permissions must be either an array or 'all', $type given";
            throw new \InvalidArgumentException($msg);
        }

        $validPermissions = resolve(PermissionsBlueprint::class)->getPermissions();

        foreach($permissions as $permission) {
            if(! in_array($permission, $validPermissions)) {
                $msg = "Permission '$permission' for role '$this->name' does not exist in the current permissions blueprint";
                throw new PermissionException($msg);
            }
        }

        $this->permissions = $permissions;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getPermissions() : array
    {
        return $this->permissions;
    }

    public function toArray()
    {
        return [
            'name' => $this->name,
            'permissions' => $this->permissions
        ];
    }
}
