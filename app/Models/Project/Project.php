<?php

namespace App\Models\Project;

use App\Models\Booking\Booking;
use App\Models\Booking\BookingRequest;
use App\Models\Client\Client;
use App\Models\Finance\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Project extends Model
{
    protected $fillable = ['client_id', 'project_name', 'project_type', 'location', 'budget', 'start_date', 'estimated_end_date', 'actual_end_date', 'available_units', 'status', 'manager_user_id', 'designer_user_id', 'created_by'];

    protected $casts = ['start_date' => 'date', 'estimated_end_date' => 'date', 'actual_end_date' => 'date'];

    public static function validatedData(Request $request): array
    {
        return $request->validate([
            'client_id' => ['nullable', 'exists:clients,id'],
            'project_name' => ['required', 'string', 'max:150'],
            'project_type' => ['required', 'string', 'max:50'],
            'location' => ['required', 'string', 'max:255'],
            'budget' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'start_date' => ['required', 'date'],
            'estimated_end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'manager_user_id' => ['nullable', 'exists:users,id'],
            'designer_user_id' => ['required', 'exists:users,id'],
            'available_units' => ['required', 'string', 'max:20000'],
        ]);
    }

    public function listedUnits(): array
    {
        return collect(preg_split('/\\r\\n|\\r|\\n/', $this->available_units ?? ''))
            ->map(function ($line) {
                $parts = array_map('trim', explode('|', $line, 2));
                if (count($parts) !== 2 || $parts[0] === '' || ! is_numeric($parts[1]) || (float) $parts[1] <= 0) {
                    return null;
                }

                return ['label' => $parts[0], 'price' => (float) $parts[1]];
            })->filter()->values()->all();
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    public function designer()
    {
        return $this->belongsTo(User::class, 'designer_user_id');
    }

    public function bookingRequests()
    {
        return $this->hasMany(BookingRequest::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
