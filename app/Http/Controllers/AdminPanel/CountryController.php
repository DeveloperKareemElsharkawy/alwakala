<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CountryController extends Controller
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
                $countries = Country::orderBy('id','desc')->where('archive' , true)->get();
            }else{
                $countries = Country::orderBy('id','desc')->where('archive' , false)->get();
            }
            return view('admin.settings.countries.index' , ['countries'=>$countries]);
        }catch (\Exception $e){
            DB::rollBack();
            Log::error('error in countries from admin panel ' . __LINE__ . $e);
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
            $material = new Country();
            $material->activation = true;
            $material->name_ar = $request->name_ar;
            $material->name_en = $request->name_en;
            $material->save();

            DB::commit();
            $request->session()->flash('status', 'تم الاضافة بنجاح');
            return redirect(route('countries.index'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in add country from admin panel ' . __LINE__ . $e);
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
        $material = Country::find($id);
        return view('admin.settings.countries.edit_popup', ['material' => $material]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $material = Country::find($id);
        if($material->activation == true){
            $material->activation = false;
            $material->save();
            return response()->json([
                'type' => 'success',
                'title' => 'تم ايقاف التفعيل',
                'msg' => 'تم ايقاف تفعيل الدولة بنجاح',
            ]);
        }else{
            $material->activation = true;
            $material->save();
            return response()->json([
                'type' => 'success',
                'title' => 'تم التفعيل',
                'msg' => 'تم تفعيل الدولة بنجاح',
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
            $material = Country::find($id);
            $material->name_ar = $request->name_ar;
            $material->name_en = $request->name_en;
            $material->save();

            DB::commit();
            $request->session()->flash('status', 'تم التعديل بنجاح');
            return redirect(route('countries.index'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in edit material from admin panel ' . __LINE__ . $e);
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
            $user = Country::find($id);
            if ($user) {
                $type = request()->type;
                if($type == 'delete'){
                    $user->delete();
                    return response()->json([
                        'type' => 'success',
                        'title' => ' حذف الدولة',
                        'msg' => 'تم حذف الدولة بنجاح',
                    ]);
                }
            } else {
                return response()->json([
                    'type' => 'error',
                    'title' => ' بحث عن الدولة',
                    'msg' => 'دولة غير موجود'
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
            $user = Country::find($id);
            if ($user) {
                $type = request()->type;
                if($type == 'archive'){
                    $user->archive = true;
                    $user->save();
                    return response()->json([
                        'type' => 'success',
                        'title' => ' ارشفة الدولة',
                        'msg' => 'تم ارشفة الدولة بنجاح',
                    ]);
                }
                if($type == 'restore'){
                    $user->archive = false;
                    $user->save();
                    return response()->json([
                        'type' => 'success',
                        'title' => 'استرجاع الدولة',
                        'msg' => 'تم استرجاع الدولة بنجاح',
                    ]);
                }

            } else {
                return response()->json([
                    'type' => 'error',
                    'title' => ' بحث عن الدولة',
                    'msg' => 'دولة غير موجود'
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
