<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Region;
use App\Models\State;
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

    public function state_ajax(Request $request)
    {
        $region_id = $request->input('region_id');
        $states = State::select(
            'name_' . app()->getLocale() . ' as name',
            'id',
            'region_id'
        )->where('region_id', $region_id)->get();
        return \Response::json($states);
    }

    public function region_ajax(Request $request)
    {
        $country_id = $request->input('country_id');
        $regions = Region::select(
            'name_' . app()->getLocale() . ' as name',
            'id',
            'country_id'
        )->where('country_id', $country_id)->get();
        return \Response::json($regions);
    }
}
