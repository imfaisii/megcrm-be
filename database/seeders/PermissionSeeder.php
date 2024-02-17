<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

use function App\Helpers\get_permissions_by_routes;

class PermissionSeeder extends Seeder
{
    protected $customPermissions = [
        'leads' => [
            [
                'name' => 'assigned-leads',
                'method' => 'index'
            ]
        ],
        'lead-jobs' => [
            [
                'name' => 'assigned-jobs',
                'method' => 'index'
            ]
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissionsByRoutes = get_permissions_by_routes();

        foreach ($this->customPermissions as $key => $value) {
            if (!isset($permissionsByRoutes[$key])) {
                $permissionsByRoutes[$key] = $value;
            } else {
                if (is_array($permissionsByRoutes[$key])) {
                    if (is_array($value)) {
                        $permissionsByRoutes[$key] = array_merge($permissionsByRoutes[$key], $value);
                    } else {
                        $permissionsByRoutes[$key][] = $value;
                    }
                } else {
                    $permissionsByRoutes[$key] = $value;
                }
            }
        }

        foreach ($permissionsByRoutes as $module => $subModules) {
            Permission::firstOrCreate(['name' => $module, 'is_module' => true, 'guard_name' => 'sanctum']);

            foreach ($subModules as $key => $subModule) {
                Permission::firstOrCreate([
                    'name' => "{$module}.{$subModule['name']}",
                    'parent_module_name' => $module,
                    'method' => $subModule['method'],
                    'guard_name' => 'sanctum'
                ]);
            }
        }
    }
}
