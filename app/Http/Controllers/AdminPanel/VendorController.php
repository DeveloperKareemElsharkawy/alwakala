<?php

namespace App\Http\Controllers\AdminPanel;

use App\Enums\UserTypes\UserType;
use App\Http\Controllers\Controller;
use App\Lib\Log\ValidationError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\BrandStore;
use App\Models\Category;
use App\Models\CategoryStore;
use App\Models\City;
use App\Models\Store;
use App\Models\StoreDocument;
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
                $store->logo = UploadImage::uploadImageToStorage($request->logo, 'stores');
            }
            if ($request->hasFile('cover')) {
                $store->cover = UploadImage::uploadImageToStorage($request->cover, 'stores');
            }
            if ($request->hasFile('licence')) {
                $store->licence = UploadImage::uploadImageToStorage($request->licence, 'stores');
            }
            $store->save();
            if($request->hasFile('identity')){
                $identity = new StoreDocument();
                $identity->store_id = $store['id'];
                $identity->title_ar = 'الهوية الوطينة';
                $identity->title_en = 'National Identity';
                $identity->type = 'identity';
                $identity->status = 1;
                $identity->image = UploadImage::uploadImageToStorage($request->identity, 'stores' , 'identity');
                $identity->save();
            }
            if($request->hasFile('text_card')){
                $text_card = new StoreDocument();
                $text_card->store_id = $store['id'];
                $text_card->title_ar = 'بطاقة نصية';
                $text_card->title_en = 'Text card';
                $text_card->type = 'text_card';
                $text_card->status = 1;
                $text_card->image = UploadImage::uploadImageToStorage($request->text_card, 'stores','text_card');
                $text_card->save();
            }

            $category = new CategoryStore();
            $category->store_id = $store->id;
            $category->category_id = $data['category_id'];
            $category->save();

            $brand_ids = $request->brand_ids;
            if(count($brand_ids) > 0){
                foreach ($brand_ids as $category_id){
                    $brand_category = new BrandStore();
                    $brand_category->store_id = $store->id;
                    $brand_category->brand_id = $category_id;
                    $brand_category->save();
                }
            }
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
                $user = new User();
                $data['type_id'] = UserType::SELLER;
                $data['activation'] = true;
                $data['mobile'] = $request['user_phone'];
                $data['email'] = $request['user_email'];
                $data['password'] = $request['password'];
                $user->initializeUserFields($data);
                $user->save();

                $store = new Store();
                $store_typo = 'new';
            }else{
                $store = Store::find($store_id);
                $store_typo = 'old';

                $user = $store['user'];
                $data['type_id'] = UserType::SELLER;
                $data['activation'] = true;
                $data['mobile'] = $request['user_phone'];
                $data['email'] = $request['user_email'];
                if(isset($request['password'])){
                    $data['password'] = $request['password'];
                }
                $user->initializeUserFields($data);
                $user->save();
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
            $store->user_id = $user->id;
            if ($request->hasFile('logo')) {
                Storage::disk('s3')->delete($store->logo);
                $store->logo = UploadImage::uploadImageToStorage($request->logo, 'stores');
            }
            if ($request->hasFile('cover')) {
                Storage::disk('s3')->delete($store->cover);
                $store->cover = UploadImage::uploadImageToStorage($request->cover, 'stores');
            }
            if ($request->hasFile('licence')) {
                Storage::disk('s3')->delete($store->licence);
                $store->licence = UploadImage::uploadImageToStorage($request->licence, 'stores');
            }
            if($request->hasFile('identity')){
                if($store_typo == 'old'){
                    Storage::disk('s3')->delete($store->identity->image);
                    if($store->identity){
                        $store->identity->delete();
                    }
                }
                $identity = new StoreDocument();
                $identity->store_id = $store['id'];
                $identity->title_ar = 'الهوية الوطينة';
                $identity->title_en = 'National Identity';
                $identity->type = 'identity';
                $identity->status = 1;
                $identity->image = UploadImage::uploadImageToStorage($request->identity, 'stores' , 'identity');
                $identity->save();
            }
            if($request->hasFile('text_card')){
                if($store_typo == 'old'){
                    Storage::disk('s3')->delete($store->text_card->image);
                    if($store->text_card){
                        $store->text_card->delete();
                    }
                }
                $text_card = new StoreDocument();
                $text_card->store_id = $store['id'];
                $text_card->title_ar = 'بطاقة نصية';
                $text_card->title_en = 'Text card';
                $text_card->type = 'text_card';
                $text_card->status = 1;
                $text_card->image = UploadImage::uploadImageToStorage($request->text_card, 'stores','text_card');
                $text_card->save();
            }
            $store->save();
            $category = new CategoryStore();
            $category->store_id = $store->id;
            $category->category_id = $data['category_id'];
            $category->save();

            if(count($main_store['brands']) > 0){
                foreach ($main_store['brands'] as $category_id){
                    $brand_category = new BrandStore();
                    $brand_category->store_id = $store->id;
                    $brand_category->brand_id = $category_id['id'];
                    $brand_category->save();
                }
            }

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
                if(isset($user->image)){
                    $user->image = UploadImage::uploadImageToStorage($data['image'], 'sellers');
                }
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
                Storage::disk('s3')->delete($store->cover);
                $store->cover = UploadImage::uploadImageToStorage($request->cover, 'stores');
            }
            if ($request->hasFile('licence')) {
                $store->licence = UploadImage::uploadImageToStorage($request->licence, 'stores');
            }
            $store->save();
            if($request->hasFile('identity')){
                if($store->identity){
                    Storage::disk('s3')->delete($store->identity->image);
                }

                if($store->identity){
                    $store->identity->delete();
                }
                $identity = new StoreDocument();
                $identity->store_id = $store['id'];
                $identity->title_ar = 'الهوية الوطينة';
                $identity->title_en = 'National Identity';
                $identity->type = 'identity';
                $identity->status = 1;
                $identity->image = UploadImage::uploadImageToStorage($request->identity, 'stores' , 'identity');
                $identity->save();
            }
            if($request->hasFile('text_card')){
                if($store->identity){
                    Storage::disk('s3')->delete($store->text_card->image);
                }
                if($store->identity){
                    $store->text_card->delete();
                }
                $text_card = new StoreDocument();
                $text_card->store_id = $store['id'];
                $text_card->title_ar = 'بطاقة نصية';
                $text_card->title_en = 'Text card';
                $text_card->type = 'text_card';
                $text_card->status = 1;
                $text_card->image = UploadImage::uploadImageToStorage($request->text_card, 'stores','text_card');
                $text_card->save();
            }

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
    public function archive($id)
    {
        try {
            $user = Store::find($id);
            if ($user) {
                $type = request()->type;
                if($type == 'archive'){
                    $user->archive = true;
                    $user->save();
                    return response()->json([
                        'type' => 'success',
                        'title' => ' ارشفة الفرع',
                        'msg' => 'تم ارشفة الفرع بنجاح',
                    ]);
                }
            } else {
                return response()->json([
                    'type' => 'error',
                    'title' => ' بحث عن الفرع',
                    'msg' => 'فرع غير موجود'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'title' => 'مشكلة من الخادم',
                'msg' => 'يوجد مشكلة يرجى الرجوع الى المطور'
            ]);
        }
    }
}
