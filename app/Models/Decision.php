<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Decision extends Model
{
    use HasFactory;

    protected $table = 'decisions';

    protected $fillable = [
        'projects_id',
        'commisions_id',
        'status',
        'comment',
        'decided_at',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'projects_id');
    }

    public function commision()
    {
        return $this->belongsTo(Commision::class, 'commisions_id');
    }
}
