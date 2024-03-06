<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'created_by_id',
        'admin_id'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, TeamUsers::class, 'team_id', 'user_id')->withPivot(['role_id'])->withTimestamps();
    }


    public function creater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id', 'id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id', 'id');
    }

}
