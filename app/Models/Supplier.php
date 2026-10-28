<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Supplier extends Model
{
    use HasFactory;
    protected $fillable = [
        'supplier_name',
        'contact_person',
        'phone',
        'phone',
        'email',
        'city',
        'address',
        'gst_number',
        'status',
    ];

    public static function validatedData(Request $request): array
    {
        return $request->validate([
            'supplier_name'      => ['required', 'string', 'max:120'],
            'contact_person'      => ['required', 'string', 'max:100'],
            'phone'          => ['required', 'string', 'max:20'],
            'email'          => ['nullable', 'email', 'max:100'],
            'city'           => ['nullable', 'string', 'max:50'],
            'gst_number'       => ['nullable', 'string', 'max:50'],
            'address'    => ['nullable', 'string', 'max:255'],
            'status'         => ['required', 'boolean'],
        ]);
    }
}
