<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
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
            if(request()->is('admin_panel/settings/categories')){
                if($type == 'archived'){
                    $categories = Category::whereNull('category_id')->orderBy('id','desc')->where('archive' , true)->get();
                }else{
                    $categories = Category::whereNull('category_id')->orderBy('id','desc')->where('archive' , false)->get();
                }
            }
            if(request()->is('admin_panel/settings/subcategories')){
                if($type == 'archived'){
                    $categories = Category::whereNotNull('category_id')->whereHas('parent' , function($q){
                        $q->whereNull('category_id');
                    })->orderBy('id','desc')->where('archive' , true)->get();
                }else{
                    $categories = Category::whereNotNull('category_id')->whereHas('parent' , function($q){
                        $q->whereNull('category_id');
                    })->orderBy('id','desc')->where('archive' , false)->get();
                }
            }
            if(request()->is('admin_panel/settings/subsubcategories')){
                if($type == 'archived'){
                    $categories = Category::whereNotNull('category_id')->whereHas('parent' , function($q){
                        $q->whereNotNull('category_id');
                    })->orderBy('id','desc')->where('archive' , true)->get();
                }else{
                    $categories = Category::whereNotNull('category_id')->whereHas('parent' , function($q){
                        $q->whereNotNull('category_id');
                    })->orderBy('id','desc')->where('archive' , false)->get();
                }
            }
            $main_categories = Category::whereNull('category_id')->orderBy('id' , 'desc')->get();

            return view('admin.settings.categories.index' , ['categories'=> $categories,'main_categories' => $main_categories]);
        }catch (\Exception $e){
            DB::rollBack();
            Log::error('error in categories from admin panel ' . __LINE__ . $e);
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
            $category = new Category();
            $category->activation = true;
            $category->name_ar = $request->name_ar;
            $category->name_en = $request->name_en;
            if($request->subcategory_id || $request->category_id){
                $category->category_id = $request->subcategory_id ? $request->subcategory_id : $request->category_id;
            }
            if (isset($request['image'])) {
                $category->image = UploadImage::uploadImageToStorage($request['image'], 'categoreis');
            }
            $category->save();
            DB::commit();
            $request->session()->flash('status', 'تم الاضافة بنجاح');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in add category from admin panel ' . __LINE__ . $e);
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
        $url = \request()->url;
        $category = Category::find($id);
        $categories = Category::whereNull('category_id')->orderBy('id' , 'desc')->get();
        $main_categories = Category::whereNull('category_id')->orderBy('id' , 'desc')->get();
        return view('admin.settings.categories.edit_popup', ['url' => $url,'category' => $category,'categories' => $categories,'main_categories' => $main_categories]);
    }

    public function tree()
    {
        $categories = Category::whereNull('category_id')->orderBy('id' , 'desc')->get();
        return view('admin.settings.categories.tree', ['categories' => $categories]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::find($id);
        if($category->activation == true){
            $category->activation = false;
            $category->save();
            return response()->json([
                'type' => 'success',
                'title' => 'تم ايقاف التفعيل',
                'msg' => 'تم ايقاف تفعيل التصنيف بنجاح',
            ]);
        }else{
            $category->activation = true;
            $category->save();
            return response()->json([
                'type' => 'success',
                'title' => 'تم التفعيل',
                'msg' => 'تم تفعيل التصنيف بنجاح',
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
            $category = Category::find($id);
            $category->activation = true;
            $category->name_ar = $request->name_ar;
            $category->name_en = $request->name_en;
            if($request->subcategory_id || $request->category_id){
                $category->category_id = $request->subcategory_id ? $request->subcategory_id : $request->category_id;
            }
            if (isset($request['image'])) {
                $category->image = UploadImage::uploadImageToStorage($request['image'], 'categories');
            }
            $category->save();
            DB::commit();
            $request->session()->flash('status', 'تم التعديل بنجاح');
            return redirect()->back();
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
            $user = Category::find($id);
            if ($user) {
                $type = request()->type;
                if($type == 'delete'){
                    Storage::disk('s3')->delete($user->image);
                    $user->delete();
                    return response()->json([
                        'type' => 'success',
                        'title' => ' حذف التصنيفات',
                        'msg' => 'تم حذف التصنيفات بنجاح',
                    ]);
                }
            } else {
                return response()->json([
                    'type' => 'error',
                    'title' => ' بحث عن التصنيفات',
                    'msg' => 'تصنيف غير موجود'
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
            $user = Category::find($id);
            if ($user) {
                $type = request()->type;
                if($type == 'archive'){
                    $user->archive = true;
                    $user->save();
                    return response()->json([
                        'type' => 'success',
                        'title' => ' ارشفة التصنيف',
                        'msg' => 'تم ارشفة التصنيف بنجاح',
                    ]);
                }
                if($type == 'restore'){
                    $user->archive = false;
                    $user->save();
                    return response()->json([
                        'type' => 'success',
                        'title' => 'استرجاع التصنيف',
                        'msg' => 'تم استرجاع التصنيف بنجاح',
                    ]);
                }

            } else {
                return response()->json([
                    'type' => 'error',
                    'title' => ' بحث عن التصنيف',
                    'msg' => 'تصنيف غير موجود'
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

    public function ajax_subcatgeories(Request $request)
    {
        $category_id = $request->input('category_id');
        $subtypes = Category::where('category_id', $category_id)->select('id' , 'name_' . app()->getLocale() .' as name')->get();
        return response()->json($subtypes);
    }

}
