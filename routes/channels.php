<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Officer;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('import-status.{id}', function (Officer $officer, $id) {
    \Log::info("faithjdjdjd........");  // Log officer info for debugging
    return true;
});
