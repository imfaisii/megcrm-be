<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\Permission\Models\Role;

class TeamUsers extends Pivot
{
    use HasFactory;

    protected $table = 'team_users';
    protected $fillable = [
        'team_id',
        'user_id',
        'role_id'
    ];



    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }
}
