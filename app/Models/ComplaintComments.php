<?php

namespace App\Models;

use App\Actions\Common\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintComments extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'complaint_id',
        'comment',
    ];



    /**
     * Get the  that owns the ComplaintComments
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function complain(): BelongsTo
    {
        return $this->belongsTo(Complaints::class, 'complain_id');
    }
}
