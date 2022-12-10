<?php


namespace App\Lib\Helpers\Orders;


class OrderHelper
{
    /**
     * @param $sellerId
     * @param $userId
     */
    public static function orderStackHolder($sellerId, $userId): string
    {
        if ($userId != $sellerId) {
            return 'owner';
        }
        return 'customer';
    }
}
