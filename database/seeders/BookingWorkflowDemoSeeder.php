<?php

namespace Database\Seeders;

use App\Models\Booking\Booking;
use App\Models\Booking\BookingRequest;
use App\Models\Booking\ClientPayment;
use App\Models\Client\Client;
use App\Models\Finance\Expense;
use App\Models\Project\Project;
use App\Models\RBAC\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BookingWorkflowDemoSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            $roleData = [
                1 => ['Admin', 'Full system access'],
                2 => ['Project Manager', 'Manages assigned projects and booking requests'],
                3 => ['Site Supervisor', 'Site execution and attendance'],
                4 => ['Designer', 'Architect / Civil / Interior designer'],
                5 => ['Procurement', 'Coordinates purchasing tasks'],
                6 => ['Store Keeper', 'Inventory and material issue'],
                7 => ['Accountant', 'Verifies client payments and financial records'],
                8 => ['Client', 'Registers, requests flats, and views own payments'],
            ];
            foreach ($roleData as $id => [$roleName, $description]) {
                Role::updateOrCreate(['id' => $id], ['role_name' => $roleName, 'description' => $description]);
            }
            $roleIds = Role::whereIn('role_name', array_column($roleData, 0))->pluck('id', 'role_name');

            $userData = [
                'admin' => ['role' => 'Admin', 'name' => 'Admin Mohon', 'email' => 'admin@gmail.com', 'phone' => '01700002001'],
                'manager' => ['role' => 'Project Manager', 'name' => 'Manager Rabby', 'email' => 'manager@gmail.com', 'phone' => '01700002002'],
                'designer' => ['role' => 'Designer', 'name' => 'Designer Shihab', 'email' => 'designer@gmail.com', 'phone' => '01700002005'],
                'accountant' => ['role' => 'Accountant', 'name' => 'Accountant Shazid', 'email' => 'accountant@gmail.com', 'phone' => '01700002003'],
                'client' => ['role' => 'Client', 'name' => 'Client Hosanur', 'email' => 'client@gmail.com', 'phone' => '01700002004'],
                'client2' => ['role' => 'Client', 'name' => 'Client Jonaki', 'email' => 'client2@gmail.com', 'phone' => '01700002005'],
            ];
            $users = [];
            foreach ($userData as $key => $data) {
                $users[$key] = User::updateOrCreate(['email' => $data['email']], [
                    'role_id' => $roleIds[$data['role']],
                    'full_name' => $data['name'],
                    'phone' => $data['phone'],
                    'status' => 1,
                    'password_hash' => Hash::make('123456'),
                ]);
            }

            $client = Client::updateOrCreate(['email' => $users['client']->email], [
                'user_id' => $users['client']->id,
                'client_name' => $users['client']->full_name,
                'contact_person' => $users['client']->full_name,
                'phone' => $users['client']->phone,
                'address' => 'Dhaka, Bangladesh',
                'status' => 1,
            ]);
            $project = Project::updateOrCreate(['project_name' => 'Essos Residence'], [
                'client_id' => $client->id,
                'project_type' => 'Residential',
                'location' => 'Mirpur 10, Dhaka',
                'budget' => 12000000,
                'start_date' => now()->subMonths(2)->toDateString(),
                'estimated_end_date' => now()->addMonths(8)->toDateString(),
                'actual_end_date' => now()->subDays(3)->toDateString(),
                'available_units' => implode("\n", array_map(fn ($number) => 'A-'.$number.' | '.([101 => 3000000, 102 => 4000000, 103 => 5000000, 104 => 4000000][$number] ?? 3000000), range(101, 120))),
                'manager_user_id' => $users['manager']->id,
                'designer_user_id' => $users['designer']->id,
                'created_by' => $users['admin']->id,
            ]);

            $requestData = [
                ['request_no' => 'BR-PM-101', 'unit_label' => 'A-101', 'agreed_amount' => 3000000, 'initial_payment_amount' => 500000, 'status' => BookingRequest::PENDING_MANAGER],
                ['request_no' => 'BR-ACC-102', 'unit_label' => 'A-102', 'agreed_amount' => 4000000, 'initial_payment_amount' => 1000000, 'status' => BookingRequest::PENDING_ACCOUNTANT, 'manager_reviewed_by' => $users['manager']->id, 'manager_reviewed_at' => now()->subHours(2)],
                ['request_no' => 'BR-PART-103', 'unit_label' => 'A-103', 'agreed_amount' => 5000000, 'initial_payment_amount' => 1000000, 'status' => BookingRequest::APPROVED, 'manager_reviewed_by' => $users['manager']->id, 'manager_reviewed_at' => now()->subDays(5), 'accountant_reviewed_by' => $users['accountant']->id, 'accountant_reviewed_at' => now()->subDays(4)],
                ['request_no' => 'BR-FULL-104', 'unit_label' => 'A-104', 'agreed_amount' => 4000000, 'initial_payment_amount' => 4000000, 'status' => BookingRequest::APPROVED, 'manager_reviewed_by' => $users['manager']->id, 'manager_reviewed_at' => now()->subDays(4), 'accountant_reviewed_by' => $users['accountant']->id, 'accountant_reviewed_at' => now()->subDays(3)],
            ];

            foreach ($requestData as $data) {
                $request = BookingRequest::updateOrCreate(['request_no' => $data['request_no']], array_merge([
                    'project_id' => $project->id,
                    'client_id' => $client->id,
                    'unit_label' => $data['unit_label'],
                    'agreed_amount' => $data['agreed_amount'],
                    'initial_payment_amount' => $data['initial_payment_amount'],
                    'payment_method' => 'bank',
                    'payment_reference' => 'DEMO-'.$data['unit_label'],
                    'client_message' => 'Workflow demonstration request.',
                    'status' => $data['status'],
                ], $data));

                if ($request->status === BookingRequest::APPROVED) {
                    $booking = Booking::updateOrCreate(['booking_request_id' => $request->id], [
                        'project_id' => $project->id,
                        'client_id' => $client->id,
                        'unit_label' => $request->unit_label,
                        'agreed_amount' => $request->agreed_amount,
                        'booking_date' => now()->subDays(3)->toDateString(),
                        'status' => Booking::BOOKED,
                        'created_by' => $users['accountant']->id,
                    ]);
                    ClientPayment::updateOrCreate(['booking_id' => $booking->id, 'reference_no' => $request->payment_reference], [
                        'amount' => $request->initial_payment_amount,
                        'payment_date' => now()->subDays(3)->toDateString(),
                        'payment_method' => $request->payment_method,
                        'status' => ClientPayment::APPROVED,
                        'recorded_by' => $users['accountant']->id,
                        'approved_by' => $users['accountant']->id,
                        'approved_at' => now()->subDays(3),
                    ]);
                }
            }

            $partialBooking = Booking::where('project_id', $project->id)->where('unit_label', 'A-103')->firstOrFail();
            ClientPayment::updateOrCreate(['booking_id' => $partialBooking->id, 'reference_no' => 'DEMO-PART-103-SECOND'], [
                'amount' => 500000,
                'payment_date' => now()->subDay()->toDateString(),
                'payment_method' => 'mobile_banking',
                'status' => ClientPayment::PENDING,
                'recorded_by' => $users['accountant']->id,
                'approved_by' => null,
                'approved_at' => null,
            ]);

            Expense::updateOrCreate(['project_id' => $project->id, 'description' => 'Demo materials'], [
                'category' => Expense::MATERIALS, 'amount' => 1200000, 'expense_date' => now()->subDays(10)->toDateString(), 'recorded_by' => $users['manager']->id,
            ]);
            Expense::updateOrCreate(['project_id' => $project->id, 'description' => 'Demo labor'], [
                'category' => Expense::LABOR, 'amount' => 800000, 'expense_date' => now()->subDays(5)->toDateString(), 'recorded_by' => $users['manager']->id,
            ]);
        });

        $this->command->info('Booking workflow demo data is ready. All demo accounts use password: 123456');
        $this->command->line(
            'Admin: admin@gmail.com | 
            Manager: manager@gmail.com | 
            Designer: designer@gmail.com | 
            Accountant: accountant@gmail.com | 
            Client: client@gmail.com | 
            Client2: client2@gmail.com'
        );
    }
}
