<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class Client extends Model
{
    use HasFactory;
    protected $fillable = [
        'client_name',
        'contact_person',
        'email',
        'phone',
        'address',
        'status',
    ];

    public static function validatedData(Request $request): array
    {
        return $request->validate([
            'client_name' => ['required', 'string', 'max:120'],
            'contact_person' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['0', '1', 0, 1])],
        ]);
    }
}
