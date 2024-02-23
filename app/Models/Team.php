<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'created_by_id',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, TeamUsers::class, 'team_id', 'user_id');
    }


}
