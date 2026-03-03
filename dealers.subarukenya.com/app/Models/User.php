<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'active',
        'last_login_at',
        'last_login_ip', 
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'active' => 'boolean',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->active && $this->hasRole(['superadmin','administrator']) && $this->hasVerifiedEmail();
        }elseif ($panel->getId() === 'dealer') {
            return $this->hasRole('dealer') && $this->active && $this->hasVerifiedEmail();
        }
 
        return false;
    }

    protected $with = ['profile'];

    public function profile()
    {
        return $this->morphTo();
    }

    public function getHasDealerProfileAttribute()
    {
        return $this->profile_type == 'App\Models\DealerProfile';
    }

    public function hasCompletedProfile()
    {
        return $this->hasDealerProfile && 
            !empty($this->profile->business_name) && 
            !empty($this->profile->phone_no) && 
            !empty($this->profile->address_street) && 
            !empty($this->profile->address_city) && 
            !empty($this->profile->address_county);
    }

}
