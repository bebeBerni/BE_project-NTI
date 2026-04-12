<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommisionsMember extends Model
{
    use HasFactory;

    protected $table = 'commisions_members';

    public $timestamps = false;

    protected $fillable = [
        'users_id',
        'commisions_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function commision()
    {
        return $this->belongsTo(Commision::class, 'commisions_id');
    }
}
