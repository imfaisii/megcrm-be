<?php

namespace App\Policies;

use App\Enums\Permissions\RoleEnum;
use App\Models\User;
use App\Traits\HasTeamTrait;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;

class AbstractPolicy
{

    use HasTeamTrait {
        getTeams as public getTeamsForUsers; // Rename and make it public in the class
    }
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks on the model.
     */
    public function before(Model $user, string $ability): bool|null
    {

        if ($user->hasAnyRole([RoleEnum::SUPER_ADMIN,RoleEnum::CSR])) {
            return true;
        }

        return null; // see the note above in Gate::before about why null must be returned here.
    }

    public function viewAny(Model $user)
    {
        return $user->can(request()->route()->getName()); // the user has permission for this action
    }


    public function view(?Model $user, Model $relatedModel)
    {
        if ($user->cannot(request()->route()->getName())) {
            return false;
        } else if ($user->hasRole(RoleEnum::TEAM_ADMIN)) {
            ['members' => $members] = $this->getTeamsForUsers();
            return in_array($relatedModel->{$relatedModel->ScopeColumn}, $members);
        } else {
            return $relatedModel->{$relatedModel->ScopeColumn} == $user?->id;
        }
    }


    public function create(Model $user)
    {
        return $user->can(request()->route()->getName()); // the user has permission for this action

    }


    public function update(Model $user, Model $relatedModel)
    {
        if ($user->cannot(request()->route()->getName())) {
            return false;
        } else if ($user->hasRole(RoleEnum::TEAM_ADMIN)) {
            ['members' => $members] = $this->getTeamsForUsers();
            return in_array($relatedModel->{$relatedModel->ScopeColumn}, $members);
        } else {
            return $relatedModel->{$relatedModel->ScopeColumn} == $user?->id;
        }
    }


    public function delete(Model $user, Model $relatedModel)
    {
        if ($user->cannot(request()->route()->getName())) {
            return false;
        } else if ($user->hasRole(RoleEnum::TEAM_ADMIN)) {
            ['members' => $members] = $this->getTeamsForUsers();
            return in_array($relatedModel->{$relatedModel->ScopeColumn}, $members);
        } else {
            return $relatedModel->{$relatedModel->ScopeColumn} == $user?->id;
        }
    }


    public function restore(Model $user, Model $relatedModel)
    {
        //
    }


    public function forceDelete(Model $user, Model $relatedModel)
    {
        //
    }

}
