<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'created_by_user_id',
        'company_id',
        'team_id',
        'budget',
        'status',
        'deadline',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'deadline' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | BELONGS TO
    |--------------------------------------------------------------------------
    */

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HAS MANY
    |--------------------------------------------------------------------------
    */

    public function assignments(): HasMany
    {
        return $this->hasMany(ProjectAssignment::class);
    }

    public function applications()
    {
        return $this->hasMany(ProjectApplication::class);
    }

    public function decisions(): HasMany
    {
        return $this->hasMany(Decision::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(ProjectHistory::class);
    }
}
