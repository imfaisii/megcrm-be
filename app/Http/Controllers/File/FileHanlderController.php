<?php

namespace App\Http\Controllers\File;

use App\Enums\AppEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\File\FileDeleteRequest;
use App\Http\Requests\File\FileUploadRequest;
use App\Models\Lead;
use App\traits\Jsonify;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use function App\Helpers\CopyFilefromSourceToDestination;
use function App\Helpers\generateUniqueRandomStringWithTimeStamp;
use function App\Helpers\meg_decrypts;

class FileHanlderController extends Controller
{
    use Jsonify;
    public function upload(string $Model, string $ID, FileUploadRequest $request)
    {
        try {
            $decryptedId = meg_decrypts($ID);
            $modelObject = resolve("App\Models\\$Model")->findOrFail($decryptedId);
            $modelObject->addMediaFromRequest('image')
                ->usingFileName(generateUniqueRandomStringWithTimeStamp() . $request->file('image')->getClientOriginalName())
                ->withCustomProperties([
                    'ip' => $request->ip(),
                    'agent' => $request->header('User-Agent'),
                    'original_name' => $request->file('image')->getClientOriginalName(),
                    'original_extension' => $request->file('image')->getClientOriginalExtension(),
                ])
                ->toMediaCollection($request->get('collection_name', AppEnum::Default_MediaType));

        } catch (Exception $e) {
            return $this->exception($e);

        }
    }

    public function delete(string $Model, string $ID, FileDeleteRequest $request)
    {
        try {
            $decryptedId = meg_decrypts($ID);
            $modelObject = resolve("App\Models\\$Model")->findOrFail($decryptedId);
            $mediaObjects = $modelObject->getMedia($request->get('collection_name', AppEnum::Default_MediaType));

            $toDelMedia = $mediaObjects->firstOrFail(function ($object, int $key) use ($request) {
                return $object->uuid === $request->get('image');
            });
            CopyFilefromSourceToDestination(Str::after($toDelMedia->getUrl(), 'storage/'),  AppEnum::DEFAULT_MEDIA_DELETED_LOCATION . "/{$Model}/{$decryptedId}/" . $toDelMedia->file_name);
            // $toDelMedia->delete(); // all associated files will be preserved
        } catch (Exception $e) {
            return $this->exception($e);

        }
    }


}
