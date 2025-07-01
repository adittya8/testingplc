<?php

use App\Exceptions\CustomAbort;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

function getSlug(string $str, string $separator = '-', $language = 'en', $dictionary = ['@' => 'at']): string
{
    return Str::slug($str, $separator, $language, $dictionary);
}

function getUniqueSlug(string $str, Model $model, int $increment = 0): string
{
    $slug = Str::slug($str);
    $finalSlug = $slug;

    if ($increment != 0) {
        $finalSlug .= "-$increment";
    }

    $dbRecords = $model->where('slug', $slug)->first();

    if ($dbRecords) {
        $finalSlug = getUniqueSlug($str, $model, ++$increment);
    }

    return $finalSlug;
}

/**
 * Returns the storage path for specific type of files
 */
function getStoragePath(?Model $modelObject = null): string
{
    return match (true) {
        $modelObject instanceof Project => 'projects/'
    };
}

/**
 * Returns the path of a file to use in Laravel's asset() method
 */
function getFileAssetPath(?Model $model = null, string $propertyName = 'file', bool $isPageImage = false): string
{
    $path = $isPageImage
        ? 'storage/' . getStoragePath($model) . $model->data[$propertyName]
        : 'storage/' . getStoragePath($model) . $model->{$propertyName};

    return $model ? $path : '';
}

function strLimit(string|null $str = '', int $limit = 25, string $end = '...'): string
{
    return $str ? Str::limit($str, $limit, $end) : '';
}

function getUsersByPermissions(array $permissionNames): Collection
{
    return User::whereHas('roles', function ($q) use ($permissionNames) {
        $q->where('name', 'Developer')
            ->orWhere(function ($q) use ($permissionNames) {
                $q->whereHas('permissions', function ($q) use ($permissionNames) {
                    $q->whereIn('name', $permissionNames);
                });
            });
    })->get();
}

function getUsersByRole(string|array $roleNames): Collection
{
    $roleNames = is_array($roleNames) ? $roleNames : [$roleNames];
    $roleNames = array_map(fn(string $item): string => strtolower($item), $roleNames);

    return User::whereHas('roles', function ($q) use ($roleNames) {
        $q->whereIn(DB::raw('LOWER(name)'), $roleNames);
    })->get();
}

/**
 * Returs an array dates between $startDate & $endDate
 */
function getDateList(string $startDate, string $endDate, string $format = "Y-m-d"): array
{
    $begin = new DateTime($startDate);
    $end = new DateTime($endDate);

    $interval = new DateInterval('P1D');
    $dateRange = new DatePeriod($begin, $interval, $end);

    $range = [];
    foreach ($dateRange as $date) {
        $range[] = $date->format($format);
    }
    array_push($range, $end->format($format));

    return $range;
}

function abortIfNotPermitted(string $permission)
{
    if (!Auth::user()->can($permission)) {
        abort(403);
    }
}

// function getPrivateFilePath(?Model $model = null, ?string $field = null, ?string $filePath = null)
// {
//     if ($model && $field) {
//         $params = ['path' => rtrim(getStoragePath($model), '/'), 'name' => $model->{$field}];
//     } elseif ($filePath) {
//         $params = ['path' => $filePath];
//     } else {
//         return '';
//     }

//     return route('admin.get-file', $params);
// }

function isSbLinkActive(array $routeNames)
{
    return in_array(request()->route()->getName(), $routeNames);
}

function getWeekdayName(int $val, bool $short = false): string
{
    if ($short) {
        return match (true) {
            $val == 0 => 'Sun',
            $val == 1 => 'Mon',
            $val == 2 => 'Tue',
            $val == 3 => 'Wed',
            $val == 4 => 'Thu',
            $val == 5 => 'Fri',
            $val == 6 => 'Sat',
            $val == 7 => 'Sun',
            default => '',
        };
    }

    return match (true) {
        $val == 0 => 'Sunday',
        $val == 1 => 'Monday',
        $val == 2 => 'Tuesday',
        $val == 3 => 'Wednesday',
        $val == 4 => 'Thursday',
        $val == 5 => 'Friday',
        $val == 6 => 'Saturday',
        default => '',
    };
}

function getWeekdayNumber(string $weekdayName): string
{
    $weekdayName = strtolower($weekdayName);

    return match (true) {
        $weekdayName == 'sunday' => 0,
        $weekdayName == 'monday' => 1,
        $weekdayName == 'tuesday' => 2,
        $weekdayName == 'wednesday' => 3,
        $weekdayName == 'thursday' => 4,
        $weekdayName == 'friday' => 5,
        $weekdayName == 'saturday' => 6,
    };
}

function signed2hex($value)
{
    $packed = pack('s', $value);
    $hex = '';
    for ($i = 0; $i < 2; $i++) {
        $hex .= str_pad(dechex(ord($packed[$i])), 2, '0', STR_PAD_LEFT);
    }
    $tmp = str_split($hex, 2);

    return implode('', array_reverse($tmp));
}

function dechex2digits($value)
{
    return str_pad(dechex($value), 2, '0', STR_PAD_LEFT);
}

function getCommandXor(string $cmd): string
{
    $hexArray = str_split($cmd, 2);
    $result = hexdec($hexArray[0]);

    for ($i = 1; $i < count($hexArray); $i++) {
        $result ^= hexdec($hexArray[$i]);
    }

    return strtoupper(str_pad(dechex($result), 2, '0', STR_PAD_LEFT));
}

function getRtuCommand(string $rtuCode, int $dimValue): string
{
    $cmdHex = "8A0006{$rtuCode}FF15001E";
    $dimHex = str_pad(dechex($dimValue), 2, "0", STR_PAD_LEFT);
    $cmdHex .= $dimHex;
    $cmdHex .= '320032';
    $cmdHex .= getCommandXor($cmdHex);

    return strtoupper($cmdHex);
}

function formatDeviceIdForCommand(string $dcuId): string
{
    return implode("", array_reverse(str_split($dcuId, 2)));
}

function hasPermissionTo(string|array $permission, bool $withLayout = false)
{
    $permission = is_array($permission) ? $permission : [$permission];
    if (!Auth::user()->canAny($permission)) {
        abort(403, 'You are not permitted to perform this action!');
    }
}

function validateBrightness(mixed $brightness, mixed $power): bool
{
    if (
        ($brightness && ($brightness < 0 || ($brightness > 0 && $brightness < 12) || !is_numeric($brightness)))
        || ($power && (!is_numeric($power) || $power < 0))
    ) {
        return false;
    }

    return true;
}

function getMappedModelName(string $name): string
{
    return match ($name) {
        'Concentrator' => 'DCU',
        'RemoteTerminal' => 'RTU',
        default => $name,
    };
}

function isDcuOnline(?string $trme)
{
    return $trme && $trme >= date('Y-m-d H:i:s', strtotime('-80 seconds'));
}

function isRtuOnline(?string $trme)
{
    return $trme && $trme >= date('Y-m-d H:i:s', strtotime('-200 seconds'));
}

function isValidTimeStamp(mixed $timestamp)
{
    return is_numeric($timestamp) && (int) $timestamp == $timestamp && $timestamp >= 0 && $timestamp <= PHP_INT_MAX;
}
