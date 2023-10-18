<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function city_ajax(Request $request)
    {
        $state_id = $request->input('state_id');
        $cities = City::select(
            'name_' . app()->getLocale() . ' as name',
            'id',
            'state_id'
        )->where('state_id', $state_id)->get();
        return \Response::json($cities);
    }
}
