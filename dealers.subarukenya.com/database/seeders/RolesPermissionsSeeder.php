<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        
        Permission::create(['name' => 'create products']);
        Permission::create(['name' => 'view products']);
        Permission::create(['name' => 'edit products']);
        Permission::create(['name' => 'delete products']);

        // create roles and assign created permissions

        // this can be done as separate statements
        $role = Role::create(['name' => 'dealer']);
        $role->givePermissionTo('view products');

        // or may be done by chaining
        $role = Role::create(['name' => 'administrator'])
            ->givePermissionTo(['create products', 'view products', 'edit products', 'delete products']);

        $role = Role::create(['name' => 'superadmin']);
        $role->givePermissionTo(Permission::all());

        User::where('email','help@samnduati.com')->first()->assignRole('superadmin');
        User::where('email','helpdesk@subarukenya.com')->first()->assignRole('administrator');
    }
}
