<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TeamMember extends Model
{
    use HasFactory;

    protected $table = 'team_members';

    protected $fillable = [
        'students_id',
        'teams_id',
        'member_role',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'students_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'teams_id');
    }
}
