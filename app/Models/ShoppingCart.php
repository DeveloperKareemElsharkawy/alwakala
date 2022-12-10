<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model
{
    protected $fillable = [
        'total_price',
        'payment_method_id',
        'shipment_method_id',
        'address',
        'date',
        'cart_price',
        'total_price',

    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_shopping_cart')
            ->withPivot([
                'purchased_item_count',
                'packing_unit_id',
                'store_id',
                'color_id',
                'size_id',
                'basic_unit_count',
                'item_price',
                'total_price'
            ]);
    }

    public function calculateTotalPrice($withProductsCalculations = false)
    {
        $totalPrice = $this->total_price;
        if ($withProductsCalculations) {
            $products = $this->products;
            $totalPrice = 0;
            foreach ($products as $product) {
                $totalPrice += $product->pivot->purchased_item_count * $product->pivot->item_price;
            }
        }
        $this->total_price = $totalPrice;
        $this->cart_price = $totalPrice - $this->discount;
        $this->save();
    }

    public function address()
    {
        return $this->belongsTo(Address::class,'seller_address_id');
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class,'payment_method_id');
    }
}
