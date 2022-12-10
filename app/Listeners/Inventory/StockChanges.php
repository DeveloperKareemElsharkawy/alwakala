<?php

namespace App\Listeners\Inventory;

use App\Events\Inventory\StockMovement;
use Illuminate\Contracts\Queue\ShouldQueue;

class StockChanges implements ShouldQueue
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
     * @param StockMovement $event
     * @return void
     */
    public function handle(StockMovement $event)
    {
        $storeId = $event->storeId;
        $productId = $event->productId;
        $transactionTypeId = $event->transactionTypeId;
        $quantity = $event->quantity;


        $stockMovement = new \App\Models\StockMovement;
        $stockMovement->store_id = $storeId;
        $stockMovement->product_id = $productId;
        $stockMovement->transaction_type_id = $transactionTypeId;
        $stockMovement->quantity = $quantity;
        $stockMovement->save();


    }
}
