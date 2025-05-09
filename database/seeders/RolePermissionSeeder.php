<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $adminRole = Role::create([
            'name' => 'admin'
        ]);

        $traineeRole = Role::create([
            'name' => 'trainee'
        ]);

        $trainerRole = Role::create([
            'name' => 'trainer'
        ]);

        //akun admin

        $userAdmin = User::create([
            'name' => 'admin',
            'pekerjaan' => 'admin',
            'avatar' => 'images/default-avatar.png',
            'email' => 'admin@admin.com',
            'password' => bcrypt('123123123'),

        ]);

        $userAdmin->assignRole($adminRole);
    }
}
