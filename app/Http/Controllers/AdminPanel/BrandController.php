<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\Brand;
use App\Models\BrandCategory;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $type = request()->type && request()->type == 'archived' ? 'archived' : '';
            if($type == 'archived'){
                $brands = Brand::orderBy('id','desc')->where('archive' , true)->get();
            }else{
                $brands = Brand::orderBy('id','desc')->where('archive' , false)->get();
            }
            $categories = Category::orderBy('id' , 'desc')->get();

            return view('admin.settings.brands.index' , ['brands'=>$brands,'categories' => $categories]);
        }catch (\Exception $e){
            DB::rollBack();
            Log::error('error in brands from admin panel ' . __LINE__ . $e);
            request()->session()->flash('error', 'خطأ يرجى التأكد من المشكلة');
            return redirect()->back();
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $brand = new Brand();
            $brand->activation = true;
            $brand->name_ar = $request->name_ar;
            $brand->name_en = $request->name_en;
            if (isset($request['image'])) {
                $brand->image = UploadImage::uploadImageToStorage($request['image'], 'brands');
            }
            $brand->save();
            $category_ids = $request->category_ids;
            foreach ($category_ids as $category_id){
                $brand_category = new BrandCategory();
                $brand_category->brand_id = $brand['id'];
                $brand_category->category_id = $category_id;
                $brand_category->save();
            }
            DB::commit();
            $request->session()->flash('status', 'تم الاضافة بنجاح');
            return redirect(route('brands.index'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in add brand from admin panel ' . __LINE__ . $e);
            $request->session()->flash('error', 'خطأ يرجى التأكد من المشكلة');
            return redirect()->back();
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
        $brand = Brand::find($id);
        $categories = Category::orderBy('id' , 'desc')->get();
        return view('admin.settings.brands.edit_popup', ['brand' => $brand,'categories' => $categories]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $brand = Brand::find($id);
        if($brand->activation == true){
            $brand->activation = false;
            $brand->save();
            return response()->json([
                'type' => 'success',
                'title' => 'تم ايقاف التفعيل',
                'msg' => 'تم ايقاف تفعيل البراند بنجاح',
            ]);
        }else{
            $brand->activation = true;
            $brand->save();
            return response()->json([
                'type' => 'success',
                'title' => 'تم التفعيل',
                'msg' => 'تم تفعيل البراند بنجاح',
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $brand = Brand::find($id);
            $brand->activation = true;
            $brand->name_ar = $request->name_ar;
            $brand->name_en = $request->name_en;
            if (isset($request['image'])) {
                $brand->image = UploadImage::uploadImageToStorage($request['image'], 'brands');
            }
            $brand->save();
            $category_ids = $request->category_ids;
            DB::table('brand_category')->where('brand_id' , $id)->delete();
            foreach ($category_ids as $category_id){
                $brand_category = new BrandCategory();
                $brand_category->brand_id = $brand['id'];
                $brand_category->category_id = $category_id;
                $brand_category->save();
            }
            DB::commit();
            $request->session()->flash('status', 'تم التعديل بنجاح');
            return redirect(route('brands.index'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in edit brand from admin panel ' . __LINE__ . $e);
            $request->session()->flash('error', 'خطأ يرجى التأكد من المشكلة');
            return redirect()->back();
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
        try {
            $user = Brand::find($id);
            if ($user) {
                $type = request()->type;
                if($type == 'delete'){
                    Storage::disk('s3')->delete($user->image);
                    DB::table('brand_category')->where('brand_id' , $id)->delete();
                    DB::table('brand_store')->where('brand_id' , $id)->delete();
                    $user->delete();
                    return response()->json([
                        'type' => 'success',
                        'title' => ' حذف البراند',
                        'msg' => 'تم حذف البراند بنجاح',
                    ]);
                }
            } else {
                return response()->json([
                    'type' => 'error',
                    'title' => ' بحث عن البراند',
                    'msg' => 'براند غير موجود'
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

    public function archive($id)
    {
        try {
            $user = Brand::find($id);
            if ($user) {
                $type = request()->type;
                if($type == 'archive'){
                    $user->archive = true;
                    $user->save();
                    return response()->json([
                        'type' => 'success',
                        'title' => ' ارشفة البراند',
                        'msg' => 'تم ارشفة البراند بنجاح',
                    ]);
                }
                if($type == 'restore'){
                    $user->archive = false;
                    $user->save();
                    return response()->json([
                        'type' => 'success',
                        'title' => 'استرجاع البراند',
                        'msg' => 'تم استرجاع البراند بنجاح',
                    ]);
                }

            } else {
                return response()->json([
                    'type' => 'error',
                    'title' => ' بحث عن البراند',
                    'msg' => 'براند غير موجود'
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
