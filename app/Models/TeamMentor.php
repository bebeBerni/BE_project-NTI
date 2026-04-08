<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TeamMentor extends Model
{
    use HasFactory;

    protected $table = 'team_mentors';

    protected $fillable = [
        'teams_id',
        'mentors_id',
        'assigned_at',
        'active',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'active' => 'boolean',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class, 'teams_id');
    }

    public function mentor()
    {
        return $this->belongsTo(Mentor::class, 'mentors_id');
    }
}
