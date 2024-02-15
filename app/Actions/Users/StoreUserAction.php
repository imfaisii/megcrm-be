<?php

namespace App\Actions\Users;

use App\Actions\Common\AbstractCreateAction;
use App\Events\Users\NewUserCreated;
use App\Mail\Mails\Users\NewUserCredentialsMail;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Mail;

class StoreUserAction extends AbstractCreateAction
{
    protected string $modelClass = User::class;

    public function create(array $data): User
    {
        /** @var User $user */
        $data['created_by_id'] = auth()->id();

        $user = parent::create($data);

        if (isset($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        try {
            event(new NewUserCreated(user: $user));
        } catch (Exception $e) {
        }

        return $user;
    }
}
