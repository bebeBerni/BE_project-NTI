<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'users_id',
        'faculty',
        'department',
        'study_program',
        'year_of_study',
        'is_ukf_verified',
    ];

    protected $casts = [
        'is_ukf_verified' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function teamMembers()
    {
        return $this->hasMany(TeamMember::class, 'students_id');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_members', 'students_id', 'teams_id')
            ->withPivot('member_role', 'joined_at')
            ->withTimestamps();
    }
}
