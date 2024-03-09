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
    public function scopeTeamScope(Builder $query, array $ids = [], array $bypassRole = []): void
    {
        if (filled($ids)) {
            $query->whereIn($this->ScopeColumn ?? 'user_id', $ids);
        } else if (auth()?->user()?->hasAnyRole(RoleEnum::SUPER_ADMIN,...$bypassRole)) {
            $query;
        } elseif (auth()?->user()?->hasRole(RoleEnum::TEAM_ADMIN)) {
            // get all the team members ids and then get those leads
            $query->whereIn($this->ScopeColumn ?? 'user_id', Arr::get($this->getTeams(), 'members', []));
        } else {
            $query->where($this->ScopeColumn ?? 'user_id', auth()->id());
        }

    }


    public function getTeams(?int $id = null): array
    {
        if (request()->__isset(config('app.key_for_request_team_cache'))) {
            // the teams are already fetched in this request
            return request()->get(config('app.key_for_request_team_cache'));
        }
        $user = User::Has('myteams')->with('teams.pivot.role', 'teams.users')->find($id ?? auth()->id());
        $myTeams = $user?->teams?->map(function ($model) {
            return $model->id;
        })?->flatten()->all();
        $myMembers = $user?->teams?->map(function ($model) {
            return $model->users?->pluck('id')->toArray();
        })?->flatten()->all();
        $teams = [
            'teams' => $myTeams,
            'members' => $myMembers ?: [$id ?? auth()->id()],
        ];
        request()->offsetSet(config('app.key_for_request_team_cache'), $teams);
        return $teams;

        // for each request we will add this to the request and if found in the request we will sent back the result instead of queries
    }
}
