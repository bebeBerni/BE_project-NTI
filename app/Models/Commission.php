<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Commission extends Model
{
    use HasFactory;

    protected $table = 'commissions';

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    public function decisions()
    {
        return $this->hasMany(Decision::class);
    }

    public function members()
    {
       return $this->hasMany(CommissionMember::class, 'commission_id');
    }

}

