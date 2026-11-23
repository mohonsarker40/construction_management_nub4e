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
    protected $fillable = ['client_id', 'project_name', 'project_type', 'location', 'budget', 'start_date', 'estimated_end_date', 'actual_end_date', 'status', 'manager_user_id', 'designer_user_id', 'created_by'];

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
            'flats' => ['required', 'array', 'min:1'],
            'flats.*.flat_name' => ['required', 'string', 'max:50'],
            'flats.*.price' => ['required', 'numeric', 'gt:0', 'max:9999999999.99'],
        ]);
    }

    public function listedUnits(): array
    {
        return $this->flats()->orderBy('flat_name')->get()
            ->map(function ($line) {
                return ['id' => $line->id, 'label' => $line->flat_name, 'price' => (float) $line->price];
            })->all();
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

    public function flats()
    {
        return $this->hasMany(Flat::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function bookingRequests()
    {
        return $this->hasMany(BookingRequest::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
