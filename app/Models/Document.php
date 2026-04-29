<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProjectApplication;
use App\Models\User;
class Document extends Model
{
     protected $fillable = [
        'user_id',
        'project_application_id',
        'type',
        'file_name',
        'file_path',
    ];


    protected $casts = [
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
];
    public function user()
    {
      return $this->belongsTo(User::class, 'user_id');
}

public function projectApplication()
{
     return $this->belongsTo(ProjectApplication::class, 'project_application_id');
}

}
