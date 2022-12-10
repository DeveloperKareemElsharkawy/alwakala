<?php

namespace App\Listeners\PurchaseOrder;

use App\Events\PurchaseOrder\ApproveOrder;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ApproveOrderNotification
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
     * @param  ApproveOrder  $event
     * @return void
     */
    public function handle(ApproveOrder $event)
    {
        //
    }
}
