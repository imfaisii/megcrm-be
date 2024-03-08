<?php

namespace App\Models;

use AjCastro\EagerLoadPivotRelations\EagerLoadPivotTrait;
use App\Actions\Common\BaseModel;
use App\Filters\Team\FilterByAdminName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\QueryBuilder\AllowedFilter;

class Team extends BaseModel
{
    use EagerLoadPivotTrait;        // the table second table we are  in many-to-many relationships has this trait, like if we are geting user with roles then roles would have this trait

    use HasFactory;

    protected $fillable = [
        'name',
        'created_by_id',
        'admin_id'
    ];
    protected array $allowedIncludes = [
        'createdBy',
    ];

    protected function getExtraFilters(): array
    {
        return [
            AllowedFilter::custom('admin_name', new FilterByAdminName()),
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, TeamUsers::class, 'team_id', 'user_id')->using(TeamUsers::class)->withPivot(['role_id'])->withTimestamps();
    }


    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id', 'id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id', 'id');
    }

}
