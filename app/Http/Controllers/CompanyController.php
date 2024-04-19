<?php

namespace App\Http\Controllers;

use App\Actions\Common\BaseJsonResource;
use App\Actions\Companies\DeleteCompanyAction;
use App\Actions\Companies\FindCompanyAction;
use App\Actions\Companies\ListCompanyAction;
use App\Actions\Companies\StoreCompanyAction;
use App\Actions\Companies\UpdateCompanyAction;
use App\Http\Requests\Companies\StoreCompanyRequest;
use App\Http\Requests\Companies\UpdateCompanyRequest;
use App\Http\Requests\Users\UpdateDocumentExpiryDate;
use App\Http\Requests\Users\UploadDocumentsToCollectionRequest;
use App\Models\Company;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

use function App\Helpers\null_resource;

class CompanyController extends Controller
{
    public function index(ListCompanyAction $action): ResourceCollection
    {
        $action->enableQueryBuilder();

        return $action->resourceCollection($action->listOrPaginate());
    }

    public function show(int $id, FindCompanyAction $action): BaseJsonResource
    {
        $action->enableQueryBuilder();

        return $action->individualResource($action->findOrFail($id));
    }

    public function store(StoreCompanyRequest $request, StoreCompanyAction $action)
    {
        $company = $action->create($request->validated());

        return $action->individualResource($company);
    }

    public function update(Company $Company, UpdateCompanyRequest $request, UpdateCompanyAction $action): BaseJsonResource
    {
        $action->enableQueryBuilder();

        return $action->individualResource($action->update($Company, $request->validated()));
    }

    public function uploadDocumentToCollection(Company $company, UploadDocumentsToCollectionRequest $request)
    {
        $existingMedia = $company->getMedia($request->collection);

        $existingMedia->each(function ($oldMedia) {
            $fileName = pathinfo(request()->file('file')->getClientOriginalName(), PATHINFO_FILENAME);

            if ($fileName === $oldMedia->name) {
                $oldMedia->delete();
            }
        });

        $company->addMediaFromRequest('file')
            ->withCustomProperties(['expiry' => $request->expiry])
            ->toMediaCollection($request->collection);

        return null_resource();
    }

    public function updateDocumentExpiry(Media $media, UpdateDocumentExpiryDate $request)
    {
        $media->setCustomProperty('expiry', $request->expiry)->save();

        return null_resource();
    }

    public function destroy(Company $Company, DeleteCompanyAction $action): BaseJsonResource
    {
        $action->delete($Company);

        return null_resource();
    }
}
