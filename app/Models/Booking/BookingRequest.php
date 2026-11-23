<?php

namespace App\Models\Booking;

use App\Models\Client\Client;
use App\Models\Project\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class BookingRequest extends Model
{
    public const PENDING_MANAGER = 'pending_manager';

    public const PENDING_ACCOUNTANT = 'pending_accountant';

    public const APPROVED = 'approved';

    public const REJECTED = 'rejected';

    protected $fillable = [
        'request_no', 'project_id', 'client_id', 'unit_label', 'agreed_amount',
        'initial_payment_amount', 'payment_method', 'payment_reference', 'client_message',
        'status', 'manager_reviewed_by', 'manager_reviewed_at', 'accountant_reviewed_by',
        'accountant_reviewed_at', 'rejection_note',
    ];

    protected $casts = ['manager_reviewed_at' => 'datetime', 'accountant_reviewed_at' => 'datetime'];

    public static function validatedData(Request $request): array
    {
        return $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'unit_label' => ['required', 'string', 'max:50'],
            'agreed_amount' => ['required', 'numeric', 'gt:0', 'max:9999999999.99'],
            'initial_payment_amount' => ['required', 'numeric', 'gt:0', 'lte:agreed_amount'],
            'payment_method' => ['required', 'in:cash,bank,mobile_banking,cheque'],
            'payment_reference' => ['nullable', 'string', 'max:100'],
            'client_message' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function booking()
    {
        return $this->hasOne(Booking::class);
    }
}
