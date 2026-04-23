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
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',

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
    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'role_user',
            'user_id',   // ⚠️ DB szerint!
            'role_id'
        );
    }

    // 🔹 COMPANIES (many-to-many)
    public function companies()
    {
        return $this->belongsToMany(
            Company::class,
            'company_members',
            'user_id',
            'company_id'
        );
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


