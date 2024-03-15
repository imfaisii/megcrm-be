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
        } else if (auth()?->user()?->hasAnyRole(RoleEnum::SUPER_ADMIN, ...$bypassRole)) {
            $query;
        } elseif (auth()?->user()?->hasRole(RoleEnum::TEAM_ADMIN)) {
            // get all the team members ids and then get those leads
            // if its admin add  lead generator as well
            $query->where(function ($q) {
                return $q->whereIn($this->ScopeColumn ?? 'user_id', Arr::get($this->getTeams(), 'members', []))
                    ->orWhereIn('lead_generator_id', Arr::get($this->getTeams(), 'lead_generators', []));
            });
            // $query->whereIn($this->ScopeColumn ?? 'user_id', Arr::get($this->getTeams(), 'members', []));
        } else {
            $query->where(function ($query) {
                // if its a single member then check also its lead generators

                if (
                    !auth()->user()->hasRole(RoleEnum::SUPER_ADMIN)
                    && auth()->user()->hasRole(RoleEnum::SURVEYOR)
                ) {
                    $query->byRole(RoleEnum::SURVEYOR);
                }
                $query->Orwhere($this->ScopeColumn ?? 'user_id', auth()->id());

            });
        }
    }


    public function getTeams(?int $id = null): array
    {
        if (request()->__isset(config('app.key_for_request_team_cache'))) {
            // the teams are already fetched in this request
            return request()->get(config('app.key_for_request_team_cache'));
        }
        $user = User::Has('myteams')->with('teams.pivot.role', 'teams.users.leadGeneratorAssignments')->find($id ?? auth()->id());
        $myTeams = $user?->teams?->map(function ($model) {
            return $model->id;
        })?->flatten()->all();
        $leadGenerators = [];
        $myMembers = $user?->teams?->map(function ($model) use (&$leadGenerators) {
            $leadGenerators[] = $model->users->map(function ($modelForLeadGen) {
                return $modelForLeadGen->leadGeneratorAssignments?->pluck('id')?->toArray();
            });
            return $model->users?->pluck('id')->toArray();
        })?->flatten()->all();
        $teams = [
            'teams' => $myTeams,
            'members' => $myMembers ?: [$id ?? auth()->id()],
            'lead_generators' => Arr::flatten($leadGenerators),
        ];
        request()->offsetSet(config('app.key_for_request_team_cache'), $teams);
        return $teams;

        // for each request we will add this to the request and if found in the request we will sent back the result instead of queries
    }
}
