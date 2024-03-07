<?php

namespace App\Http\Controllers\Permissions;

use App\Actions\Permissions\ListRoleAction;
use App\Actions\Permissions\StoreRoleAction;
use App\Actions\Permissions\UpdateRoleAction;
use App\Enums\Permissions\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Permissions\StoreRoleRequest;
use App\Http\Requests\Permissions\UpdateRoleRequest;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

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

        return $action->individualResource($role->load('permissions'));
    }

    public function update(Role $role, UpdateRoleAction $action, UpdateRoleRequest $request): JsonResource
    {
        $role = $action->update($role, $request->validated());

        return $action->individualResource($role->load('permissions'));
    }

    public function destroy(Role $role)
    {
        if ($role->name === RoleEnum::SUPER_ADMIN) {
            return $this->error('Cannot delete super admin.');
        }

        DB::table('roles')->where('id', $role->id)->delete();

        return null_resource();
    }
}
