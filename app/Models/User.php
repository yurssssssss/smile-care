<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

        protected $fillable = [
            'first_name',
            'last_name',
            'email',
            'phone',
            'password',
            'role',
            'profile_photo',
        ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /* ── Accessors ── */

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /* ── Relationships ── */

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /** The Dentist profile linked to this user account */
    public function dentist()
    {
        return $this->hasOne(Dentist::class);
    }

    /* ── Role helpers ── */

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDentist(): bool
    {
        return $this->role === 'dentist';
    }

    public function isPatient(): bool
    {
        return $this->role === 'patient';
    }
}