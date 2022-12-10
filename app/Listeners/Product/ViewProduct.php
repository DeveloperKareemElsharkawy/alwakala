<?php

namespace App\Listeners\Product;

use App\Enums\Views\AViews;
use App\Events\Product\VisitProduct;
use App\Lib\Helpers\Views\ViewsHelper;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ViewProduct
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
     * @param VisitProduct $event
     * @return void
     */
    public function handle(VisitProduct $event)
    {
        ViewsHelper::addView($event->request, AViews::PRODUCT, $event->userId, $event->itemId);
    }
}
