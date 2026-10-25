<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class Staff extends Model
{
    use HasFactory;
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'specialization',
        'daily_rate',
        'designation',
        'status',
    ];

    public static function validatedData(Request $request): array
    {
        return $request->validate([
            'full_name'      => ['required', 'string', 'max:120'],
            'phone'          => ['required', 'string', 'max:20'],
            'email'          => ['nullable', 'email', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'daily_rate'     => ['nullable', 'numeric', 'min:0'],
            'designation'    => ['nullable', 'string', 'max:255'],
            'status'         => ['required', 'boolean'],
        ]);
    }}
