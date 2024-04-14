<?php

namespace App\Http\Controllers\File;

use App\Enums\AppEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\File\FileDeleteRequest;
use App\Http\Requests\File\FileUploadRequest;
use App\Http\Requests\File\GetFilesRequest;
use App\traits\Jsonify;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

use function App\Helpers\CopyFilefromSourceToDestination;
use function App\Helpers\generateUniqueRandomStringWithTimeStamp;
use function App\Helpers\meg_decrypts;

class FileHanlderController extends Controller
{
    use Jsonify;

    public function upload(string $Model, string $ID, FileUploadRequest $request)
    {
        try {
            $Model = meg_decrypts($Model);
            $decryptedId = meg_decrypts($ID);
            $modelObject = resolve("App\Models\\$Model")->findOrFail($decryptedId);
            $mediaObjects = $modelObject->getMedia($request->get('collection_name', AppEnum::Default_MediaType));
            if ($request->collection_name == AppEnum::CUSTOMER_LEAD_IMAGES && $mediaObjects->count() > AppEnum::DEFAULT_LIMIT_FOR_MEDIA_FILE_CUSTOMER) {
                return $this->error('You cannot upload more than'.AppEnum::DEFAULT_LIMIT_FOR_MEDIA_FILE_CUSTOMER.' images.');
            } elseif ($request->collection_name == AppEnum::CUSTOMER_LEAD_DOCUMENTS && $mediaObjects->count() > AppEnum::DEFAULT_LIMIT_FOR_SUPPORTING_DOCUMENTS_FILE_CUSTOMER) {
                return $this->error('You cannot upload more than'.AppEnum::DEFAULT_LIMIT_FOR_SUPPORTING_DOCUMENTS_FILE_CUSTOMER.' docuements.');
            }

            $response = $modelObject->addMediaFromRequest('image')
                ->usingFileName(generateUniqueRandomStringWithTimeStamp().$request->file('image')->getClientOriginalName())
                ->withCustomProperties([
                    'ip' => $request->ip(),
                    'agent' => $request->header('User-Agent'),
                    'original_name' => $request->file('image')->getClientOriginalName(),
                    'original_extension' => $request->file('image')->getClientOriginalExtension(),
                ])
                ->toMediaCollection($request->get('collection_name', AppEnum::Default_MediaType), 'public');

            return $response->uuid ?: '';
        } catch (Exception $e) {
            return $this->exception($e);

        }
    }

    public function delete(string $Model, string $ID, FileDeleteRequest $request)
    {
        try {
            $Model = meg_decrypts($Model);
            $decryptedId = meg_decrypts($ID);
            $modelObject = resolve("App\Models\\$Model")->findOrFail($decryptedId);
            $mediaObjects = $modelObject->getMedia($request->get('collection_name', AppEnum::Default_MediaType));
            $toDelMedia = $mediaObjects->firstOrFail(function ($object, int $key) use ($request) {
                return $object->uuid === $request->get('image');
            });
            $copyResponse = CopyFilefromSourceToDestination(Str::after($toDelMedia->getUrl(), 'storage/'), AppEnum::DEFAULT_MEDIA_DELETED_LOCATION."/{$Model}/{$decryptedId}/".$toDelMedia->file_name);
            if ($copyResponse['success']) {
                $toDelMedia->delete(); // all associated files will be preserved

                return $this->success($copyResponse['message']);
            } else {
                return $this->error($copyResponse['message']);
            }
        } catch (Exception $e) {
            return $this->exception($e);

        }
    }

    public function load(Media $Media, Request $request)
    {
        // this is supposed to send an file object , as being used by the file pond load
        try {
            if (blank($Media)) {
                return $this->error('file not found ');
            }
            if (Storage::disk('public')->exists(Str::after($Media?->getUrl(), 'storage/'))) {
                // Get the file's MIME type
                $imagePath = Str::after($Media->getUrl(), 'storage/');
                $mimeType = Storage::disk('public')->mimeType($imagePath);

                // Return the file as a response with appropriate headers
                return response()->file(public_path('storage/'.$imagePath), [
                    'Content-Disposition' => 'inline; filename="'.basename($imagePath).'"',

                    'Content-Type' => $mimeType,
                ]);
            } else {
                return $this->error('file not found ');
            }
        } catch (Exception $e) {
            return $this->exception($e);

        }
    }

    public function getAllFilesAssocaiatedWithModel(string $Model, string $ID, GetFilesRequest $request)
    {
        try {
            $type = strtolower($request->get('type', 'files'));
            $Model = meg_decrypts($Model);
            $decryptedId = meg_decrypts($ID);
            $modelObject = resolve("App\Models\\$Model")->findOrFail($decryptedId);
            $mediaObjects = $modelObject->getMedia($request->get('collection_name', AppEnum::Default_MediaType));
            $mediaObjects = $mediaObjects->map(function (Media $Media) use ($type) {
                if ($type === 'files') {
                    return $Media->getUrl();

                } else {
                    return $Media->uuid;
                }
            });

            return $this->success(data: $mediaObjects);
        } catch (Exception $e) {
            return $this->exception($e);

        }
    }
}
