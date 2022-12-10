<?php

namespace App\Listeners\Store;

use App\Enums\Views\AViews;
use App\Events\Store\VisitStore;
use App\Lib\Helpers\Views\ViewsHelper;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ViewStore
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
     * @param VisitStore $event
     * @return void
     */
    public function handle(VisitStore $event)
    {
        ViewsHelper::addView($event->request, AViews::STORE, $event->userId, $event->itemId);
    }
}
