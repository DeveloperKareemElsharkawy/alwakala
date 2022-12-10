<?php

namespace App\Providers;

use App\Models\ProductStore;
use App\Observers\ProductStoreObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'App\Events\Inventory\StockMovement' => [
            'App\Listeners\Inventory\StockChanges',
        ],
        'App\Events\PurchaseOrder\PlaceOrder' => [
            'App\Listeners\PurchaseOrder\PlaceOrderNotification',
        ],
        'App\Events\PurchaseOrder\RejectOrder' => [
            'App\Listeners\PurchaseOrder\RejectOrderNotification',
        ],
        'App\Events\PurchaseOrder\ApproveOrder' => [
            'App\Listeners\PurchaseOrder\ApproveOrderNotification',
        ],
        'App\Events\Order\PlaceOrder' => [
            'App\Listeners\Order\PlaceOrderNotification',
        ],
        'App\Events\Order\RejectOrder' => [
            'App\Listeners\Order\RejectOrderNotification',
        ],
        'App\Events\Order\ShippingOrder' => [
            'App\Listeners\Order\ShippingOrderNotification',
        ],
        'App\Events\Order\ApproveOrder' => [
            'App\Listeners\Order\ApproveOrderNotification',
        ],
        'App\Events\Product\VisitProduct' => [
            'App\Listeners\Product\ViewProduct',
        ],
        'App\Events\Feed\VisitFeed' => [
            'App\Listeners\Feed\ViewFeed',
        ],
        'App\Events\Product\FavoriteProduct' => [
            'App\Listeners\Product\FavoriteProduct',
        ],
        'App\Events\Product\ReviewProduct' => [
            'App\Listeners\Product\ReviewProduct',
        ],
        'App\Events\Store\VisitStore' => [
            'App\Listeners\Store\ViewStore',
        ],
        'App\Events\Store\FavoriteStore' => [
            'App\Listeners\Store\FavoriteStore',
        ],
        'App\Events\Store\FollowStore' => [
            'App\Listeners\Store\FollowStore',
        ],
        'App\Events\Order\ReceiveOrder' => [
            'App\Listeners\Order\ReceiveOrderNotification',
        ],
        'App\Events\Users\ApprovePendingSeller' => [
            'App\Listeners\Users\ApprovePendingSeller',
        ],
        'App\Events\Users\PendingForReview' => [
            'App\Listeners\Users\SendPendingForReviewNotification',
        ],
        'App\Events\ShippingCompany\VisitShippingCompany' => [
            'App\Listeners\ShippingCompany\ViewShippingCompany',
        ],
        'App\Events\Logs\DashboardLogs' => [
            'App\Listeners\Logs\DashboardLogs',
        ],

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

     }
}
