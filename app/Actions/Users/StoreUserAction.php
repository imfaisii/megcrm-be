<?php

namespace App\Actions\Users;

use App\Actions\Common\AbstractCreateAction;
use App\Models\User;
use Exception;

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

    return $user;
  }
}
