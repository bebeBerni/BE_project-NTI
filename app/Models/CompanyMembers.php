<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyMembers extends Model
{

  protected $table = 'company_members';

    protected $fillable = [
        'users_id',
        'companies_id',
        'role_in_company',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function company()
    {
        return $this->belongsTo(Companies::class, 'companies_id');
    }


}
