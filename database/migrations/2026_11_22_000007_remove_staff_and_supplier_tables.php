<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('staff');
        Schema::dropIfExists('suppliers');
    }

    public function down()
    {
        // Staff and suppliers are intentionally not part of this project.
    }
};
