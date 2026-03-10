<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $admin = User::firstOrCreate(
            ['name' => 'admin'],
            ['password' => Hash::make('12345')]
        );
        $admin->assignRole('admin');

        $user = User::firstOrCreate(
            ['name' => 'user'],
            ['password' => Hash::make('12345')]
        );
        $user->assignRole('user');
    }
}
