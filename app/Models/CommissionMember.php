<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommissionMember extends Model
{
    use HasFactory;

    protected $table = 'commissions_members';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'commission_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commission()
    {
        return $this->belongsTo(Commission::class);
    }
    public function members($id)
{
    $commission = Commission::with('members.user')->findOrFail($id);

    return response()->json($commission->members);
}
}
