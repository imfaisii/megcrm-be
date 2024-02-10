<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Actions\Common\BaseModel;
use App\Filters\Users\FilterByRole;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use LaravelAndVueJS\Traits\LaravelPermissionToVueJS;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\QueryBuilder\AllowedFilter;

class User extends BaseModel implements AuthenticatableContract
{
    use HasApiTokens, HasFactory, Authenticatable, Notifiable, HasRoles, LaravelPermissionToVueJS, CausesActivity;

    protected $guard_name = 'sanctum';

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'created_by_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean'
    ];

    protected array $allowedAppends = [];

    protected array $allowedIncludes = [
        'createdBy',
        'leadGeneratorAssignments'
    ];

    protected $appends = ['rights', 'top_role'];

    protected function getExtraFilters(): array
    {
        return [
            AllowedFilter::custom('roles_except', new FilterByRole()),
        ];
    }

    public function getRightsAttribute()
    {
        return $this->getPermissions();
    }

    public function getTopRoleAttribute()
    {
        return Str::ucfirst(Str::replace("_", " ", Arr::first($this->getRoleNames())));
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
    public function routeNotificationForSlack()
    {
        return data_get(config('logging.channels.slack-crm'),'url');
    }
}
