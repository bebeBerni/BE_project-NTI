<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamJoinRequest extends Model
{
    protected $fillable = [
        'team_id',
        'student_id',
        'status',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
