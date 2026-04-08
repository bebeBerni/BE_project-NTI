<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'leader_user_id',
        'status',
    ];

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_user_id');
    }

    public function teamMembers()
    {
        return $this->hasMany(TeamMember::class, 'teams_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'team_members', 'teams_id', 'students_id')
            ->withPivot('member_role', 'joined_at')
            ->withTimestamps();
    }

    public function teamMentors()
    {
        return $this->hasMany(TeamMentor::class, 'teams_id');
    }

    public function mentors()
    {
        return $this->belongsToMany(Mentor::class, 'team_mentors', 'teams_id', 'mentors_id')
            ->withPivot('assigned_at', 'active')
            ->withTimestamps();
    }

    public function projectAssignments()
    {
        return $this->hasMany(ProjectAssignment::class, 'teams_id');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_assignments', 'teams_id', 'projects_id')
            ->withPivot('assigned_at', 'status')
            ->withTimestamps();
    }
}
