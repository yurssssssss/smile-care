<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dentist extends Model
{
    protected $fillable = [
        'name',
        'specialization',
        'is_active',
        'user_id',      // links to the dentist's login account in the users table
    ];

    /* ── Relationships ── */

    /** The login account (User) for this dentist */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}