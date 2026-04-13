<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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

 public function CommissionMember()
    {
        return $this->hasMany(CommissionMember::class);
    }

public function Document()
{
    return $this->hasMany(Document::class);
}

public function CompanyMember()
{
    return $this->hasMany(CompanyMember::class);
}

public function Student()
{
    return $this->hasMany(Student::class);
}


public function Mentor()
{
    return $this->hasMany(Mentor::class);
}

public function role()
{
    return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
}


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
}
