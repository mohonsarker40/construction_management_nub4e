<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'user_id',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function bookingRequests() { return $this->hasMany(BookingRequest::class); }
    public function bookings() { return $this->hasMany(Booking::class); }

}
