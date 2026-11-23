<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{

    public function run()
    {
        $roles = [
            ['id' => 1, 'role_name' => 'Admin',          'description' => 'Full system access'],
            ['id' => 2, 'role_name' => 'Project Manager','description' => 'Manages projects and teams'],
            ['id' => 3, 'role_name' => 'Site Supervisor','description' => 'Site execution and attendance'],
            ['id' => 4, 'role_name' => 'Designer',       'description' => 'Architect / Civil / Interior designer'],
            ['id' => 5, 'role_name' => 'Procurement',    'description' => 'Coordinates purchasing tasks'],
            ['id' => 6, 'role_name' => 'Store Keeper',   'description' => 'Inventory and material issue'],
            ['id' => 7, 'role_name' => 'Accountant',     'description' => 'Expenses, payments, reports'],
            ['id' => 8, 'role_name' => 'Client',         'description' => 'Register, request flats, and view own payments'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['id' => $role['id']], $role);
        }
    }
}
