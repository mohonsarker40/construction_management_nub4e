<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
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

    public function down()
    {
        Schema::dropIfExists('booking_requests');
    }
};
