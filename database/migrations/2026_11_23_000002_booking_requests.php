<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('booking_requests')) {
            Schema::create('booking_requests', function (Blueprint $table) {
                $table->id();
                $table->string('request_no', 24)->unique();
                $table->foreignId('project_id')->constrained('projects');
                $table->foreignId('client_id')->constrained('clients');
                $table->string('unit_label', 50);
                $table->decimal('agreed_amount', 12, 2);
                $table->decimal('initial_payment_amount', 12, 2);
                $table->string('payment_method', 30);
                $table->string('payment_reference', 100)->nullable();
                $table->text('client_message')->nullable();
                $table->string('status', 30)->default('pending_manager');
                $table->foreignId('manager_reviewed_by')->nullable()->constrained('users');
                $table->timestamp('manager_reviewed_at')->nullable();
                $table->foreignId('accountant_reviewed_by')->nullable()->constrained('users');
                $table->timestamp('accountant_reviewed_at')->nullable();
                $table->string('rejection_note')->nullable();
                $table->timestamps();
                $table->index(['project_id', 'unit_label', 'status']);
            });
        }

        if (! Schema::hasColumn('bookings', 'request_no')) {
            return;
        }

        foreach (DB::table('bookings')->whereNotNull('request_no')->orderBy('id')->get() as $row) {
            $status = [
                'requested' => 'pending_manager',
                'manager_approved' => 'pending_accountant',
                'approved' => 'approved',
                'rejected' => 'rejected',
            ][$row->type ?? 'requested'] ?? 'pending_manager';

            $requestId = DB::table('booking_requests')->where('request_no', $row->request_no)->value('id');
            $values = [
                'request_no' => $row->request_no,
                'project_id' => $row->project_id,
                'client_id' => $row->client_id,
                'unit_label' => $row->unit_label,
                'agreed_amount' => $row->agreed_amount,
                'initial_payment_amount' => $row->initial_payment_amount ?? $row->agreed_amount,
                'payment_method' => $row->payment_method ?? 'bank',
                'payment_reference' => $row->payment_reference ?? null,
                'client_message' => $row->client_message ?? null,
                'status' => $status,
                'manager_reviewed_by' => $row->manager_reviewed_by ?? null,
                'manager_reviewed_at' => $row->manager_reviewed_at ?? null,
                'accountant_reviewed_by' => $row->accountant_reviewed_by ?? null,
                'accountant_reviewed_at' => $row->accountant_reviewed_at ?? null,
                'rejection_note' => $row->rejection_note ?? null,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ];
            if ($requestId) {
                DB::table('booking_requests')->where('id', $requestId)->update($values);
            } else {
                $requestId = DB::table('booking_requests')->insertGetId($values);
            }

            if ($status === 'approved') {
                if (! Schema::hasColumn('bookings', 'booking_request_id')) {
                    Schema::table('bookings', function (Blueprint $table) {
                        $table->foreignId('booking_request_id')->nullable()->unique()->constrained('booking_requests');
                    });
                }
                DB::table('bookings')->where('id', $row->id)->update(['booking_request_id' => $requestId]);
            } else {
                DB::table('bookings')->where('id', $row->id)->delete();
            }
        }

        if (! Schema::hasColumn('bookings', 'booking_request_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->foreignId('booking_request_id')->nullable()->unique()->constrained('booking_requests');
            });
        }

        $extraColumns = ['request_no', 'type', 'flat_id', 'initial_payment_amount', 'payment_method', 'payment_reference', 'client_message', 'manager_reviewed_by', 'manager_reviewed_at', 'accountant_reviewed_by', 'accountant_reviewed_at', 'rejection_note'];
        foreach (['flat_id', 'manager_reviewed_by', 'accountant_reviewed_by'] as $foreignColumn) {
            if (Schema::hasColumn('bookings', $foreignColumn)) {
                $this->dropForeignKey($foreignColumn);
            }
        }
        $dropColumns = array_values(array_filter($extraColumns, fn ($column) => Schema::hasColumn('bookings', $column)));
        if ($dropColumns) {
            $this->dropIndexesForColumns($dropColumns);
            Schema::table('bookings', fn (Blueprint $table) => $table->dropColumn($dropColumns));
        }

        if (! $this->hasIndex('bookings', ['project_id', 'unit_label'])) {
            Schema::table('bookings', fn (Blueprint $table) => $table->unique(['project_id', 'unit_label']));
        }
    }

    private function dropForeignKey(string $column): void
    {
        $constraint = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())->where('TABLE_NAME', 'bookings')
            ->where('COLUMN_NAME', $column)->whereNotNull('REFERENCED_TABLE_NAME')->value('CONSTRAINT_NAME');
        if ($constraint) {
            DB::statement('ALTER TABLE bookings DROP FOREIGN KEY `'.$constraint.'`');
        }
    }

    private function hasIndex(string $table, array $columns): bool
    {
        $indexes = collect(DB::select("SHOW INDEX FROM `{$table}`"))->groupBy('Key_name');
        foreach ($indexes as $index) {
            $indexedColumns = $index->sortBy('Seq_in_index')->pluck('Column_name')->all();
            if ($indexedColumns === $columns) return true;
        }

        return false;
    }

    private function dropIndexesForColumns(array $columns): void
    {
        $indexes = collect(DB::select('SHOW INDEX FROM bookings'))->groupBy('Key_name');
        foreach ($indexes as $name => $index) {
            $indexedColumns = $index->pluck('Column_name')->all();
            if ($name !== 'PRIMARY' && array_intersect($columns, $indexedColumns)) {
                DB::statement('ALTER TABLE bookings DROP INDEX `'.$name.'`');
            }
        }
    }

    public function down(): void
    {
        throw new RuntimeException('Restore the prior database backup to roll back booking table conversion.');
    }
};
