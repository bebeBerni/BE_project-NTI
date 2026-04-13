<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
      protected $fillable = [
        'company_name',
        'ico',
        'description',
        'website',
        'address',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'company_members', 'companies_id', 'users_id')
            ->withPivot('role_in_company');
    }
}
