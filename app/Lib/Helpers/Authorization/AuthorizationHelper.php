<?php


namespace App\Lib\Helpers\Authorization;


class AuthorizationHelper
{
    /**
     * @param $checkedKey
     * @param $checkedValue
     * @param $ownerKey
     * @param $ownerValue
     * @param $model
     * @return bool
     */
    public static function isAuthorized($checkedKey, $checkedValue, $ownerKey, $ownerValue, $model): bool
    {
        if ($model::query()->where($checkedKey, $checkedValue)->where($ownerKey, $ownerValue)->first()) {
            return true;
        }
        return false;
    }

}
