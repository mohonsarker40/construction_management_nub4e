<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $fillable = ['role_id', 'full_name', 'email', 'password_hash', 'phone', 'status',];


    protected $hidden = ['password_hash'];
//        'password',
//        'remember_token',


    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public static function validatedData(Request $request, ?self $user = null): array
    {
        return $request->validate([
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'full_name' => ['required', 'string', 'max:100'],
            'email' => [
                'required', 'email', 'max:100',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'status' => ['required', Rule::in(['0', '1', 0, 1])],
        ]);
    }

    public static function validatedCredentials(Request $request): array
    {
        return $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);
    }

    public function getAuthPassword()
    {
        return $this->password_hash;
    }
}
