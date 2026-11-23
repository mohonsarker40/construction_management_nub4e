<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;

class Flat extends Model
{
    protected $fillable = ['project_id', 'flat_name', 'price'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
