<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Commision extends Model
{
    use HasFactory;

    protected $table = 'commisions';

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    public function decisions()
    {
        return $this->hasMany(Decision::class, 'commisions_id');
    }
}
