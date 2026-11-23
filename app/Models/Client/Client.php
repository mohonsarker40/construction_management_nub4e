<?php

namespace App\Models\Client;

use App\Models\Booking\Booking;
use App\Models\Booking\BookingRequest;
use App\Models\User;
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function bookingRequests()
    {
        return $this->hasMany(BookingRequest::class);
    }
}
