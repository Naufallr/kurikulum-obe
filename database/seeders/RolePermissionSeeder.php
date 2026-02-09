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
        $permissions = [
            'create-user',
            'edit-user',
            'delete-user',
            'read-user',
            'create-idpl',
            'create-deskpl',
            'delete-idpl',
            'delete-deskpl',
            'edit-idpl',
            'edit-deskpl',
            'create-idmk',
            'create-namamk',
            'create-jenismk',
            'create-statusmk',
            'delete-idmk',
            'delete-namamk',
            'delete-jenismk',
            'delete-statusmk',
            'edit-idmk',
            'edit-namamk',
            'edit-jenismk',
            'edit-statusmk',
            'create-idbk',
            'create-namabk',
            'create-kategoribk',
            'create-referensibk',
            'delete-idbk',
            'delete-namabk',
            'delete-kategoribk',
            'delete-referensibk',
            'edit-idbk',
            'edit-namabk',
            'edit-kategoribk',
            'edit-referensibk',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleUser = Role::firstOrCreate(['name' => 'user']);

        $roleAdmin->syncPermissions(Permission::all());

        $roleUserPermissions = [
            'create-user',
            'edit-user',
            'delete-user',
            'read-user',
            'create-idpl',
            'create-deskpl',
            'delete-idpl',
            'delete-deskpl',
            'edit-idpl',
            'edit-deskpl',
            'create-idmk',
            'create-namamk',
            'create-jenismk',
            'create-statusmk',
            'delete-idmk',
            'delete-namamk',
            'delete-jenismk',
            'delete-statusmk',
            'edit-idmk',
            'edit-namamk',
            'edit-jenismk',
            'edit-statusmk',
            'create-idbk',
            'create-namabk',
            'create-kategoribk',
            'create-referensibk',
            'delete-idbk',
            'delete-namabk',
            'delete-kategoribk',
            'delete-referensibk',
            'edit-idbk',
            'edit-namabk',
            'edit-kategoribk',
            'edit-referensibk',
        ];

        $roleUser->syncPermissions($roleUserPermissions);
    }
}
