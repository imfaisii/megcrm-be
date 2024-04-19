<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use App\Filters\FilterByCreatedAt;
use App\Traits\Common\HasRecordCreator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\QueryBuilder\AllowedFilter;

class DataMatchFile extends BaseModel
{
    use HasFactory, HasRecordCreator;

    public bool $enableLoggingModelsEvents = false;

    protected $table = 'data_match_files';

    protected $keyType = 'string';

    public $incrementing = false;

    protected array $allowedIncludes = ['createdBy'];

    protected array $discardedFieldsInFilter = ['created_at'];

    protected $fillable = [
        'file_name',
        'file_path',
        'created_by_id',
    ];

    protected function getExtraFilters(): array
    {
        return [
            AllowedFilter::custom('created_at', new FilterByCreatedAt()),
        ];
    }

    /**
     * Get the user that owns the DataMatchFile
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
