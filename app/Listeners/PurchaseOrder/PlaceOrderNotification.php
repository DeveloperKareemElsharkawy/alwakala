<?php

namespace App\Listeners\PurchaseOrder;

use App\Events\PurchaseOrder\PlaceOrder;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PlaceOrderNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PlaceOrder  $event
     * @return void
     */
    public function handle(PlaceOrder $event)
    {
        //
    }
}
