<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Http\Resources\Consumer\Order\OrderResource;
use App\Models\Order;
use DB;
use App\Models\OrderProduct;
use App\Models\OrderStatus;
use App\Models\ProductOrderUnitDetails;
use App\Models\ProductStore;
use App\Models\ProductStoreStock;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($store_id)
    {
        $store_type = request()->store_type ? request()->store_type : 2;
        $store = Store::whereNull('parent_id')->where('id', $store_id)->first();
        $order_ids = OrderProduct::where('store_id',$store_id)->pluck('order_id');
        $orders = Order::whereHas('items', function ($q) use ($store_type , $store) {
            $q->whereHas('store', function ($qq) use ($store_type , $store) {

            });
        })->whereIn('id',$order_ids)->latest()->get();
        $order_types = OrderStatus::where('id', '!=', 7)->get();
        return view('admin.purchases.index', compact('orders', 'store', 'order_types', 'store_type'));
    }

    public function show($id,$store_id)
    {
        $order = Order::find($id);
        $store = Store::where('id', $store_id)->first();
        $status = OrderStatus::where('id' , '!=' , 4)->get();
        return view('admin.purchases.show', compact('order', 'store','status'));
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $order_id , $store_id)
    {
        \DB::table('order_products')->where('order_id', $order_id)->where('store_id' , $store_id)->update(['status_id' => $request['status_id']]);
        $request->session()->flash('status', 'تم التعديل بنجاح');
        return redirect()->back();
    }
}
