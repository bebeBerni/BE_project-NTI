<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'deadline' => 'datetime',
        'budget' => 'float',
    ];

    /*
    |--------------------------------------------------------------------------
    | BELONGS TO
    |--------------------------------------------------------------------------
    */

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HAS MANY
    |--------------------------------------------------------------------------
    */

    public function assignments()
    {
        return $this->hasMany(ProjectAssignment::class);
    }

    public function applications()
    {
        return $this->hasMany(ProjectApplication::class);
    }

    public function decisions()
    {
        return $this->hasMany(Decision::class);
    }

    public function history()
    {
        return $this->hasMany(ProjectHistory::class);
    }
}
