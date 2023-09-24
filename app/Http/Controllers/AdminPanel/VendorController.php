<?php

namespace App\Http\Controllers\AdminPanel;

use App\Enums\UserTypes\UserType;
use App\Http\Controllers\Controller;
use App\Lib\Log\ValidationError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\Category;
use App\Models\CategoryStore;
use App\Models\City;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $active = request()->active ? request()->active : true;
            $verified = request()->verified ? request()->verified : true;
            $city_id = request()->city_id ?: '';
            $date = request()->date ?: '';
            $type = request()->type == 'retail' ? 1 : 2;
            $cities = City::where('activation', true)->get();
            $categories = Category::where('activation', true)->get();
            $stores = Store::whereNull('parent_id')->orderBy('id', 'desc');
            if ($active && $active != 1) {
                $stores = $stores->where('activation', $active);
            } else {
                $stores = $stores->where('activation', true);
            }
            if ($verified && $verified != 1) {
                $stores = $stores->where('is_verified', $verified);
            } else {
                $stores = $stores->where('is_verified', true);
            }
            if ($type) {
                $stores = $stores->where('store_type_id', $type);
            } else {
                $stores = $stores->where('store_type_id', 1);
            }
            if ($city_id) {
                $stores = $stores->where('city_id', $city_id);
            }
            if ($date) {
                $stores = $stores->whereDate('created_at', $date);
            }
            $stores = $stores->get();

            $all_retail = Store::whereNull('parent_id')->where([['store_type_id', 1]])->count();
            $active_retail = Store::whereNull('parent_id')->where([['store_type_id', 1], ['activation', true], ['is_verified', true]])->count();
            $inactive_retail = Store::whereNull('parent_id')->where([['store_type_id', 1], ['activation', false]])->count();
            $pending_retail = Store::whereNull('parent_id')->where([['store_type_id', 1], ['activation', true], ['is_verified', false]])->count();
            $all_supplier = Store::whereNull('parent_id')->where([['store_type_id', 2]])->count();
            $active_supplier = Store::whereNull('parent_id')->where([['store_type_id', 2], ['activation', true], ['is_verified', true]])->count();
            $inactive_supplier = Store::whereNull('parent_id')->where([['store_type_id', 2], ['activation', false]])->count();
            $pending_supplier = Store::whereNull('parent_id')->where([['store_type_id', 2], ['activation', true], ['is_verified', false]])->count();
            return view('admin.vendors.index', compact('active', 'verified', 'type', 'stores',
                'active_retail', 'inactive_retail', 'pending_retail', 'active_supplier', 'inactive_supplier', 'pending_supplier',
                'all_supplier', 'all_retail', 'cities', 'city_id', 'date', 'categories'));
        } catch (\Exception $e) {
            return redirect()->route('adminHome');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            DB::beginTransaction();
            $data = $request->all();

            $user = new User();
            $data['type_id'] = UserType::SELLER;

            $user->name = $data['user_name'];
            $user->password = bcrypt('123456789');
            $user->email = $data['user_email'];
            $user->mobile = $data['user_phone'];
            $user->type_id = $data['type_id'];

            if ($data['type_id'] == UserType::SELLER && array_key_exists('image', $data)) {
                $user->image = UploadImage::uploadImageToStorage($data['image'], 'sellers');
                $user->activation = true;
            }
            $user->save();

            $store = new Store();
            $store->name = $data['store_name'];
            $store->latitude = '31.0062392';
            $store->longitude = '31.3840003';
            $store->name = $data['store_name'];
            $store->store_type_id = $data['store_type_id'];
            $store->mobile = $data['store_phone'];
            $store->address = $data['store_address'];
            $store->city_id = $data['city_id'];
            $store->is_verified = true;
            $store->user_id = $user['id'];
            $store->is_main_branch = true;
            if ($request->hasFile('logo')) {
                Storage::disk('s3')->delete($store->logo);
                $store->logo = UploadImage::uploadImageToStorage($request->logo, 'stores');
            }
            if ($request->hasFile('cover')) {
                Storage::disk('s3')->delete($store->licence);
                $store->licence = UploadImage::uploadImageToStorage($request->cover, 'stores');
                $store->cover = UploadImage::uploadImageToStorage($request->cover, 'stores');
            }
            $store->save();
            $category = new CategoryStore();
            $category->store_id = $store->id;
            $category->category_id = $data['category_id'];
            $category->save();

            DB::commit();
            $request->session()->flash('status', 'تم الاضافة بنجاح');
            return redirect(route('vendors.index'));
        }catch (\Exception $e){
            DB::rollBack();
            Log::error('error in add vendor from admin panel ' . __LINE__ . $e);
            $request->session()->flash('error', 'خطأ يرجى التأكد من المشكلة');

            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $store = Store::find($id);
            $cities = City::where('activation' , true)->get();
            $categories = Category::where('activation' , true)->get();
            $type = request()->type;
            if($type == 'branches'){
                return view('admin.vendors.branches',compact('store','categories' , 'cities'));
            }else{
                return view('admin.vendors.show',compact('store','categories' , 'cities'));
            }
        }catch (\Exception $e){
            Log::error('error in show vendor from admin panel ' . __LINE__ . $e);
            request()->session()->flash('error', 'خطأ يرجى التأكد من المشكلة');
            return redirect()->back();
        }
    }

    public function branch($store_id = null ,Request $request)
    {
        try{
            DB::beginTransaction();
            $data = $request->all();

            $main_store = Store::where('id' , $request->store_id);
            if($store_id == null){
                $store = new Store();
            }else{
                $store = Store::find($store_id);
            }
            $store->name = $data['store_name'];
            $store->latitude = '31.0062392';
            $store->longitude = '31.3840003';
            $store->name = $data['store_name'];
            $store->store_type_id = $data['store_type_id'];
            $store->mobile = $data['store_phone'];
            $store->address = $data['store_address'];
            $store->city_id = $data['city_id'];
            $store->is_main_branch = false;
            $store->parent_id = $main_store->id;
            $store->is_verified = true;
            $store->user_id = $main_store->user->id;
            if ($request->hasFile('logo')) {
                Storage::disk('s3')->delete($store->logo);
                $store->logo = UploadImage::uploadImageToStorage($request->logo, 'stores');
            }
            if ($request->hasFile('cover')) {
                Storage::disk('s3')->delete($store->licence);
                $store->licence = UploadImage::uploadImageToStorage($request->cover, 'stores');
                $store->cover = UploadImage::uploadImageToStorage($request->cover, 'stores');
            }
            $store->save();
            $category = new CategoryStore();
            $category->store_id = $store->id;
            $category->category_id = $data['category_id'];
            $category->save();

            DB::commit();
            $request->session()->flash('status', 'تم الاضافة بنجاح');
            return redirect()->back();
        }catch (\Exception $e){
            DB::rollBack();
            Log::error('error in add vendor from admin panel ' . __LINE__ . $e);
            $request->session()->flash('error', 'خطأ يرجى التأكد من المشكلة');

            return redirect()->back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            DB::beginTransaction();
            $data = $request->all();

            $store = Store::find($id);

            $user = $store->user;
            $data['type_id'] = UserType::SELLER;

            $user->name = $data['user_name'];
            $user->password = bcrypt('123456789');
            $user->email = $data['user_email'];
            $user->mobile = $data['user_phone'];
            $user->type_id = $data['type_id'];

            if ($data['type_id'] == UserType::SELLER && array_key_exists('image', $data)) {
                $user->image = UploadImage::uploadImageToStorage($data['image'], 'sellers');
                $user->activation = true;
            }
            $user->save();
            $store->name = $data['store_name'];
            $store->latitude = '31.0062392';
            $store->longitude = '31.3840003';
            $store->name = $data['store_name'];
            $store->mobile = $data['store_phone'];
            $store->address = $data['store_address'];
            $store->city_id = $data['city_id'];
            $store->is_verified = true;
            $store->user_id = $user['id'];
            $store->is_main_branch = true;
            if ($request->hasFile('logo')) {
                Storage::disk('s3')->delete($store->logo);
                $store->logo = UploadImage::uploadImageToStorage($request->logo, 'stores');
            }
            if ($request->hasFile('cover')) {
                Storage::disk('s3')->delete($store->licence);
                $store->licence = UploadImage::uploadImageToStorage($request->cover, 'stores');
                $store->cover = UploadImage::uploadImageToStorage($request->cover, 'stores');
            }
            $store->save();
            $category = CategoryStore::where('store_id' , $store->id)->first();
            $category->store_id = $store->id;
            $category->category_id = $data['category_id'];
            $category->save();

            DB::commit();
            $request->session()->flash('status', 'تم التعديل بنجاح');
            return redirect()->back();
        }catch (\Exception $e){
            DB::rollBack();
            Log::error('error in add vendor from admin panel ' . __LINE__ . $e);
            $request->session()->flash('error', 'خطأ يرجى التأكد من المشكلة');

            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
