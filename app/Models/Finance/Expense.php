<?php

namespace App\Models\Finance;

use App\Models\Project\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Expense extends Model
{
    public const MATERIALS = 1;

    public const LABOR = 2;

    public const OVERHEAD = 3;

    public const OTHER = 4;

    protected $table = 'expenses';

    protected $fillable = ['project_id', 'category', 'amount', 'expense_date', 'description', 'recorded_by'];

    public static function validatedData(Request $request): array
    {
        return $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'category' => ['required', 'in:1,2,3,4'],
            'amount' => ['required', 'numeric', 'gt:0', 'max:9999999999.99'],
            'expense_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
