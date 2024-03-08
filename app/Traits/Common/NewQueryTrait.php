<?php

namespace App\Traits\Common;

use App\Actions\Common\BaseQueryBuilder as QueryBuilder;
use App\Filters\Common\FilterByTimestamp;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder as SpatieQueryBuilder;

/**
 * @property bool $queryBuilderEnabled
 */
trait NewQueryTrait
{
    protected string $modelClass;

    protected bool $queryBuilderEnabled = false;

    protected ?Builder $query = null;

    /**
     * @return $this
     */
    public function enableQueryBuilder(): static
    {
        $this->queryBuilderEnabled = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function disableQueryBuilder(): static
    {
        $this->queryBuilderEnabled = false;

        return $this;
    }

    public function isQueryBuilderEnabled(): bool
    {
        return $this->queryBuilderEnabled;
    }

    public function newQuery(): Builder
    {
        return $this->modelClass::query();
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getQuery(): SpatieQueryBuilder|Builder
    {
        $query = $this->query ?? $this->newQuery();
        if (! $this->queryBuilderEnabled || $query instanceof QueryBuilder) {
            return $query;
        }

        return $this->applyQueryBuilder($query);
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function applyQueryBuilder(Builder $query): SpatieQueryBuilder
    {
        /** @var \App\Actions\Common\BaseModel $model */
        $model = $query->getModel();
        $query = QueryBuilder::for($query)
            ->allowedFilters(array_merge($model->getAllowedFilters(), $this->getExtraFilters()))
            ->allowedSorts(array_merge($model->getAllowedSorts(), $this->getExtraSorts()))
            ->allowedFields($model->getAllowedFields())
            ->allowedIncludes(array_merge($model->getAllowedIncludes(), $this->getExtraIncludes()));

        // check if search is initiated
        if (request()->has('q') && (new $model())->shouldBeSearchable()) {
            $primaryKey = (new $model())->getKeyName();
            $table = (new $model())->getTable();
            $items = $model::search(request()->input('q'));

            $filter = request()->get('filter');
            if (Arr::get($filter, 'trashed', false) === 'only') {
                $items = $items->onlyTrashed()->get();
            } else {
                $items = $items->get();
            }

            $query->whereIn("$table.$primaryKey", $items->pluck($primaryKey));
        }

        return $query;
    }

    protected function getExtraIncludes(): array
    {
        return [];
    }

    protected function getExtraFilters(): array
    {
        return [
            AllowedFilter::custom('timestamp', new FilterByTimestamp()),
        ];
    }

    protected function getExtraSorts(): array
    {
        return [];
    }

    protected function getExtraAppends(): array
    {
        return [];
    }

    /**
     * @return NewQueryTrait
     */
    public function setQuery(Builder $query): static
    {
        $this->query = $query;

        return $this;
    }
}
