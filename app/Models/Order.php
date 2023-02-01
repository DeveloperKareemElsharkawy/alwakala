<?php

namespace App\Models;

use App\Repositories\CommissionRepository;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'status_id',
        'total_price',
        'discount',
        'number',
        'user_id',
        'payment_method_id',
        'order_address_id',
        'coupon_id',
        'barcode',
        'share_coupon_code'
    ];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'coupon' => 'array',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_products')
            ->withPivot([
                // 'packing_unit_id',
                'order_products.id as order_product_id',
                'size_id',
                'color_id',
                'basic_unit_count',
                'purchased_item_count',
                'total_price',
                'item_price',
                'product_id',
                'total_price',
                'store_id',
            ]);
    }

    public function items()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function stores()
    {
        $stores = $this->hasMany(OrderProduct::class)
            ->select('store_id')
            ->distinct()
            ->with('store')
            ->get();
        foreach ($stores as $store) {
            $store->products = $this->items()->where('store_id', $store->store_id)->get();
            $store->total_price = $this->items()->where('store_id', $store->store_id)->sum('total_price');
        }
        return $stores;
    }

    public function last_status()
    {
        return $this->belongsTo(OrderStatus::class, 'status_id');
    }



    public function order_address()
    {
        return $this->belongsTo(OrderAddress::class);
    }

    public function coupon(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id', 'name');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function calculateTotalPrice()
    {
        $this->total_price = $this->items->sum('total_price');
        $this->save();
    }

    public function calculateTotalPriceOld($withProductsCalculations = false)
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
        $this->order_price = $totalPrice - $this->discount;
        $this->save();
        $store = Store::query()->where('id', $this->store_id)->first();
        $commission_row = CommissionRepository::getCommissionByStoreType($store->store_type_id);
        $commission = round(($this->order_price * $commission_row->commission) / 100);
        CommissionRepository::addStoreCommission($this->store_id, $commission);
    }

    public function status()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }
}
