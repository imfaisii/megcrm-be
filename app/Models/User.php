<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use AjCastro\EagerLoadPivotRelations\EagerLoadPivotTrait;
use App\Actions\Common\BaseModel;
use App\Filters\Users\FilterByGivenRole;
use App\Filters\Users\FilterByRole;
use App\Includes\Users\UserNotificationsInclude;
use App\Traits\Common\HasCalenderEvent;
use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AccessAuthorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Laravel\Sanctum\HasApiTokens;
use LaravelAndVueJS\Traits\LaravelPermissionToVueJS;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;

use function App\Helpers\is_append_present;
use function App\Helpers\split_name;

class User extends BaseModel implements AccessAuthorizable, AuthenticatableContract, CanResetPasswordContract, HasMedia
{
    use Authenticatable,
        AuthenticationLoggable,
        Authorizable,
        CanResetPassword,
        CausesActivity,
        EagerLoadPivotTrait,
        HasApiTokens,
        HasCalenderEvent,
        HasFactory,
        HasPermissions,
        HasRoles,
        InteractsWithMedia,
        LaravelPermissionToVueJS,
        Notifiable;

    protected $guard_name = 'sanctum';

    protected $fillable = [
        'name',
        'email',
        'password',
        'air_caller_id',
        'is_active',
        'company_id',
        'is_associated_with_company',
        'phone_number_aircall',
        'aircall_email_address',
        'created_by_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'phone_number_aircall',
        'air_caller_id',
        'aircall_email_address',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'is_associated_with_company' => 'boolean',
    ];

    protected $with = ['additional'];

    protected array $allowedIncludes = [
        'createdBy',
        'leadGeneratorAssignments',
        'notifications',
        'authentications',
        'company',
        'additional.bank',
    ];

    protected $appends = ['rights', 'top_role', 'user_agents', 'first_name', 'last_name'];

    public function notifyAuthenticationLogVia()
    {
        return ['mail', 'slack'];
    }

    protected function getExtraFilters(): array
    {
        return [
            AllowedFilter::custom('roles_except', new FilterByRole()),
            AllowedFilter::custom('roles', new FilterByGivenRole()),
        ];
    }

    protected function getExtraIncludes(): array
    {
        return [
            AllowedInclude::custom('latestNotifications', new UserNotificationsInclude()),
        ];
    }

    public function getRightsAttribute()
    {
        if (is_append_present('rights')) {
            return $this->getPermissions();
        }

        return null;
    }

    public function getTopRoleAttribute()
    {
        return Str::ucfirst(Str::replace('_', ' ', Arr::first($this->getRoleNames())));
    }

    public function getUserAgentsAttribute($count = 5)
    {
        if (is_append_present('authentications')) {
            return $this->authentications->take($count)->map(function ($log) {
                $agent = tap(new Agent, fn ($agent) => $agent->setUserAgent($log->user_agent));

                return [
                    'is_mobile' => ($agent->isMobile() || $agent->isTablet()) ? true : false,
                    'device' => $agent->device() === false ? 'WebKit' : $agent->device(),
                    'platform' => $agent->platform() === false ? 'Windows' : $agent->platform(),
                    'browser' => $agent->browser() === false ? 'Chrome' : $agent->browser(),
                    'login_at' => Carbon::parse($log->login_at)->format('l M d g:i a'),
                    'country' => $log->location['country'],
                    'ip' => $log->location['ip'],
                    'timezone' => $log->location['timezone'],
                ];
            })
                ->unique('login_at');
        }

        return null;
    }

    public function getFirstNameAttribute()
    {
        return split_name($this->name)['first_name'];
    }

    public function getLastNameAttribute()
    {
        return split_name($this->name)['last_name'];
    }

    public function additional()
    {
        return $this->hasOne(UserAdditional::class);
    }

    public function scopeActive(Builder $builder)
    {
        return $builder->where('is_active', true);
    }

    public function scopeInActive(Builder $builder)
    {
        return $builder->where('is_active', false);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'created_by_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function leadGeneratorAssignments()
    {
        return $this->belongsToMany(LeadGenerator::class, LeadGeneratorAssignment::class)
            ->withPivot('created_by_id')
            ->withTimestamps();
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function installationTypes()
    {
        return $this->belongsToMany(InstallationType::class, UserHasInstallationType::class)
            ->withPivot('created_by_id')
            ->withTimestamps();
    }

    public function routeNotificationForSlack()
    {
        return data_get(config('logging.channels.slack-crm'), 'url');
    }

    public function leadGeneratorManagers()
    {
        return $this->hasMany(LeadGeneratorManager::class);
    }

    /* which team i am part of */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, TeamUsers::class, 'user_id', 'team_id')->withPivot(['role_id'])->withTimestamps();
    }

    /* which team i am admin of */
    public function myteams(): HasMany
    {
        return $this->hasMany(Team::class, 'admin_id');
    }
}
