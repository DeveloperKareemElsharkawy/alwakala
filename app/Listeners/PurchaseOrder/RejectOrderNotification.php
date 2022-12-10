<?php

namespace App\Listeners\PurchaseOrder;

use App\Events\PurchaseOrder\RejectOrder;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RejectOrderNotification
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
     * @param  RejectOrder  $event
     * @return void
     */
    public function handle(RejectOrder $event)
    {
        //
    }
}
