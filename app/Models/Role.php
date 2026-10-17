<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class Role extends Model
{
    use HasFactory;
    protected $fillable = ['role_name', 'description'];

    public static function validatedData(Request $request, ?self $role = null): array
    {
        return $request->validate([
            'role_name' => [
                'required', 'string', 'max:50',
                Rule::unique('roles', 'role_name')->ignore($role?->id),
            ],
            'description' => ['required', 'string', 'max:255'],
        ]);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
