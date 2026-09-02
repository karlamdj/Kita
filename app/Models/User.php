<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\HasApiTokens;
use App\Mail\WelcomeUserMail;

#[Fillable(['name', 'email', 'password', 'google_id', 'avatar'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Send the email verification notification using KITA's branded welcome/verification email.
     */
    public function sendEmailVerificationNotification(): void
    {
        Mail::to($this->email)->send(new WelcomeUserMail($this));
    }

    /**
     * Get the profiles for the user.
     */
    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }

    /**
     * Get the profiles relation for backwards compatibility.
     */
    public function profile(): HasMany
    {
        return $this->profiles();
    }

    /**
     * Get the active profile (or the first profile).
     */
    public function getProfileAttribute()
    {
        $activeId = request()->header('X-Profile-ID') ?? request()->input('profile_id') ?? session('active_profile_id');
        if ($activeId) {
            $profile = $this->profiles()->find($activeId);
            if ($profile) {
                return $profile;
            }
        }
        return $this->profiles()->first();
    }

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
}
