<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    public const BOOKED = 'booked';

    protected $fillable = ['booking_request_id', 'project_id', 'client_id', 'unit_label', 'agreed_amount', 'booking_date', 'status', 'created_by'];

    public function request() { return $this->belongsTo(BookingRequest::class, 'booking_request_id'); }
    public function project() { return $this->belongsTo(Project::class); }
    public function client() { return $this->belongsTo(Client::class); }
    public function payments() { return $this->hasMany(ClientPayment::class); }
}
