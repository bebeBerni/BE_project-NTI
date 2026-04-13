<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectAssignment extends Model
{
    use HasFactory;

    protected $table = 'project_assignments';

    protected $fillable = [
        'projects_id',
        'teams_id',
        'assigned_at',
        'status',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
