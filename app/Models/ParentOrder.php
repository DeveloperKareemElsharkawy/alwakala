<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_price'
    ];
    protected $table = 'parent_orders';

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function calculateParentOrderPrice()
    {
        $orderPrice = 0;
        foreach ($this->orders as $order) {
            $orderPrice += $order->order_price;
        }
        $this->order_price = $orderPrice;
        $this->save();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
