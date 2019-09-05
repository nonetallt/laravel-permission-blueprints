<?php

namespace Nonetallt\Laravel\Permission\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Nonetallt\Laravel\Permission\Contracts\PermissionsBlueprint;

class UpdatePermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:update {--Y|yes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the application permissions in the database';

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
        /* Load all the permissions that this application should have */
        $requiredPermissionNames = resolve(PermissionsBlueprint::class)->getPermissions();

        /* Load all the permissions that the application currently has */
        $currentPermissions = Permission::select(['id', 'name'])->get();
        $currentPermissionNames = $currentPermissions->map(function($permission) {
            return $permission->name;
        })->toArray();

        /* Find all the missing permissions that should be added */
        $missing = array_diff($requiredPermissionNames, $currentPermissionNames);

        /* Find all the extra permissions that should be removed */
        $extra = array_diff($currentPermissionNames, $requiredPermissionNames);

        if(empty($missing) && empty($extra)) {
            $this->info("Permissions are up to date");
            return;
        }

        $this->warn("\nChanges to be made:\n");
        $this->displayChangesTable($missing, $extra);

        if(! $this->option('yes')) {
            $question = 'Are you sure you wish to execute these changes?';
            if(! $this->confirm($question)) return; 
        }
        
        /* Add all permissions that do not exist yet */
        $this->addPermissions($missing);

        /* Remove all permissions that no longer exist */
        $this->removePermissions($extra);

        $this->info('Permissions updated');
    }

    private function displayChangesTable(array $missing, array $extra)
    {
        $tableHeaders = ['permission', 'operation'];
        $tableData = [];

        foreach($missing as $name) {
            $tableData[] = ['permission' => $name, 'operation' => '+'];
        }

        foreach($extra as $name) {
            $tableData[] = ['permission' => $name, 'operation' => '-'];
        }

        $this->table($tableHeaders, $tableData);
    }

    private function addPermissions(array $permissionNames)
    {
        foreach($permissionNames as $permissionName) {
            Permission::create(['name' => $permissionName]);
        }
    }

    private function removePermissions(array $permissionNames)
    {
        Permission::whereIn('name', $permissionNames)->delete();
    }
}
