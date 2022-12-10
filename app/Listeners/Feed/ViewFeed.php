<?php

namespace App\Listeners\Feed;

use App\Enums\Views\AViews;
use App\Events\Feed\VisitFeed;
use App\Events\Product\VisitProduct;
use App\Lib\Helpers\Views\ViewsHelper;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ViewFeed
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
    public function handle(VisitFeed $event)
    {
        ViewsHelper::addView($event->request, AViews::FEED, $event->userId, $event->itemId);
    }
}
