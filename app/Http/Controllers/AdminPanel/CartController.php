<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $carts = Cart::all();
            return view('admin.carts.index' , ['carts'=>$carts]);
        }catch (\Exception $e){
            DB::rollBack();
            Log::error('error in carts from admin panel ' . __LINE__ . $e);
            request()->session()->flash('error', 'خطأ يرجى التأكد من المشكلة');
            return redirect()->back();
        }
    }

    public function show($id)
    {
        try{
            $order = Cart::find($id);
            return view('admin.carts.show' , ['order'=>$order]);
        }catch (\Exception $e){
            DB::rollBack();
            Log::error('error in carts from admin panel ' . __LINE__ . $e);
            request()->session()->flash('error', 'خطأ يرجى التأكد من المشكلة');
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        request()->session()->flash('status', 'تم التنبية بنجاح');
        return redirect()->back();
    }

}
