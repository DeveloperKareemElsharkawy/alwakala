<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Size;
use App\Models\CategorySize;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SizeController extends Controller
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
                $sizes = Size::orderBy('id','desc')->where('archive' , true)->get();
            }else{
                $sizes = Size::orderBy('id','desc')->where('archive' , false)->get();
            }
            $categories = Category::orderBy('id' , 'desc')->get();

            return view('admin.settings.sizes.index' , ['sizes'=>$sizes,'categories' => $categories]);
        }catch (\Exception $e){
            DB::rollBack();
            Log::error('error in sizes from admin panel ' . __LINE__ . $e);
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
            $size = new Size();
            $size->activation = true;
            $size->size = $request->size;
            $size->size_type = 2;
            $size->save();
            $category_ids = $request->category_ids;
            foreach ($category_ids as $category_id){
                $size_category = new CategorySize();
                $size_category->size_id = $size['id'];
                $size_category->category_id = $category_id;
                $size_category->save();
            }
            DB::commit();
            $request->session()->flash('status', 'تم الاضافة بنجاح');
            return redirect(route('sizes.index'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in add size from admin panel ' . __LINE__ . $e);
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
        $size = Size::find($id);
        $categories = Category::orderBy('id' , 'desc')->get();
        return view('admin.settings.sizes.edit_popup', ['size' => $size,'categories' => $categories]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $size = Size::find($id);
        if($size->activation == true){
            $size->activation = false;
            $size->save();
            return response()->json([
                'type' => 'success',
                'title' => 'تم ايقاف التفعيل',
                'msg' => 'تم ايقاف تفعيل المقاس بنجاح',
            ]);
        }else{
            $size->activation = true;
            $size->save();
            return response()->json([
                'type' => 'success',
                'title' => 'تم التفعيل',
                'msg' => 'تم تفعيل المقاس بنجاح',
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
            $size = Size::find($id);
            $size->activation = true;
            $size->size = $request->size;
            $size->save();
            $category_ids = $request->category_ids;
            DB::table('category_size')->where('size_id' , $id)->delete();
            foreach ($category_ids as $category_id){
                $size_category = new CategorySize();
                $size_category->size_id = $size['id'];
                $size_category->category_id = $category_id;
                $size_category->save();
            }
            DB::commit();
            $request->session()->flash('status', 'تم التعديل بنجاح');
            return redirect(route('sizes.index'));
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
            $user = Size::find($id);
            if ($user) {
                $type = request()->type;
                if($type == 'delete'){
                    Storage::disk('s3')->delete($user->image);
                    DB::table('category_size')->where('size_id' , $id)->delete();
                    $user->delete();
                    return response()->json([
                        'type' => 'success',
                        'title' => ' حذف المقاس',
                        'msg' => 'تم حذف المقاس بنجاح',
                    ]);
                }
            } else {
                return response()->json([
                    'type' => 'error',
                    'title' => ' بحث عن المقاس',
                    'msg' => 'مقاس غير موجود'
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
            $user = Size::find($id);
            if ($user) {
                $type = request()->type;
                if($type == 'archive'){
                    $user->archive = true;
                    $user->save();
                    return response()->json([
                        'type' => 'success',
                        'title' => ' ارشفة المقاس',
                        'msg' => 'تم ارشفة المقاس بنجاح',
                    ]);
                }
                if($type == 'restore'){
                    $user->archive = false;
                    $user->save();
                    return response()->json([
                        'type' => 'success',
                        'title' => 'استرجاع المقاس',
                        'msg' => 'تم استرجاع المقاس بنجاح',
                    ]);
                }

            } else {
                return response()->json([
                    'type' => 'error',
                    'title' => ' بحث عن المقاس',
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
