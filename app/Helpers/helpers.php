<?php

namespace App\Helpers;

use App\Actions\Common\BaseJsonResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

function null_resource(): JsonResource
{
    return new BaseJsonResource(null);
}

function get_permissions_by_routes(): array
{
    $routeCollection = Route::getRoutes()->get();
    $permissions = [];

    foreach ($routeCollection as $item) {
        $name = $item->action;
        if (!empty ($name['as'])) {
            $permission = $name['as'];
            $permission = trim(strtolower($permission));
            $ignoreRoutesStartingWith = 'sanctum|livewire|ignition|notifications|log-viewer|debugbar';
            $permissionFilled = trim(str_replace('user management ', '', $permission));
            if (preg_match("($ignoreRoutesStartingWith)", $permission) === 0 && filled($permissionFilled)) {
                $method = $item->getActionMethod();

                if (strpos($method, '\\') !== false) {
                    $method = '__invoke';
                }

                $permissions[] = ['name' => $permissionFilled, 'method' => $method];
            }
        }
    }

    return get_modules_array_from_permissions($permissions);
}

function get_modules_array_from_permissions(array $permissions): array
{
    $modules = [];

    foreach ($permissions as $item) {
        $parts = explode('.', $item['name']);
        $module = $parts[0];
        $submodule = implode('.', array_slice($parts, 1));

        if (!isset ($modules[$module])) {
            $modules[$module] = [];
        }

        if (!in_array($submodule, $modules[$module])) {
            array_push($modules[$module], ['name' => $submodule, 'method' => $item['method']]);
        }
    }

    foreach ($modules as &$submodules) {
        sort($submodules);
    }

    return $modules;
}

function get_all_includes(): array
{
    $includes = request()->input('include');
    if ($includes === null) {
        return [];
    } elseif (is_array($includes)) {
        return $includes;
    } else {
        return explode(',', $includes);
    }
}

function get_all_includes_in_camel_case(): array
{
    return collect(get_all_includes())
        ->map(function (string $includes) {
            return collect(explode('.', $includes))
                ->map(fn(string $include) => Str::camel($include))
                ->join('.');
        })
        ->toArray();
}

function get_all_appends(): array
{
    $appends = request()->input('append');
    if ($appends === null) {
        return [];
    } elseif (is_array($appends)) {
        return $appends;
    } else {
        return explode(',', $appends);
    }
}

function is_include_present(string $include): bool
{
    return in_array(Str::snake($include), get_all_includes());
}

function is_append_present(string $append): bool
{
    return in_array(Str::snake($append), get_all_appends());
}

function get_permissions_as_modules_array(mixed $permissions): array
{
    $finalPermissions = [];
    $modules = $permissions->where('parent_module_name', null)->pluck('name')->toArray();
    $modulesThroughSubmodules = $permissions->pluck('name');

    foreach ($modulesThroughSubmodules as $key => $submodule) {
        try {
            $moduleName = explode('.', $submodule)[0];
            if (!in_array($moduleName, $modules)) {
                $modules[] = $moduleName;
            }
        } catch (\Exception $e) {
            //
        }
    }

    foreach ($modules as $module) {
        $modulePermissions = $permissions->filter(function ($permission) use ($module) {
            return strpos($permission['name'], $module) === 0 && $permission['name'] !== $module;
        })->map(function ($permission) use ($module) {
            $name = Str::ucfirst(Str::replace('.', ' ', Str::replace("{$module}.", '', $permission['name'])));

            return [
                'id' => $permission['id'],
                'name' => match ($name) {
                    'Index' => 'Can view records',
                    'Destroy' => 'Can delete records',
                    'Show' => 'Can view record details',
                    'Store' => 'Can save new record',
                    'Update' => 'Can update old record',
                    default => $name
                },
            ];
        })->toArray();

        $moduleObject = [
            'name' => $module,
            'submodules' => array_values($modulePermissions),
        ];

        $finalPermissions[] = $moduleObject;
    }

    return $finalPermissions;
}

function shouldAppend(string $append): bool
{
    $appends = [];

    if (request()->has('append')) {
        $appends = explode(',', request()->get('append'));
    }

    if (!in_array($append, $appends)) {
        return false;
    }

    return true;
}

function formatCommas($address): string
{
    // Remove commas followed by a space
    $address = preg_replace('/,(?=\s)/', '', $address);   // woh comma remove kro jis k agy space h

    // Remove commas not followed by a space
    $address = preg_replace('/,(?!\s)/', ' ', $address);

    // Remove extra spaces
    $address = preg_replace('/\s+/', ' ', $address);

    return $address;
}

function removeSpace(string $string): string
{
    return str_replace(' ', '', $string);
}

function extractFirstNumericNumber(string $input): ?string
{
    return trim(Str::before($input, ' '));
}

/* Replace the only first occurance of a substring in a string */
function removeStringFromString(string $needle, string $string, string $replaceString = ''): ?string
{
    return trim(Str::replaceFirst($needle, $replaceString, $string));
}

function getOnlyNumersFromString(string $string): string
{
    $cleanedString = preg_replace('/[^0-9.,\/-]/', ' ', $string);

    // Remove extra spaces at the end
    return $cleanedString = trim($cleanedString);
}


function replaceFirst(string $search, string $replace, string $subject): string
{
    return preg_replace('/' . preg_quote($search, '/') . '/', $replace, $subject, 1);
}


function fixNumberForAirCall(string $number): string
{
    return Str::start(substr(preg_replace('/\D/', '', $number), -10), '+44');
}


function generateUniqueRandomString(): string
{
    return str()->upper(Str::random(10));
}


function base64url_encode($data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data): bool|string
{
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '='));
}

function meg_encrypt($data): string
{
    // it get the string, replace each character with our specified ascii value from config array
    $str = json_encode($data);
    $ourAsciiArray = config("encrypt.ascii_char");
    $result = '';
    for ($i = 0; $i < strlen($str); $i++) {
        $stringChar = substr($str, $i, 1);
        $result .= chr(Arr::get($ourAsciiArray, ord($stringChar)));
        // $result .= chr(ord($stringChar) + 33);  // if the above not working we could replace it with a simple addition of a random ascii character

    }
    return base64url_encode($result);
}

function meg_decrypts($data)
{
    $str = base64url_decode($data);

    $ourAsciiArray = config("encrypt.ascii_char");

    $result = '';
    for ($i = 0; $i < strlen($str); $i++) {
        $stringChar = substr($str, $i, 1);
        $result .= chr(Arr::get($ourAsciiArray, ord($stringChar)));
        // $result .= chr(ord($stringChar) - 33);

    }

    return json_decode($result, true);
}
