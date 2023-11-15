<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        try{
            $type = request()->type && request()->type == 'archived' ? 'archived' : '';
            if($type == 'archived'){
                $regions = Region::orderBy('id','desc')->where('archive' , true)->get();
            }else{
                $regions = Region::orderBy('id','desc')->where('archive' , false)->get();
            }
           $countries = Country::all();
            return view('admin.settings.regions.index' , ['regions'=>$regions,'countries'=>$countries]);
//        }catch (\Exception $e){
//            DB::rollBack();
//            Log::error('error in regions from admin panel ' . __LINE__ . $e);
//            request()->session()->flash('error', 'خطأ يرجى التأكد من المشكلة');
//            return redirect()->back();
//        }
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
            $material = new Region();
            $material->activation = true;
            $material->name_ar = $request->name_ar;
            $material->name_en = $request->name_en;
            $material->country_id = $request->country_id;
            $material->save();

            DB::commit();
            $request->session()->flash('status', 'تم الاضافة بنجاح');
            return redirect(route('regions.index'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in add region from admin panel ' . __LINE__ . $e);
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
        $material = Region::find($id);
        $countreis = Country::all();
        return view('admin.settings.regions.edit_popup', ['material' => $material , 'countries'=>$countreis]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $material = Region::find($id);
        if($material->activation == true){
            $material->activation = false;
            $material->save();
            return response()->json([
                'type' => 'success',
                'title' => 'تم ايقاف التفعيل',
                'msg' => 'تم ايقاف تفعيل الاقليم بنجاح',
            ]);
        }else{
            $material->activation = true;
            $material->save();
            return response()->json([
                'type' => 'success',
                'title' => 'تم التفعيل',
                'msg' => 'تم تفعيل الاقليم بنجاح',
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
            $material = Region::find($id);
            $material->name_ar = $request->name_ar;
            $material->name_en = $request->name_en;
            $material->country_id = $request->country_id;
            $material->save();

            DB::commit();
            $request->session()->flash('status', 'تم التعديل بنجاح');
            return redirect(route('regions.index'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in edit regions from admin panel ' . __LINE__ . $e);
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
            $user = Region::find($id);
            if ($user) {
                $type = request()->type;
                if($type == 'delete'){
                    $user->delete();
                    return response()->json([
                        'type' => 'success',
                        'title' => ' حذف الاقليم',
                        'msg' => 'تم حذف الاقليم بنجاح',
                    ]);
                }
            } else {
                return response()->json([
                    'type' => 'error',
                    'title' => ' بحث عن الاقليم',
                    'msg' => 'اقليم غير موجود'
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
            $user = Region::find($id);
            if ($user) {
                $type = request()->type;
                if($type == 'archive'){
                    $user->archive = true;
                    $user->save();
                    return response()->json([
                        'type' => 'success',
                        'title' => ' ارشفة الاقليم',
                        'msg' => 'تم ارشفة الاقليم بنجاح',
                    ]);
                }
                if($type == 'restore'){
                    $user->archive = false;
                    $user->save();
                    return response()->json([
                        'type' => 'success',
                        'title' => 'استرجاع الاقليم',
                        'msg' => 'تم استرجاع الاقليم بنجاح',
                    ]);
                }

            } else {
                return response()->json([
                    'type' => 'error',
                    'title' => ' بحث عن الاقليم',
                    'msg' => 'اقليم غير موجود'
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
