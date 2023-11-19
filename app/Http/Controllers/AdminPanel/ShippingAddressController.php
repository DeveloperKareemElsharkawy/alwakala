<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Http\Requests\SellerApp\AddAddressRequest;
use App\Http\Requests\ShippingAddresses\AddShipmentAddressRequest;
use App\Models\Address;
use App\Models\City;
use App\Models\CityStore;
use App\Models\Country;
use App\Models\OrderAddress;
use App\Models\Region;
use App\Models\State;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShippingAddressController extends Controller
{
    public function index($store_id)
    {
//        try{
            $store = Store::find($store_id);
            $cities = City::all();
            $states = State::all();
            $countries = Country::all();
            $addresses = CityStore::where('store_id' , $store_id)->orderBy('id','desc')->get();
            return view('admin.shipping_addresses.index' , ['store' => $store , 'addresses' => $addresses,
                'cities'=>$cities,'states'=>$states , 'countries' => $countries]);
//        }catch (\Exception $e){
//            return redirect()->route('adminHome');
//        }
    }

    public function form(AddShipmentAddressRequest $request , $address_id = null)
    {
        try {
            if(empty($address_id)){
                $sellerAddress = new CityStore();
                $found = CityStore::where('store_id' , $request['store_id'])->where('city_id' , $request['city_id'])->first();
                if($found){
                    $request->session()->flash('error', 'عنوان شحن موجود مسبقا');
                    return redirect()->back();
                }
            }else{
                $sellerAddress = CityStore::find($address_id);
            }
            $sellerAddress->city_id = $request->city_id;
            $sellerAddress->store_id = $request->store_id;
            $sellerAddress->fees = $request->fees;
            $sellerAddress->save();

            if(empty($address_id)){
                $request->session()->flash('status', 'تم الاضافة بنجاح');
            }else{
                $request->session()->flash('status', 'تم التعديل بنجاح');
            }
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('error in addAddress of seller Address' . __LINE__ . $e);
            $request->session()->flash('error', 'خطأ يرجى التأكد من المشكلة');
            return redirect(back());
        }
    }


    public function shipping_address_info($id)
    {
        $address = \App\Models\CityStore::find($id);
        $states = State::all();
        $old_state = State::find($address['city']['state_id']);
        $lang = app()->getLocale();
        $countries = Country::all();
        $regions = Region::all();
        return view('admin.shipping_addresses.edit_popup', ['regions'=>$regions,'countries'=>$countries,'old_state'=>$old_state,'lang'=>$lang,'address' => $address,'states' => $states]);
    }

    public function delete_address($id)
    {
        $address = \App\Models\CityStore::find($id);
        $address->delete();
        return response()->json([
            'type' => 'success',
            'title' => ' حذف العنوان نهائيا',
            'msg' => 'تم حذف العنوان بنجاح',
        ]);
    }
}
