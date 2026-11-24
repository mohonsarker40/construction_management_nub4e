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
        $arrUser = [
            [
                'role_id'       => 1, // Admin
                'full_name'     => 'Mohon',
                'email'         => 'mohon@gmail.com',
                'phone'         => '01700000001',
                'status'        => 1,
                'password_hash' => '12345678',
            ],
            [
                'role_id'       => 2, // Project Manager
                'full_name'     => 'Rabby',
                'email'         => 'rabby@gmail.com',
                'phone'         => '01700000002',
                'status'        => 1,
                'password_hash' => '12345678',
            ],
            [
                'role_id'       => 3, // Site Supervisor
                'full_name'     => 'Site Supervisor',
                'email'         => 'supervisor@gmail.com',
                'phone'         => '01700000003',
                'status'        => 1,
                'password_hash' => '12345678',
            ],
            [
                'role_id'       => 4, // Designer
                'full_name'     => 'Shihab',
                'email'         => 'shihab@gmail.com',
                'phone'         => '01700000004',
                'status'        => 1,
                'password_hash' => '12345678',
            ],
            [
                'role_id'       => 5, // Procurement
                'full_name'     => 'Procurement Officer',
                'email'         => 'procurement@gmail.com',
                'phone'         => '01700000005',
                'status'        => 1,
                'password_hash' => '12345678',
            ],
            [
                'role_id'       => 6, // Store Keeper
                'full_name'     => 'Store Keeper',
                'email'         => 'store@gmail.com',
                'phone'         => '01700000006',
                'status'        => 1,
                'password_hash' => '12345678',
            ],
            [
                'role_id'       => 7, // Accountant
                'full_name'     => 'Shazid',
                'email'         => 'shazid@gmail.com',
                'phone'         => '01700000007',
                'status'        => 1,
                'password_hash' => '12345678',
            ],
            [
                'role_id'       => 8, // Client
                'full_name'     => 'jonaki',
                'email'         => 'jonaki@gmail.com',
                'phone'         => '01700000008',
                'status'        => 1,
                'password_hash' => '12345678',
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
                    'password_hash' => Hash::make($eachUser['password_hash']),
                ]
            );
        }

        $this->command->info('✅ Seeded ' . count($arrUser) . ' users (one per role). Password: "12345678"');
    }
}
