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

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($store_id)
    {
        $store_type = request()->store_type ? request()->store_type : 2;
        $store = Store::where('id', $store_id)->first();
        $orders = Order::where('user_id', $store['user']['id'])->whereHas('items', function ($q) use ($store_type) {
            $q->whereHas('store');
        })->latest()->get();
        $order_types = OrderStatus::get();
        return view('admin.orders.index', compact('orders', 'store', 'order_types', 'store_type'));
    }

    public function show($id)
    {
        $order = Order::find($id);
        $store = Store::where('user_id', $order['user_id'])->first();
        return view('admin.orders.show', compact('order', 'store'));
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        $order = Order::find($id);
        if($order->status_id != 6 && $order->status_id != 3){
            DB::beginTransaction();
            try{
                $products = OrderProduct::where('order_id', $id)->get();
                foreach ($products as $product) {
                    $stock = ProductStore::where('product_id', $product['product_id'])->first();
                    $product_store_stock = ProductStoreStock::where([['product_store_id', $stock['id']], ['color_id', $product['color_id']], ['size_id', $product['size_id']]])->first();
                    $product_store_stock['stock'] = $product_store_stock['stock'] + $product['quantity'];
                    $product_store_stock->save();
                    $product_unit = ProductOrderUnitDetails::where('order_product_id', $product['id'])->delete();
                    $product->delete();
                }
                foreach ($order->status as $status){
                    $status->delete();
                }
                $order->delete();
                DB::commit();
                return back()->with('success', 'تم الحذف بنجاح');
            }catch (\Exception $e){
                DB::rollback();
                return back()->with('error', 'حدث خطأ');
            }
        }
        return back()->with('error', 'لا يمكن حذف هذا الطلب');
    }

    public function cancel($id)
    {
        $order = Order::find($id);
        if($order->status_id != 6 && $order->status_id != 3 && $order->status_id != 4){
            DB::beginTransaction();
            try{
                $products = OrderProduct::where('order_id', $id)->get();
                foreach ($products as $product) {
                    $stock = ProductStore::where('product_id', $product['product_id'])->first();
                    $product_store_stock = ProductStoreStock::where([['product_store_id', $stock['id']], ['color_id', $product['color_id']], ['size_id', $product['size_id']]])->first();
                    $product_store_stock['stock'] = $product_store_stock['stock'] + $product['quantity'];
                    $product_store_stock->save();
                }
                $order->status_id = 4;
                $order->save();
                DB::commit();
                return back()->with('success', 'تم الالغاء بنجاح');
            }catch (\Exception $e){
                DB::rollback();
                return back()->with('error', 'حدث خطأ');
            }
        }
        return back()->with('error', 'لا يمكن الغاء هذا الطلب');
    }

    public function delete_product_order($id)
    {
        $request = request()->all();
        $product = OrderProduct::find($id);
        $stock = ProductStore::where('product_id', $product['product_id'])->first();
        $product_id = $product['product_id'];
        $product_store_stock = ProductStoreStock::where([['product_store_id', $stock['id']], ['color_id', $product['color_id']], ['size_id', $product['size_id']]])->first();
        $product_store_stock['stock'] = $product_store_stock['stock'] + $product['quantity'];
        $product_store_stock->save();
        $product_unit = ProductOrderUnitDetails::where('order_product_id', $product['id'])->delete();
        $order = Order::find($product['order_id']);
        $product->delete();
        $order->total_price = OrderProduct::where('order_id', $order['id'])->sum('total_price');
        $order->save();
        if (count($order->items) == 0) {
            $order->status->delete();
            $order->delete();
        }

        return back()->with('success', 'تم الحذف بنجاح');
    }

    public function delete_store_order($id)
    {
        $request = request()->all();
        $products = OrderProduct::where('order_id', $request['order_id'])->where('store_id', $id)->get();
        foreach ($products as $product) {
            $stock = ProductStore::where('product_id', $product['product_id'])->where('store_id', $id)->first();
            $product_store_stock = ProductStoreStock::where([['product_store_id', $stock['id']], ['color_id', $product['color_id']], ['size_id', $product['size_id']]])->first();
            $product_store_stock['stock'] = $product_store_stock['stock'] + $product['quantity'];
            $product_store_stock->save();
            $product_unit = ProductOrderUnitDetails::where('order_product_id', $product['id'])->delete();
            $order = Order::find($product['order_id']);
            $product->delete();
            $order->total_price = OrderProduct::where('order_id', $order['id'])->sum('total_price');
            $order->save();
            if (count($order->items) == 0) {
                $order->status->delete();
                $order->delete();
            }
        }

        return back()->with('success', 'تم الحذف بنجاح');
    }
}
