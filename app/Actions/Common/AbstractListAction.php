<?php

namespace App\Actions\Common;

use App\Traits\Common\HasApiResource;
use App\Traits\Common\NewQueryTrait;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

abstract class AbstractListAction
{
    use HasApiResource;
    use NewQueryTrait;

    protected int $defaultPagination = 10;

    public function setPaginationLength(int $length)
    {
        $this->defaultPagination = $length;

        return $this;
    }

    public function get(): Collection
    {
        return $this->getQuery()->get();
    }

    public function paginate(): Paginator
    {
        return $this->getQuery()->latest()->paginate(request()->get('per_page', $this->defaultPagination));
    }

    public function listOrPaginate(): Paginator|Collection
    {

        if (request()->has('all')) {
            return $this->get();
        } else {
            return $this->paginate();
        }
    }
}
