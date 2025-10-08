<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        // Extended profile & role fields
        'role','profile_picture','phone','address','subscription_status','points',
        'employee_id','vehicle_type','vehicle_plate','work_area','status','rating','total_collections'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'points' => 'integer',
            'rating' => 'decimal:2',
            'total_collections' => 'integer',
        ];
    }

    /**
     * Get user role check methods
     */
    public function isEndUser(): bool
    {
        return $this->role === 'end_user';
    }

    public function isMitra(): bool
    {
        return $this->role === 'mitra';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Relationships
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'user_id');
    }

    public function mitraSchedules()
    {
        return $this->hasMany(Schedule::class, 'mitra_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'user_id');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class, 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function balanceEntries()
    {
        return $this->hasMany(BalanceEntry::class, 'user_id');
    }
}
