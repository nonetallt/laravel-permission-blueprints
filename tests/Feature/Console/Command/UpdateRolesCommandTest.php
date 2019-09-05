<?php 
namespace Tests\Feature\Console\Commands;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;
use Nonetallt\Laravel\Permission\Contracts\RolesBlueprint;
use Spatie\Permission\Models\Permission;

class UpdateRolesCommandTest extends TestCase
{
    use RefreshDatabase;

    private $roles;

    public function setUp() : void
    {
        parent::setUp();

        $this->roles = resolve(RolesBlueprint::class)->getRoles();
    }

    /** 
     * Make sure that there are at least some roles in the json file 
     */
    public function testRolesFileHasSomeRoles()
    {
        $this->assertFalse($this->roles->isEmpty());
    }

    public function testCommandRemovesExtraRoles()
    {
        Role::create(['name' => 'foobar']);
        Artisan::call('roles:update', ['--yes' => true]);

        $expected = $this->roles->map(function($role) {
            return $role->getName();
        });

        $roleNames = Role::get()->map(function($role) {
            return $role->name;
        })->toArray();

        $this->assertEquals($expected, $roleNames);
    }

    public function testCommandAddsMissingPermissionsForExistingRoles()
    {
        Role::create(['name' => 'admin']);
        Artisan::call('roles:update', ['--yes' => true]);

        $expected = $this->roles->getItemWithName('admin')->getPermissions();

        $adminRole = Role::where('name', 'admin')->first();
        $adminPermissions = $adminRole->permissions->map(function($permission) {
            return $permission->name;
        })->toArray();

        $this->assertEquals($expected, $adminPermissions);
    }

    public function testCommandRemovesPermissionsThatNoLongerExistForRoles()
    {
        $role = Role::create(['name' => 'admin']);
        $permissions = [];

        for($n = 1; $n <= 4; $n++) {
            $permissionName = "permission-$n";
            $permissions[] =  $permissionName;
            Permission::create(['name' => $permissionName]);
            $role->givePermissionTo($permissionName);
        }

        Artisan::call('roles:update', ['--yes' => true]);

        /* Expect that admin role has permissions 1 2 and 3 */
        $expected = array_slice($permissions, 0, 3);

        $adminRole = Role::where('name', 'admin')->first();
        $adminPermissions = $adminRole->permissions->map(function($permission) {
            return $permission->name;
        })->toArray();

        $this->assertEquals($expected, $adminPermissions);
    }
}
