<?php

namespace App\Actions\Users;

use App\Actions\Common\AbstractFindAction;
use App\Actions\Common\BaseModel;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Http;

class FindUserAction extends AbstractFindAction
{
    protected string $modelClass = User::class;

    protected string $dropboxRefreshTokenUrl = "https://api.dropbox.com/oauth2/token";

    public function findOrFail($primaryKey, array $columns = ['*']): BaseModel
    {
        $user = $this->getQuery()->findOrFail($primaryKey, $columns);

        try {
            $response =  Http::asForm()->post($this->dropboxRefreshTokenUrl, [
                "grant_type" => "refresh_token",
                "client_id" => config('services.dropbox.key'),
                "client_secret" => config('services.dropbox.secret'),
                "refresh_token" => config('services.dropbox.refresh_token'),
            ]);

            $user['dropbox'] = [
                'status' => true,
                'data' => $response->json()['access_token']
            ];
        } catch (Exception $e) {
            $user['dropbox'] = [
                'status' => false,
                'data' => $e->getMessage()
            ];
        }

        return $user;
    }
}
