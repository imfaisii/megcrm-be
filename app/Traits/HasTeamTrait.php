<?php

namespace App\Traits;

use App\Enums\Permissions\RoleEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

trait HasTeamTrait
{
    /******** This is basically for handling the team assignments  **********/
    public static function bootHasTeamTrait()
    {
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeTeamScope(Builder $query, array $ids = []): void
    {
        if (filled($ids)) {
            $query->whereIn($this->ScopeColumn ?? 'user_id', $ids);
        }
        if (auth()?->user()?->hasRole(RoleEnum::SUPER_ADMIN)) {
            $query;
        } elseif (auth()?->user()?->hasRole(RoleEnum::TEAM_ADMIN)) {
            // get all the team members ids and then get those leads
            $query->whereIn($this->ScopeColumn ?? 'user_id', Arr::get($this->getTeams(), 'members', []));
        } else {
            $query->where($this->ScopeColumn ?? 'user_id', auth()->id());
        }

    }


    public function getTeams(): array
    {
        $user = User::Has('myteams')->with('teams.pivot.role', 'teams.users')->find(auth()->id());
        $myTeams = $user?->teams?->map(function ($model) {
            return $model->id;
        })?->flatten()->all();
        $myMembers = $user?->teams?->map(function ($model) {
            return $model->users?->pluck('id')->toArray();
        })?->flatten()->all();
        return [
            'teams' => $myTeams,
            'members' => $myMembers ?: [auth()->id()],
        ];
    }
}
