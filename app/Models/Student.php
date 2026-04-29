<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
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
        return $this->hasMany(TeamMember::class);
    }

public function teams()
{
    return $this->belongsToMany(
        Team::class,
        'team_members',
        'student_id',
        'team_id'
    );
}
}
