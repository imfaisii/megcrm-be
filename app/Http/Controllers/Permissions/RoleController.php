<?php

namespace App\Http\Controllers\Permissions;

use App\Actions\Permissions\DeleteRoleAction;
use App\Actions\Permissions\ListRoleAction;
use App\Actions\Permissions\StoreRoleAction;
use App\Actions\Permissions\UpdateRoleAction;
use App\Enums\Permissions\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Permissions\StoreRoleRequest;
use App\Http\Requests\Permissions\UpdateRoleRequest;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Permission\Contracts\Role;
use Spatie\Permission\Models\Role as ModelsRole;

use function App\Helpers\null_resource;

class RoleController extends Controller
{
    public function index(ListRoleAction $action)
    {
        $action->enableQueryBuilder();
        return $action->resourceCollection($action->listOrPaginate());
    }

    public function store(StoreRoleRequest $request, StoreRoleAction $action): JsonResource
    {
        $role = $action->create($request->validated());
        return $action->individualResource($role);
    }

    public function update(Role $role, UpdateRoleAction $action, UpdateRoleRequest $request): JsonResource
    {
        $role = $action->update($role, $request->validated());
        return $action->individualResource($role);
    }

    public function destroy($id, DeleteRoleAction $action)
    {
        $role = ModelsRole::where('id', $id)->first();

        if ($role) {
            if ($role->name !== RoleEnum::SUPER_ADMIN) {
                $action->delete($role);
            }

            return null_resource();
        }

        return $this->error('No role exists with this id');
    }
}
