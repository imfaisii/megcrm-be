<?php

namespace App\Actions\Common;

use App\Models\User;
use App\Traits\Common\HasLogsAppend;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\DeletedModels\Models\Concerns\KeepsDeletedModels;
use Throwable;

use function App\Helpers\get_all_appends;
use function App\Helpers\get_all_includes_in_camel_case;

/**
 * @property int $id
 */
abstract class BaseModel extends Model
{
    use BaseQueryBuilderConfig, HasLogsAppend, KeepsDeletedModels, LogsActivity;
    public $ScopeColumn = 'user_id';   // this is the main column used in policy and for checking whether this model can be checked by admin or not
    protected string $resourceKey = '';

    protected array $exactFilters = [];

    protected array $discardedFieldsInFilter = [];

    protected array $allowedRelationshipFilters = [];

    protected array $allowedAppends = [];

    protected array $allowedIncludes = [];

    protected array $excludedFromInputs = [];

    protected array $excludedFromCreateInputs = [];

    protected array $excludedFromUpdateInputs = [];

    protected array $searchableRelationships = [];

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getPerPage(): int
    {
        $request = Container::getInstance()->make('request');
        if ($request && $request->input('per_page')) {
            return $request->input('per_page');
        }

        return $this->perPage;
    }

    public function getExcludedFromCreateInputs(): array
    {
        return array_merge($this->excludedFromInputs, $this->excludedFromCreateInputs);
    }

    public function getExcludedFromUpdateInputs(): array
    {
        return array_merge($this->excludedFromInputs, $this->excludedFromUpdateInputs);
    }

    public function getExactFilters(): array
    {
        return array_merge($this->exactFilters, [
            $this->primaryKey,
        ]);
    }

    /**
     * Prepare a date for array / JSON serialization.
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function guessResourceKey(): string
    {
        if ($this->resourceKey !== '') {
            return Str::plural($this->resourceKey);
        }

        return $this->getTable();
    }

    public function toSearchableArray(): array
    {
        foreach ($this->searchableRelationships as $relationship) {
            if (!$this->relationLoaded($relationship)) {
                $this->load($relationship);
            }
        }

        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $dates = [];
        foreach ($this->casts as $key => $cast) {
            if (in_array($cast, ['date', 'datetime', 'immutable_date', 'immutable_datetime'])) {
                $dates[] = $key;
            }
        }
        $dates = array_merge($dates, $this->dates ?? []);
        foreach ($dates as $dateKey) {
            if ($array[$dateKey] ?? false) {
                try {
                    $date = Carbon::parse($array[$dateKey]);
                    $array[$dateKey . '_date_formatted'] = $date->format($user?->date_format ?? 'Y-m-d');
                    $array[$dateKey . '_time_formatted'] = $date->format($user?->time_format ?? 'H:i:s');
                } catch (Throwable) { // @phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
                }
            }
        }

        return $array;
    }

    /**
     * @return BaseModel
     */
    public function applyQueryBuilder(): static
    {
        return $this->load(get_all_includes_in_camel_case())
            ->append(get_all_appends());
    }

    public function shouldBeSearchable(): bool
    {
        return false;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "This record has been {$eventName}")
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if (!auth()->check()) {
            $activity->causer_id = 1;
            $activity->causer_type = User::class;
        }
    }
}
