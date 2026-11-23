<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients');
            $table->string('project_name', 150);
            $table->string('project_type', 50);
            $table->string('location');
            $table->decimal('budget', 12, 2);
            $table->date('start_date');
            $table->date('estimated_end_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->text('available_units')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->foreignId('manager_user_id')->nullable()->constrained('users');
            $table->foreignId('designer_user_id')->nullable()->constrained('users');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
