<?php

use Illuminate\Support\Facades\Log;

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

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});

Broadcast::channel('chat.{receiver_id}.receive-message', function ($user, $receiverId) {

//    $sellersIds = \App\Lib\Helpers\StoreId\StoreId::getSellersIDsFromStores([3,4]); // if wanted To include Both Senders & Receivers
    $sellersIds = \App\Lib\Helpers\StoreId\StoreId::getStoreUsersIDs($receiverId);

     return in_array($user->id, $sellersIds);
});
