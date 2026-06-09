<?php

namespace App\Models;
use App\Models\Role;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Authenticatable implements MustVerifyEmail, CanResetPasswordContract
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable,HasApiTokens;
    use CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'email_verified_at',

    ];

        protected $hidden = [
        'password',
        'remember_token',
    ];

      protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }



  // 🔹 ROLES (many-to-many)


    // 🔹 COMPANIES (many-to-many)
   public function companies()
{
    return $this->belongsToMany(
        Company::class,
        'company_members',
        'user_id',
        'company_id'
    )->withPivot('role_in_company');
}

    // 🔹 STUDENT (valószínű 1:1)
    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    // 🔹 MENTOR (1:1)
    public function mentor()
    {
        return $this->hasOne(Mentor::class, 'user_id');
    }

    // 🔹 DOCUMENTS (1:N)
    public function documents()
    {
        return $this->hasMany(Document::class, 'user_id');
    }

    // 🔹 COMPANY MEMBERS (1:N)
    public function companyMembers()
    {
        return $this->hasMany(CompanyMember::class, 'user_id');
    }

    // 🔹 COMMISSION MEMBERS (1:N)
    public function commissionMembers()
    {
        return $this->hasMany(CommissionMember::class, 'user_id');
    }
    public function roles()
{
    return $this->belongsToMany(Role::class, 'role_user');
}
public function hasRole($role)
{
    return $this->roles()
        ->whereRaw('LOWER(name) = ?', [strtolower($role)])
        ->exists();
}
public function isRole($role)
{
    return $this->hasRole($role);
}
    public function sendPasswordResetNotification($token): void
    {
        $email = urlencode($this->email);

        $url = config('app.frontend_url') . "/reset-password/{$token}?email={$email}";

        $this->notify(new class($url) extends ResetPassword {
            public function __construct(private string $url)
            {
                parent::__construct('');
            }

            public function toMail($notifiable)
            {
                return (new MailMessage)
                    ->subject('Reset Password Notification')
                    ->greeting('Hello!')
                    ->line('You are receiving this email because we received a password reset request for your account.')
                    ->action('Reset Password', $this->url)
                    ->line('This password reset link will expire in 60 minutes.')
                    ->line('If you did not request a password reset, no further action is required.');
            }
        });
    }



    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
        */


    }


