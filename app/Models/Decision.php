<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Decision extends Model
{
    use HasFactory;


    protected $fillable = [
        'project_id',
        'commission_id',
        'status',
        'comment',
        'decided_at',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function commission()
    {
        return $this->belongsTo(Commission::class);
    }
}
