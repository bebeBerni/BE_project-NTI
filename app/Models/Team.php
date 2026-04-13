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
        return $this->belongsTo(User::class);
    }

    public function teamMembers()
    {
        return $this->hasMany(TeamMember::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'team_members', 'team_id', 'student_id')
            ->withPivot('member_role', 'joined_at')
            ->withTimestamps();
    }

    public function teamMentors()
    {
        return $this->hasMany(TeamMentor::class,);
    }

    public function mentors()
    {
        return $this->belongsToMany(Mentor::class, 'team_mentors', 'team_id', 'mentor_id')
            ->withPivot('assigned_at', 'active')
            ->withTimestamps();
    }

    public function projectAssignments()
    {
        return $this->hasMany(ProjectAssignment::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_assignments', 'team_id', 'project_id')
            ->withPivot('assigned_at', 'status')
            ->withTimestamps();
    }
}
