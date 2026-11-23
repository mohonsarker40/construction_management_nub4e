<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ClientPayment extends Model
{
    public const PENDING = 'pending';
    public const APPROVED = 'approved';

    protected $fillable = ['booking_id', 'amount', 'payment_date', 'payment_method', 'reference_no', 'status', 'recorded_by', 'approved_by', 'approved_at'];
    protected $casts = ['approved_at' => 'datetime'];

    public static function validatedData(Request $request): array
    {
        return $request->validate([
            'booking_id' => ['required', 'exists:bookings,id'],
            'amount' => ['required', 'numeric', 'gt:0', 'max:9999999999.99'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,bank,mobile_banking,cheque'],
            'reference_no' => ['nullable', 'string', 'max:100'],
        ]);
    }

    public function booking() { return $this->belongsTo(Booking::class); }
}
