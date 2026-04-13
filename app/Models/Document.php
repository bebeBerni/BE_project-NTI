<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documents extends Model
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
}