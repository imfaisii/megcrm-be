<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Actions\Common\BaseModel;
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

use function App\Helpers\get_permissions_as_modules_array;

class User extends BaseModel implements AuthenticatableContract
{
    use HasApiTokens, HasFactory, Authenticatable, Notifiable, HasRoles, LaravelPermissionToVueJS;

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

    protected array $allowedIncludes = [];

    protected $appends = ['rights', 'top_role'];

    public function getRightsAttribute()
    {
        return $this->getPermissions();
    }

    public function getTopRoleAttribute()
    {
        return Str::ucfirst(Str::replace("_", " ", Arr::first($this->getRoleNames())));
    }

    // public function getPermissions()
    // {
    //     $permissions = $this->getAllPermissions();


    //     return [
    //         'roles' => $this->getRoleNames(),
    //         'permissions' => get_permissions_as_modules_array($permissions),
    //     ];
    // }

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
}
