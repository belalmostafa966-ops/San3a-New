<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'role',
        'status',
        'device_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function craftsmanProfile()
    {
        return $this->hasOne(CraftsmanProfile::class);
    }

    public function verificationDocuments()
    {
        return $this->hasMany(VerificationDocument::class);
    }

    public function identityFingerprint()
    {
        return $this->hasOne(IdentityFingerprint::class);
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }
    public function wallet()
{
    return $this->hasOne(Wallet::class);
}

public function paymentMethods()
{
    return $this->hasMany(PaymentMethod::class);
}

public function subscriptions()
{
    return $this->hasMany(Subscription::class, 'craftsman_id');
}
}
