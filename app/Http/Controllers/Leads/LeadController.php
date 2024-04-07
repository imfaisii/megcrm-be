<?php

namespace App\Http\Controllers\Leads;

use App\Actions\Leads\DeleteLeadAction;
use App\Actions\Leads\FindLeadAction;
use App\Actions\Leads\GetLeadExtrasAction;
use App\Actions\Leads\GetOtherSitesLinkAction;
use App\Actions\Leads\ListDataMatchAction;
use App\Actions\Leads\ListLeadAction;
use App\Actions\Leads\StoreLeadAction;
use App\Actions\Leads\UpdateLeadAction;
use App\Actions\Leads\UploadLeadsFileAction;
use App\Exports\Leads\DatamatchExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\DataMatch\UploadDataMatchRequest;
use App\Http\Requests\Lead\GetAllDataMatchFilesRequest;
use App\Http\Requests\Leads\StoreLeadCommentsRequest;
use App\Http\Requests\Leads\StoreLeadRequest;
use App\Http\Requests\Leads\UpdateLeadRequest;
use App\Http\Requests\Leads\UpdateLeadStatusRequest;
use App\Http\Requests\Leads\UploadLeadFileRequest;
use App\Http\Requests\Users\UploadDocumentsToCollectionRequest;
use App\Models\DataMatchFile;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

use function App\Helpers\null_resource;

class LeadController extends Controller
{
    public function __construct()
    {
        // $this->authorizeResource(Lead::class, 'lead');
    }

    public function index(ListLeadAction $action): ResourceCollection
    {
        $action->enableQueryBuilder();

        return $action->resourceCollection($action->listOrPaginate());
    }

    public function store(StoreLeadRequest $request, StoreLeadAction $action)
    {
        $lead = $action->create($request->validated());

        return $action->individualResource($lead);
    }

    public function show(Lead $lead, FindLeadAction $action)
    {
        $action->enableQueryBuilder();

        return $action->individualResource($action->findOrFail($lead->id));
    }

    public function update(Lead $lead, UpdateLeadRequest $request, UpdateLeadAction $action)
    {
        $action->enableQueryBuilder();

        return $action->individualResource($action->update($lead, $request->validated()));
    }

    public function destroy(Lead $lead, DeleteLeadAction $action)
    {
        $action->delete($lead);

        return null_resource();
    }

    public function storeComments(Lead $lead, StoreLeadCommentsRequest $request)
    {
        $lead->comment($request->comments);

        return null_resource();
    }

    public function getCouncilTaxLink(string $postCode): RedirectResponse
    {
        return redirect((new GetOtherSitesLinkAction())->councilTax($postCode));
    }

    public function getExtras(): JsonResponse
    {
        return $this->success(data: (new GetLeadExtrasAction(auth()->user()))->execute());
    }

    public function updateStatus(Lead $lead, UpdateLeadStatusRequest $request)
    {
        if (
            str_contains(str()->lower($request->status), 'survey booked')
            ||
            str_contains(str()->lower($request->status), 'survey done')
        ) {
            $lead->update([
                'is_marked_as_job' => true,
            ]);
        }

        $lead->setStatus($request->status, $request->comments);

        return null_resource();
    }

    public function uploadDocumentToCollection(Lead $lead, UploadDocumentsToCollectionRequest $request)
    {
        $existingMedia = $lead->getMedia($request->collection);

        $existingMedia->each(function ($oldMedia) {
            $fileName = pathinfo(request()->file('file')->getClientOriginalName(), PATHINFO_FILENAME);

            if ($fileName === $oldMedia->name) {
                $oldMedia->delete();
            }
        });

        $lead->addMediaFromRequest('file')
            ->toMediaCollection($request->collection);

        return null_resource();
    }

    public function handleFileUpload(UploadLeadFileRequest $request, UploadLeadsFileAction $action): JsonResponse
    {
        return $action->execute($request);
    }

    public function downloadDatamatch()
    {
        $Model = DataMatchFile::make();
        $Model->id = (string) Str::uuid();

        $fileName = 'data_match_file_'.now()->format('YmdHis').'.xlsx';
        // Store on default disk
        $result = Excel::store(new DatamatchExport(), "DataMatch/{$Model->id}/{$fileName}", 'public');
        if ($result) {

            $Model->file_name = $fileName;
            $Model->file_path = asset("storage/DataMatch/{$Model->id}/{$fileName}");
            $Model->created_by = auth()->user()->id;
            $Model->save();

            return $this->success(data: asset("storage/DataMatch/{$Model->id}/{$fileName}"));
        } else {
            return $this->error(message: 'Something went wrong');
        }
    }

    public function uploadDatamatch(UploadDataMatchRequest $request, UploadLeadsFileAction $action)
    {
        return $action->executeLeadsDataMatchResultUpload($request);
    }

    public function getDataMatchResultsFileLink(GetAllDataMatchFilesRequest $request, ListDataMatchAction $action)
    {
        $action->enableQueryBuilder();

        return $action->resourceCollection($action->listOrPaginate());
    }
}
