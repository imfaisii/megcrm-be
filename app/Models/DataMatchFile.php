<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataMatchFile extends BaseModel
{
    use HasFactory;

    public bool $enableLoggingModelsEvents = false;

    protected $table = 'data_match_files';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'file_name',
        'file_path',
        'created_by',
    ];

    /**
     * Get the user that owns the DataMatchFile
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
