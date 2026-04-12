<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'projects_id',
        'teams_id',
        'categories_id',
        'status',
        'motivation',
        'note',
        'applied_at',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'projects_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'teams_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'categories_id');
    }
}
