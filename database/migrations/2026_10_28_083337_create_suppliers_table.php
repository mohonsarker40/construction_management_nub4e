<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
             $table->string('supplier_name', 120);               
            $table->string('contact_person', 100);   
            $table->string('phone', 20);                        
            $table->string('email', 100)->nullable();            
            $table->string('city', 50)->nullable();              
            $table->string('address', 255)->nullable();          
            $table->string('gst_number', 50)->nullable();        
            $table->tinyInteger('status')->default(1)->comment('0=Inactive, 1=Active');   
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suppliers');
    }
};
