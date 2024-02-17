<?php

namespace App\Http\Controllers\Leads;

use App\Actions\LeadJobs\ListLeadJobAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LeadJobController extends Controller
{
    public function index(ListLeadJobAction $action): ResourceCollection
    {
        $action->enableQueryBuilder();
        return $action->resourceCollection($action->listOrPaginate());
    }
}
