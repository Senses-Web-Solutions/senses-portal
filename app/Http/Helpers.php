<?php

use App\Models\User;
use Illuminate\Support\Str;
use App\Actions\UserSettings\GetUserImpersonationSetting;

function textColour($colour)
{
    //    $colour = preg_replace('/^#/', '', $colour);
    //    return (hexdec($colour) > 0xffffff / 1.2) ? '#000000' : '#ffffff';

    $part = Str::afterLast($colour, '-');
    $shade = match ($part) {
        'light' => 'darker',
        'dark' => 'light',
        'lighter' => 'darker',
        'darker' => 'lighter',
        default => 'darker',
    };

    return Str::beforeLast($colour, '-') . '-' . $shade;
}

function getCurrentUser()
{
    if (request()->job_user instanceof User) {
        return request()->job_user;
    }

    $user = auth()->user();

    if ($user) {
        $userSetting = app(GetUserImpersonationSetting::class)->execute($user->id, 'impersonation');
        $userID = $userSetting?->data['user_id'] ?? null;
        if ($userID !== null && $userID != $user->id) {
            $user = User::find($userID);
        }
    }

    return $user;
}

function getActualCurrentUser()
{
    return auth()->user();
}

function getSensesSystemUser()
{
    return User::where('email', 'senses@senses.co.uk')->first();
}

function getCurrentUserOrSystemUser()
{
    return  getCurrentUser() ?? getSensesSystemUser();
}

function broadcast_safely($event, $rescue = null, bool $report = false) {
    //PendingBroadcasts are returned by broadcast() these trigger the broadcast on destruction
    //This makes it annoying to capture the exception
    //So we will detect if its broadcasting 'now', logic for detection is from
    //https://github.com/laravel/framework/blob/b6f47323da0f0052680d337fe49ffdfe12692ad6/src/Illuminate/Broadcasting/BroadcastManager.php#L154
    //In these instances we will fire the destructor immediatly via unassigning so we can capture it and you will not get a return value from the broadcast method

    if ($event instanceof Illuminate\Contracts\Broadcasting\ShouldBroadcastNow || (is_object($event) && method_exists($event, 'shouldBroadcastNow') && $event->shouldBroadcastNow())) {
        rescue(function() use($event) {
            $result = broadcast($event);
            $result = null;
        }, rescue:$rescue, report:$report);
        return null;
    }

    return broadcast($event);
}
