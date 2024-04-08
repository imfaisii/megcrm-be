<?php

namespace App\Actions\Users;

use App\Actions\Common\AbstractCreateAction;
use App\Models\Bank;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class StoreUserAction extends AbstractCreateAction
{
    protected string $modelClass = User::class;

    protected $relations = ['additional', 'company', 'installation_types'];

    public function create(array $data): User
    {
        /** @var User $user */
        $data['created_by_id'] = auth()->id();

        $user = parent::create($data);

        if (isset($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        $this->relations($user, $data);

        return $user;
    }

    public function relations(User $user, array $data): void
    {
        foreach ($this->relations as $key => $relation) {
            if ($relation === 'installation_types') {
                if (Arr::has($data, 'installation_types') && count($data['installation_types']) > 0) {
                    $user->installationTypes()->syncWithPivotValues($data['installation_types'], [
                        'created_by_id' => auth()->id(),
                    ]);
                }
            } else {
                $snakeCase = Str::snake($relation);

                if (array_key_exists($snakeCase, $data)) {
                    $user->$relation()->updateOrCreate([
                        'user_id' => $user->id,
                    ], $data[$snakeCase]);
                }
            }
        }

        if (Arr::get($data, 'additional.bank', null)) {
            $bank = Bank::firstOrCreate([
                'name' => Str::upper($data['additional']['bank']),
            ]);

            $user->additional->update([
                'bank_id' => $bank->id,
            ]);
        }
    }
}
