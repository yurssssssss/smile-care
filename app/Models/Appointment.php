<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'user_id',
        'dentist_id',
        'service',
        'appointment_date',
        'appointment_time',
        'concern',
        'status',
        'admin_notes',
        'reschedule_reason',    // why the dentist rescheduled
        'rescheduled_from',     // the original date before rescheduling
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'rescheduled_from' => 'date',
    ];

    /* ── Relationships ── */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dentist()
    {
        return $this->belongsTo(Dentist::class);
    }

    /* ── Accessors ── */

    public function getFormattedDateAttribute(): string
    {
        return $this->appointment_date->format('M d, Y');
    }

    public function getFormattedTimeAttribute(): string
    {
        return date('g:i A', strtotime($this->appointment_time));
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'confirmed'   => 'badge-blue',
            'completed'   => 'badge-green',
            'cancelled'   => 'badge-red',
            'rescheduled' => 'badge-amber',
            default       => 'badge-amber',   // pending
        };
    }
}