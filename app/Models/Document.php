<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProjectApplication;
use App\Models\User;
class Document extends Model
{
     protected $fillable = [
        'users_id',
        'project_application_id',
        'type',
        'file_name',
        'file_path',
    ];

    public function user()
    {
      return $this->belongsTo(User::class, 'users_id');
}

public function ProjectApplication()
{
    return $this->hasMany(ProjectApplication::class, 'project_application_id');
}

}
