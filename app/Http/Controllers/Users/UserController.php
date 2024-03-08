<?php

namespace App\Http\Controllers\Users;

use App\Actions\Common\BaseJsonResource;
use App\Actions\Users\DeleteUserAction;
use App\Actions\Users\FindUserAction;
use App\Actions\Users\ListUserAction;
use App\Actions\Users\StoreUserAction;
use App\Actions\Users\UpdateUserAction;
use App\Actions\Users\UpdateUserProfileAction;
use App\Enums\Users\MediaCollectionEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateDocumentExpiryDate;
use App\Http\Requests\Users\UpdateProfileRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Requests\Users\UploadDocumentRequest;
use App\Http\Requests\Users\UploadDocumentsToCollectionRequest;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

use function App\Helpers\null_resource;

class UserController extends Controller
{
    public function index(ListUserAction $action): ResourceCollection
    {
        $action->enableQueryBuilder();

        return $action->resourceCollection($action->listOrPaginate());
    }

    public function store(StoreUserRequest $request, StoreUserAction $action): JsonResource
    {
        $user = $action->create($request->validated());

        return $action->individualResource($user);
    }

    public function show(int $id, FindUserAction $action): BaseJsonResource
    {
        $action->enableQueryBuilder();

        return $action->individualResource($action->findOrFail($id));
    }

    public function update(User $user, UpdateUserRequest $request, UpdateUserAction $action): BaseJsonResource
    {
        $action->enableQueryBuilder();

        return $action->individualResource($action->update($user, $request->validated()));
    }

    public function currentUser(FindUserAction $action): BaseJsonResource
    {
        $action->enableQueryBuilder();

        return $action->individualResource($action->findOrFail(auth()->id()));
    }

    public function destroy(User $user, DeleteUserAction $action): BaseJsonResource
    {
        $action->delete($user);

        return null_resource();
    }

    public function uploadDocumentToCollection(User $user, UploadDocumentsToCollectionRequest $request)
    {
        $existingMedia = $user->getMedia($request->collection);

        $existingMedia->each(function ($oldMedia) {
            $fileName = pathinfo(request()->file('file')->getClientOriginalName(), PATHINFO_FILENAME);

            if ($fileName === $oldMedia->name) {
                $oldMedia->delete();
            }
        });

        $user->addMediaFromRequest('file')
            ->withCustomProperties(['expiry' => $request->expiry])
            ->toMediaCollection($request->collection);

        return null_resource();
    }

    public function updateDocumentExpiry(Media $media, UpdateDocumentExpiryDate $request)
    {
        $media->setCustomProperty('expiry', $request->expiry)->save();

        return null_resource();
    }

    public function uploadDocument(User $user, UploadDocumentRequest $request)
    {
        $user->addMediaFromRequest('file')->toMediaCollection(MediaCollectionEnum::DOCUMENTS);

        return null_resource();
    }

    public function updateUserProfile(User $user, UpdateProfileRequest $request, UpdateUserProfileAction $action): BaseJsonResource
    {
        $action->update($user, $request->all());

        return null_resource();
    }
}
