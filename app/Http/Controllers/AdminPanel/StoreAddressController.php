<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Http\Requests\SellerApp\AddAddressRequest;
use App\Models\Address;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StoreAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($store_id)
    {
        try{
            $store = Store::find($store_id);
            $cities = City::all();
            $states = State::all();
            $addresses = Address::where('user_id' , $store['user_id'])->orderBy('id','desc')->get();
            return view('admin.addresses.index' , ['store' => $store , 'addresses' => $addresses,'cities'=>$cities,'states'=>$states]);
        }catch (\Exception $e){
            return redirect()->route('adminHome');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($store_id)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $sellerAddress = new Address;
            $sellerAddress->name = $request->name;
            $sellerAddress->type = $request->type;
            $sellerAddress->user_id = $request->user_id;
            $sellerAddress->mobile = $request->mobile;
            $sellerAddress->address = $request->address;
            $sellerAddress->city_id = $request->city_id;
            $sellerAddress->latitude = $request->latitude ?? 1.1;
            $sellerAddress->longitude = $request->longitude ?? 1.1;
            $sellerAddress->building_no = $request->building_no ?? 0;
            $sellerAddress->landmark = $request->landmark ?? '';
            $sellerAddress->main_street = $request->main_street ?? '';
            $sellerAddress->side_street = $request->side_street ?? '';
            $sellerAddress->is_default = $request->is_default ?? false;
            $sellerAddress->save();
        } catch (\Exception $e) {
            Log::error('error in addAddress of seller Address' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addAddress(AddAddressRequest $request)
    {
        try {
            $sellerAddress = new Address;
            $sellerAddress->name = $request->name;
            $sellerAddress->type = $request->type;
            $sellerAddress->user_id = $request->user_id;
            $sellerAddress->mobile = $request->mobile;
            $sellerAddress->address = $request->address;
            $sellerAddress->city_id = $request->city_id;
            $sellerAddress->latitude = $request->latitude ?? 1.1;
            $sellerAddress->longitude = $request->longitude ?? 1.1;
            $sellerAddress->building_no = $request->building_no ?? 0;
            $sellerAddress->landmark = $request->landmark ?? '';
            $sellerAddress->main_street = $request->main_street ?? '';
            $sellerAddress->side_street = $request->side_street ?? '';
            $sellerAddress->is_default = $request->is_default ?? false;
            $sellerAddress->save();
        } catch (\Exception $e) {
            Log::error('error in addAddress of seller Address' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
