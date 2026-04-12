<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'projects_id',
        'teams_id',
        'result',
        'final_note',
        'finished_at',
    ];

    protected $casts = [
        'finished_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'projects_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'teams_id');
    }
}
