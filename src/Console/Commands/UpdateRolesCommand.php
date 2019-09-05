<?php

namespace Nonetallt\Laravel\Permission\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Nonetallt\Laravel\Permission\Contracts\RolesBlueprint;
use Nonetallt\Laravel\Permission\Blueprint\RoleCollection;
use Illuminate\Support\Facades\Artisan;

class UpdateRolesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:update {--Y|yes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the application roles in the database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /* Make sure that permissions are up to date */
        $args = $this->option('yes') ? ['--yes' => true] : [];
        Artisan::call('permissions:update', $args);

        /* Load all the roles that this application should have */
        $requiredRoles = resolve(RolesBlueprint::class)->getRoles();

        /* Load all the roles that the application currently has */
        $currentRoles = Role::select(['id', 'name'])->get();

        $existing = $requiredRoles->getExistingItems($currentRoles);
        $missing = $requiredRoles->getMissingItems($currentRoles);
        $extra = $requiredRoles->getExtraItems($currentRoles);

        $this->warn("\nChanges to be made:\n");
        $this->displayChangesTable($missing, $extra, $existing);

        if(! $this->option('yes')) {
            $question = 'Are you sure you wish to execute these changes?';
            if(! $this->confirm($question)) return; 
        }
        
        /* Add all roles that do not exist yet */
        $this->addRoles($missing);

        /* Remove all roles that no longer exist */
        $this->removeRoles($extra);

        /* Update permissions for all old roles */
        $this->updatePermissions($requiredRoles, $existing);

        $this->info('Roles updated');
    }

    private function displayChangesTable($missing, $extra, $existing)
    {
        $tableHeaders = ['role', 'operation'];
        $tableData = [];

        foreach($missing as $role) {
            $tableData[] = ['role' => $role->getName(), 'operation' => '+'];
        }

        foreach($extra as $role) {
            $tableData[] = ['role' => $role->getName(), 'operation' => '-'];
        }

        foreach($existing as $role) {
            $tableData[] = ['role' => $role->getName(), 'operation' => '~'];
        }

        $this->table($tableHeaders, $tableData);
    }

    private function addRoles(RoleCollection $roles)
    {
        foreach($roles as $role) {
            $model = Role::create(['name' => $role->getName()]);

            /* Give permissions for the role */
            foreach($role->getPermissions() as $permission) {
                $model->givePermissionTo($permission);
            }
        }
    }

    private function removeRoles(RoleCollection $roles)
    {
        $roleNames = $roles->map(function($role) {
            return $role->getName();
        });

        Role::whereIn('name', $roleNames)->delete();
    }

    private function updatePermissions(RoleCollection $required, RoleCollection $existing)
    {
        foreach($existing as $role) {

            $model = Role::where('name', $role->getName())->first();
            $expectedPermissions = $required->getItemWithName($role->getName())->getPermissions();

            /* Revoke permissions that this role no longer has */
            foreach($role->getPermissions() as $permission) {
                if(! in_array($permission, $expectedPermissions)) $model->revokePermissionTo($permission);
            }

            /* Add new permissions for this role */
            foreach($expectedPermissions as $permission) {
                if(! in_array($permission, $role->getPermissions())) $model->givePermissionTo($permission);
            }
        }
    }
}
