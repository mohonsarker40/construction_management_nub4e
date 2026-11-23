<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_request_id')->unique()->constrained('booking_requests');
            $table->foreignId('project_id')->constrained('projects');
            $table->foreignId('client_id')->constrained('clients');
            $table->string('unit_label', 50);
            $table->decimal('agreed_amount', 12, 2);
            $table->date('booking_date');
            $table->string('status', 20)->default('booked');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->unique(['project_id', 'unit_label']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};
