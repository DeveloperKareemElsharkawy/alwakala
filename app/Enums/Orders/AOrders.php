<?php

namespace App\Enums\Orders;

class AOrders
{
    const ISSUED = 1;
    const IN_PROGRESS = 2;
    const RECEIVED = 3;
    const CANCELED = 4;
    const REJECT = 5;
    const SHIPPING = 6;
    const WAITING_FOR_BARCODE_APPROVAL = 7;
    const RETURNED = 8;

}
