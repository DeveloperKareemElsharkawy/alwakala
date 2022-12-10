<?php

namespace App\Listeners\ShippingCompany;

use App\Events\ShippingCompany\VisitShippingCompany;
use App\Lib\Helpers\Views\ViewsHelper;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ViewShippingCompany
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
     * @param  VisitShippingCompany  $event
     * @return void
     */
    public function handle(VisitShippingCompany $event)
    {
        ViewsHelper::addView($event->request, $event->type);
    }
}
