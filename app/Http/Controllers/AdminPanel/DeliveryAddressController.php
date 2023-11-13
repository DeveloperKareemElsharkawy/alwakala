<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminPanel\AddDeliveryAddressRequest;
use App\Http\Requests\SellerApp\AddAddressRequest;
use App\Models\Address;
use App\Models\City;
use App\Models\CityStore;
use App\Models\OrderAddress;
use App\Models\State;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeliveryAddressController extends Controller
{
    public function index($store_id)
    {
        try {
            $store = Store::find($store_id);
            $cities = City::all();
            $states = State::all();
            $addresses = Address::where('user_id', $store['user_id'])->where('archive', false)->orderBy('id', 'desc')->get();
            return view('admin.delivery_addresses.index', ['store' => $store, 'addresses' => $addresses, 'cities' => $cities, 'states' => $states]);
        } catch (\Exception $e) {
            return redirect()->route('adminHome');
        }
    }

    public function form(AddDeliveryAddressRequest $request, $address_id = null)
    {
        try {

            if (empty($address_id)) {
                $sellerAddress = new Address();
                $store = Store::find($request->store_id);
                $user_id = $store['user_id'];
            } else {
                $sellerAddress = Address::find($address_id);
                $user_id = $request['user_id'];
            }
            \DB::table('addresses')->where('user_id', $user_id)->update(['is_default' => false]);
            $sellerAddress->city_id = $request->city_id;
            $sellerAddress->user_id = $user_id;
            $sellerAddress->mobile = $request->mobile;
            $sellerAddress->name = $request->name;
            $sellerAddress->address = $request->address;
            $sellerAddress->longitude = $request->longitude ?: '111111';
            $sellerAddress->latitude = $request->latitude ?: '111111';
            $sellerAddress->main_street = $request->main_street;
            $sellerAddress->type = 'home';
            $sellerAddress->is_default = $request->is_default;
            $sellerAddress->save();

            if (empty($address_id)) {
                $request->session()->flash('status', 'تم الاضافة بنجاح');
            } else {
                $request->session()->flash('status', 'تم التعديل بنجاح');
            }
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('error in addAddress of seller Address' . __LINE__ . $e);
            $request->session()->flash('error', 'خطأ يرجى التأكد من المشكلة');
            return redirect(back());
        }
    }

    public function delivery_address_info($id)
    {
        $address = \App\Models\Address::find($id);
        $states = State::all();
        $old_state = State::find($address['city']['state_id']);
        $lang = app()->getLocale();
        return view('admin.delivery_addresses.edit_popup', ['old_state' => $old_state, 'lang' => $lang, 'address' => $address, 'states' => $states]);
    }

    public function primary_address($id)
    {
        $address = \App\Models\Address::find($id);
        $address->is_default = true;
        request()->session()->flash('status', 'تم التعديل بنجاح');
        return redirect()->back();
    }

    public function delete_address($id)
    {
        $address = \App\Models\Address::find($id);
        $address->archive = true;
        $address->save();
        return response()->json([
            'type' => 'success',
            'title' => ' حذف العنوان نهائيا',
            'msg' => 'تم حذف العنوان بنجاح',
        ]);
    }
}
