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
            'contact_person'      => ['required', 'string', 'max:120'],
            'phone'          => ['required', 'string', 'max:20'],
            'email'          => ['nullable', 'email', 'max:255'],
            'city'           => ['nullable', 'string', 'max:255'],
            'gst_number'       => ['nullable', 'numeric', 'min:0'],
            'address'    => ['nullable', 'string', 'max:255'],
            'status'         => ['required', 'boolean'],
        ]);
    }
}
