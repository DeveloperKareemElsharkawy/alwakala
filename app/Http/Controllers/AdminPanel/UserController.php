<?php

namespace App\Http\Controllers\AdminPanel;

use App\Enums\Activity\Activities;
use App\Enums\AStatusCodeResponse;
use App\Enums\UserTypes\UserType;
use App\Events\Logs\DashboardLogs;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $active = request()->type ? request()->type : true;
            $date = request()->date;
            $users = User::orderBy('id','desc');
            if($active){
                $users = $users->where('activation', $active);
            }
            if($date){
                $users = $users->whereDate('created_at', $date);
            }
            $users = $users->get();
            return view('admin.users.index' , ['users' => $users , 'request' => $active]);
        }catch (\Exception $e){
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $user = new User();
            if($request->type == 'CONSUMER'){
                $data['type_id'] = UserType::CONSUMER;
            }elseif($request->type == 'ADMIN'){
                $data['type_id'] = UserType::ADMIN;
            }elseif($request->type == 'SELLER'){
                $data['type_id'] = UserType::SELLER;
            }

            $data['activation'] = true;
            $user->initializeUserFields($data);
            $user->save();

            DB::commit();
            $request->session()->flash('status', 'تم الاضافة بنجاح');
            return redirect(route('users.index'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in add user from admin panel ' . __LINE__ . $e);
            $request->session()->flash('error', 'خطأ يرجى التأكد من المشكلة');

            return redirect()->back();
        }
    }

    public function user_info($id)
    {
        $user = \App\Models\User::find($id);
        $roles = Role::all();
        return view('admin.users.edit_popup', ['user' => $user,'roles' => $roles]);
    }

    public function user_show($id)
    {
        $user = \App\Models\User::find($id);
        $roles = Role::all();
        return view('admin.users.user_show_popup', ['user' => $user,'roles' => $roles]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
            $data = $request->all();
            $user = User::find($id);
            if($request->type == 'CONSUMER'){
                $data['type_id'] = UserType::CONSUMER;
            }elseif($request->type == 'ADMIN'){
                $data['type_id'] = UserType::ADMIN;
            }elseif($request->type == 'SELLER'){
                $data['type_id'] = UserType::SELLER;
            }
            $data['activation'] = true;
            if(isset($date['image'])){
                Storage::disk('s3')->delete($user->image);
            }
            $user->initializeUserFields($data);
            $user->save();

            DB::commit();
            $request->session()->flash('status', 'تم التعديل بنجاح');
            return redirect(route('users.index'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in add user from admin panel ' . __LINE__ . $e);
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
    public function archive($id)
    {
        try {
            $user = User::find($id);
            if ($user) {
                $user->activation = false;
                $user->save();
                return response()->json([
                    'type' => 'success',
                    'title' => ' ارشفة المستخدم',
                    'msg' => 'تم ارشفة المستخدم بنجاح',
                ]);
            } else {
                return response()->json([
                    'type' => 'error',
                    'title' => ' ارشفة المستخدم',
                    'msg' => 'مستخدم غير موجود'
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

    public function restore($id)
    {
        try {
            $user = User::find($id);
            if ($user) {
                $user->activation = true;
                $user->save();
                return response()->json([
                    'type' => 'success',
                    'title' => ' استرجاع المستخدم من الارشيف',
                    'msg' => 'تم استرجاع المستخدم بنجاح',
                ]);
            } else {
                return response()->json([
                    'type' => 'error',
                    'title' => ' استرجاع المستخدم',
                    'msg' => 'مستخدم غير موجود'
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

    public function destroy($id)
    {
        try {
            $user = User::find($id);
            if ($user) {
                Storage::disk('s3')->delete($user->image);
                $user->delete();
                return response()->json([
                    'type' => 'success',
                    'title' => ' حذف المستخدم نهائيا',
                    'msg' => 'تم حذف المستخدم بنجاح',
                ]);
            } else {
                return response()->json([
                    'type' => 'error',
                    'title' => ' حذف المستخدم',
                    'msg' => 'مستخدم غير موجود'
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
