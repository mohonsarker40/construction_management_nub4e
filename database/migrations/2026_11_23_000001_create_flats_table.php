<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('flat_name', 50);
            $table->decimal('price', 12, 2);
            $table->timestamps();
            $table->unique(['project_id', 'flat_name']);
        });

        foreach (DB::table('projects')->select('id', 'available_units')->get() as $project) {
            foreach (preg_split('/\r\n|\r|\n/', $project->available_units ?? '') as $line) {
                $parts = array_map('trim', explode('|', $line, 2));
                if (count($parts) === 2 && $parts[0] !== '' && is_numeric($parts[1]) && (float) $parts[1] > 0) {
                    DB::table('flats')->insertOrIgnore([
                        'project_id' => $project->id,
                        'flat_name' => $parts[0],
                        'price' => $parts[1],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('available_units');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->text('available_units')->nullable();
        });
        foreach (DB::table('projects')->get() as $project) {
            $lines = DB::table('flats')->where('project_id', $project->id)->orderBy('flat_name')
                ->get()->map(fn ($flat) => $flat->flat_name.' | '.$flat->price)->implode("\n");
            DB::table('projects')->where('id', $project->id)->update(['available_units' => $lines]);
        }
        Schema::dropIfExists('flats');
    }
};
