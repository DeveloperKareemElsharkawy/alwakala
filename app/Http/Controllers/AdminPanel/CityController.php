<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CityController extends Controller
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
                $cities = City::orderBy('id','desc')->where('archive' , true)->get();
            }else{
                $cities = City::orderBy('id','desc')->where('archive' , false)->get();
            }
           $countries = Country::all();
            return view('admin.settings.cities.index' , ['cities'=>$cities,'countries'=>$countries]);
//        }catch (\Exception $e){
//            DB::rollBack();
//            Log::error('error in cities from admin panel ' . __LINE__ . $e);
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
            $material = new City();
            $material->activation = true;
            $material->name_ar = $request->name_ar;
            $material->name_en = $request->name_en;
            $material->state_id = $request->state_id;
            $material->save();

            DB::commit();
            $request->session()->flash('status', 'تم الاضافة بنجاح');
            return redirect(route('cities.index'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in add city from admin panel ' . __LINE__ . $e);
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
        $material = City::find($id);
        $countreis = Country::all();
        $region = Region::find($material['state']['region_id']);
        $old_country = $region['country'];
        $regions = Region::where('country_id' , $material['state']['region_id'])->get();
        $states = State::where('region_id' , $region['id'])->get();
        return view('admin.settings.cities.edit_popup', ['material' => $material , 'countries'=>$countreis,
            'old_country' =>$old_country,'regions' => $regions , 'states'=>$states]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $material = City::find($id);
        if($material->activation == true){
            $material->activation = false;
            $material->save();
            return response()->json([
                'type' => 'success',
                'title' => 'تم ايقاف التفعيل',
                'msg' => 'تم ايقاف تفعيل المدينة بنجاح',
            ]);
        }else{
            $material->activation = true;
            $material->save();
            return response()->json([
                'type' => 'success',
                'title' => 'تم التفعيل',
                'msg' => 'تم تفعيل المدينة بنجاح',
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
            $material = City::find($id);
            $material->name_ar = $request->name_ar;
            $material->name_en = $request->name_en;
            $material->state_id = $request->state_id;
            $material->save();

            DB::commit();
            $request->session()->flash('status', 'تم التعديل بنجاح');
            return redirect(route('cities.index'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in edit cities from admin panel ' . __LINE__ . $e);
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
            $user = City::find($id);
            if ($user) {
                $type = request()->type;
                if($type == 'delete'){
                    $user->delete();
                    return response()->json([
                        'type' => 'success',
                        'title' => ' حذف المدينة',
                        'msg' => 'تم حذف المدينة بنجاح',
                    ]);
                }
            } else {
                return response()->json([
                    'type' => 'error',
                    'title' => ' بحث عن المدينة',
                    'msg' => 'مدينة غير موجود'
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
            $user = City::find($id);
            if ($user) {
                $type = request()->type;
                if($type == 'archive'){
                    $user->archive = true;
                    $user->save();
                    return response()->json([
                        'type' => 'success',
                        'title' => ' ارشفة المدينة',
                        'msg' => 'تم ارشفة المدينة بنجاح',
                    ]);
                }
                if($type == 'restore'){
                    $user->archive = false;
                    $user->save();
                    return response()->json([
                        'type' => 'success',
                        'title' => 'استرجاع المدينة',
                        'msg' => 'تم استرجاع المدينة بنجاح',
                    ]);
                }

            } else {
                return response()->json([
                    'type' => 'error',
                    'title' => ' بحث عن المدينة',
                    'msg' => 'مدينة غير موجود'
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
