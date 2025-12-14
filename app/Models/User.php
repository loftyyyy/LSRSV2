<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
        ];
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'reserved_by', 'user_id');
    }

    public function releasedRentals()
    {
        return $this->hasMany(Rental::class, 'released_by', 'user_id');
    }

    public function returnedRentals()
    {
        return $this->hasMany(Rental::class, 'returned_to', 'user_id');
    }

    public function processedPayments()
    {
        return $this->hasMany(Payment::class, 'processed_by', 'user_id');
    }

    public function createdInvoices()
    {
        return $this->hasMany(Invoice::class, 'created_by', 'user_id');
    }
}
