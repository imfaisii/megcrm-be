<?php

namespace App\Http\Controllers;

use App\Actions\Leads\LeadGeneratorAssignment\StoreLeadGeneratorAssignmentAction;
use App\Http\Requests\Leads\LeadGeneratorAssignment\StoreLeadGeneratorAssignmentRequest;

use function App\Helpers\null_resource;

class LeadGeneratorAssignmentController extends Controller
{
    public function store(StoreLeadGeneratorAssignmentRequest $request, StoreLeadGeneratorAssignmentAction $action)
    {
        $action->create($request->validated());

        return null_resource();
    }
}
