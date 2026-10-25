<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{

    public function run()
    {
        User::truncate();

        $arrUser = [
            [
                'role_id'       => 1, // Admin
                'full_name'     => 'System Admin',
                'email'         => 'admin@gmail.com',
                'phone'         => '0000000000',
                'status'        => 1,
                'show_password' => 'password',
            ],
            [
                'role_id'       => 2, // Project Manager
                'full_name'     => 'Project Manager',
                'email'         => 'pm@gmail.com',
                'phone'         => '01700000001',
                'status'        => 1,
                'show_password' => 'password',
            ],
            [
                'role_id'       => 3, // Site Supervisor
                'full_name'     => 'Site Supervisor',
                'email'         => 'sv@gmail.com',
                'phone'         => '01700000002',
                'status'        => 1,
                'show_password' => 'password',
            ],
            [
                'role_id'       => 4, // Designer
                'full_name'     => 'Designer',
                'email'         => 'designer@gmail.com',
                'phone'         => '01700000003',
                'status'        => 1,
                'show_password' => 'password',
            ],
            [
                'role_id'       => 5, // Procurement
                'full_name'     => 'Procurement Officer',
                'email'         => 'procurement@gmail.com',
                'phone'         => '01700000004',
                'status'        => 1,
                'show_password' => 'password',
            ],
            [
                'role_id'       => 6, // Store Keeper
                'full_name'     => 'Store Keeper',
                'email'         => 'store@gmail.com',
                'phone'         => '01700000005',
                'status'        => 1,
                'show_password' => 'password',
            ],
            [
                'role_id'       => 7, // Accountant
                'full_name'     => 'Accountant',
                'email'         => 'accounts@gmail.com',
                'phone'         => '01700000006',
                'status'        => 1,
                'show_password' => 'password',
            ],
            [
                'role_id'       => 8, // Client
                'full_name'     => 'Demo Client',
                'email'         => 'client@gmail.com',
                'phone'         => '01700000007',
                'status'        => 1,
                'show_password' => 'password',
            ],
        ];


        foreach ($arrUser as $eachUser) {
            User::updateOrCreate(
                ['email' => $eachUser['email']],
                [
                    'role_id'       => $eachUser['role_id'],
                    'full_name'     => $eachUser['full_name'],
                    'phone'         => $eachUser['phone'],
                    'status'        => $eachUser['status'],
                    'password_hash' => Hash::make($eachUser['show_password']),
                ]
            );
        }

        $this->command->info('✅ Seeded ' . count($arrUser) . ' users (one per role). Password: "password"');
    }
}
