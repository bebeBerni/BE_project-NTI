<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mentor extends Model
{
    use HasFactory;

    protected $fillable = [
        'users_id',
        'specialization',
        'bio',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function teamMentors()
    {
        return $this->hasMany(TeamMentor::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_mentors', 'mentor_id', 'team_id')
            ->withPivot('assigned_at', 'active')
            ->withTimestamps();
    }
}
