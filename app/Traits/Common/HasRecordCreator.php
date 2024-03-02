<?php

namespace App\Traits\Common;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait HasRecordCreator
{
    public static function bootHasRecordCreator(): void
    {
        static::creating(function (Model $model) {
            if (! Auth::guest()) {
                $model->setAttribute('created_by_id', Auth::user()->id);
            }
        });

        static::updating(function (Model $model) {
            if (! Auth::guest()) {
                $model->setAttribute('created_by_id', Auth::user()->id);
            }
        });
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
