<?php
namespace App\Enums\DiscountTypes;
class DiscountTypes
{
   const AMOUNT = 1;
   const PERCENTAGE = 2;
   const GOODS = 3;


    public static function getDiscountType(int $value): string
    {
        switch ($value) {
            case self::AMOUNT:
                return 'Amount';
                break;
            case self::PERCENTAGE:
                return 'Percentage';
                break;
            case self::GOODS:
                return 'Goods';
                break;
            default:
                return self::getKey($value);
        }
    }

}
