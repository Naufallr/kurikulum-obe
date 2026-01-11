<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'create-user']);
        Permission::create(['name' => 'edit-user']);
        Permission::create(['name' => 'delete-user']);
        Permission::create(['name' => 'read-user']);

        Permission::create(['name' => 'create-idpl']);
        Permission::create(['name' => 'create-deskpl']);
        Permission::create(['name' => 'delete-idpl']);
        Permission::create(['name' => 'delete-deskpl']);
        Permission::create(['name' => 'edit-idpl']);
        Permission::create(['name' => 'edit-deskpl']);

        Permission::create(['name' => 'create-idmk']);
        Permission::create(['name' => 'create-namamk']);
        Permission::create(['name' => 'create-jenismk']);
        Permission::create(['name' => 'create-statusmk']);
        Permission::create(['name' => 'delete-idmk']);
        Permission::create(['name' => 'delete-namamk']);
        Permission::create(['name' => 'delete-jenismk']);
        Permission::create(['name' => 'delete-statusmk']);
        Permission::create(['name' => 'edit-idmk']);
        Permission::create(['name' => 'edit-namamk']);
        Permission::create(['name' => 'edit-jenismk']);
        Permission::create(['name' => 'edit-statusmk']);

        Permission::create(['name' => 'create-idbk']);
        Permission::create(['name' => 'create-namabk']);
        Permission::create(['name' => 'create-kategoribk']);
        Permission::create(['name' => 'create-referensibk']);
        Permission::create(['name' => 'delete-idbk']);
        Permission::create(['name' => 'delete-namabk']);
        Permission::create(['name' => 'delete-kategoribk']);
        Permission::create(['name' => 'delete-referensibk']);
        Permission::create(['name' => 'edit-idbk']);
        Permission::create(['name' => 'edit-namabk']);
        Permission::create(['name' => 'edit-kategoribk']);
        Permission::create(['name' => 'edit-referensibk']);

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);

        $roleAdmin = Role::findByName('admin');
        $roleAdmin->givePermissionTo( 'create-user');
        $roleAdmin->givePermissionTo('admin', 'edit-user');
        $roleAdmin->givePermissionTo('admin', 'delete-user');
        $roleAdmin->givePermissionTo('admin', 'read-user');

        $roleUser = Role::findByName('user');
        $roleUser->givePermissionTo('create-user');
        $roleUser->givePermissionTo('edit-user');
        $roleUser->givePermissionTo('delete-user');
        $roleUser->givePermissionTo('read-user');

        $roleUser->givePermissionTo('create-idpl');
        $roleUser->givePermissionTo('create-deskpl');
        $roleUser->givePermissionTo('delete-idpl');
        $roleUser->givePermissionTo('delete-deskpl');
        $roleUser->givePermissionTo('edit-idpl');
        $roleUser->givePermissionTo('edit-deskpl');

        $roleUser->givePermissionTo('create-idmk');
        $roleUser->givePermissionTo('create-namamk');
        $roleUser->givePermissionTo('create-jenismk');
        $roleUser->givePermissionTo('create-statusmk');
        $roleUser->givePermissionTo('delete-idmk');
        $roleUser->givePermissionTo('delete-namamk');
        $roleUser->givePermissionTo('delete-jenismk');
        $roleUser->givePermissionTo('delete-statusmk');
        $roleUser->givePermissionTo('edit-idmk');
        $roleUser->givePermissionTo('edit-namamk');
        $roleUser->givePermissionTo('edit-jenismk');
        $roleUser->givePermissionTo('edit-statusmk');

        $roleUser->givePermissionTo('create-idbk');
        $roleUser->givePermissionTo('create-namabk');
        $roleUser->givePermissionTo('create-kategoribk');
        $roleUser->givePermissionTo('create-referensibk');
        $roleUser->givePermissionTo('delete-idbk');
        $roleUser->givePermissionTo('delete-namabk');
        $roleUser->givePermissionTo('delete-kategoribk');
        $roleUser->givePermissionTo('delete-referensibk');
        $roleUser->givePermissionTo('edit-idbk');
        $roleUser->givePermissionTo('edit-namabk');
        $roleUser->givePermissionTo('edit-kategoribk');
        $roleUser->givePermissionTo('edit-referensibk');
    }
}
