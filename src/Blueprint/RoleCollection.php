<?php

namespace Nonetallt\Laravel\Permission\Blueprint;

use Nonetallt\Helpers\Generic\SerializableCollection;
use Illuminate\Database\Eloquent\Collection;

class RoleCollection extends SerializableCollection
{
    public function __construct(array $data = [])
    {
        parent::__construct($data, Role::class);
    }

    public function getItemWithName(string $name)
    {
        $items = $this->filter(function($item) use($name) {
            return $item->getName() === $name;
        });

        return $items[0] ?? null;
    }

    public function hasItemWithName(string $name)
    {
        return $this->getItemWithName($name) !== null;
    }

    /**
     * Get items that are in this and the given collection
     */
    public function getExistingItems(Collection $collection) : RoleCollection
    {
        $items = new RoleCollection();

        foreach($collection as $model) {
            if($this->hasItemWithName($model->name)) {
                $items->push(Role::fromModel($model));
            }
        }

        return $items;
    }

    /**
     * Get items that are in the given collection but not in this collection
     */
    public function getExtraItems(Collection $collection) : RoleCollection
    {
        $items = new RoleCollection();

        foreach($collection as $model) {
            if(! $this->hasItemWithName($model->name)) {
                $items->push(Role::fromModel($model));
            }
        }

        return $items;
    }

    /**
     * Get items that are in this collection but not in the given collection
     */
    public function getMissingItems(Collection $collection) : RoleCollection
    {
        $items = new RoleCollection();

        foreach($this as $role) {
            $item = $collection->first(function($model) use($role) {
                return $model->name === $role->getName();
            });

            if($item === null) $items->push( $role);
        }

        return $items;
    }
}
