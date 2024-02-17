<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use App\Sorts\Surveyors\UserRelationSort;
use App\Traits\Common\HasRecordCreator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\QueryBuilder\AllowedSort;

class Surveyor extends BaseModel
{
    use HasFactory, HasRecordCreator;

    protected $fillable = [
        'user_id'
    ];

    protected array $allowedIncludes = [
        'user',
        'createdBy'
    ];

    protected array $allowedRelationshipFilters = [
        'user:name',
        'user:email',
    ];

    protected function getExtraSorts(): array
    {
        return [
            AllowedSort::custom('user.name', new UserRelationSort()),
            AllowedSort::custom('user.email', new UserRelationSort()),
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
