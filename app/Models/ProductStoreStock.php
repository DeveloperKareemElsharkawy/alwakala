<?php

namespace App\Models;

use App\Enums\Orders\AOrders;
use Illuminate\Database\Eloquent\Model;

class ProductStoreStock extends Model
{
    protected $table = "product_store_stock";
    protected $fillable = [
        'product_store_id',
        'stock',
        'reserved_stock',
        'available_stock',
        'sold',
        'size_id',
        'color_id',
        'returned'
    ];

    public function adoptStock($productId, $store_id, $packingUnitProductId = null)
    {
        $result = OrderProduct::query()
            ->where('order_products.status_id', AOrders::ISSUED)
            ->where('order_products.product_id', $productId)
            ->where('order_products.store_id', $store_id)
            ->selectRaw('( CASE
          WHEN order_products.size_id IS NOT NULL
          THEN order_products.purchased_item_count
          ELSE
          (select COALESCE(SUM(order_products.purchased_item_count * (select quantity from packing_unit_product_attributes where size_id = ? AND packing_unit_product_id = ?)),0))
          END )  as total_reserved', [$this->size_id, $packingUnitProductId])
            ->groupBy(['order_products.size_id', 'order_products.purchased_item_count'])
            ->get();

        $total_reserved = count($result) > 0 ? $result[0]['total_reserved'] : 0;
        $this->available_stock = $this->stock - $total_reserved;
        $this->reserved_stock = $total_reserved;

        $this->save();
    }

    public function color()
    {
        return $this->belongsTo(Color::class)->select('id', 'name_en', 'name_ar', 'hex');
    }

    public function size()
    {
        return $this->belongsTo(Size::class)->select('id', 'size');
    }

    public function product_store()
    {
        return $this->belongsTo(ProductStore::class,'product_store_id','id');
    }
}
