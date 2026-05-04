<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

      protected $fillable = [
        'company_name',
        'ico',
        'description',
        'website',
        'address',
    ];

    public function users()
    {
       return $this->belongsToMany(
    User::class,
    'company_members',
    'company_id',
    'user_id'
)->withPivot('role_in_company');
    }
}
