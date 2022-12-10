<?php

namespace App\Http\Resources\Dashboard\Response;


class ApiDashboardResource
{
    /**
     * Rerponse Dashboard api index .
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    static public function index($data)
    {
        return [
            'status' => true,
            'message' =>  $data['message'],
            'data' =>  $data['data'],
            'offset' => (int)$data['offset'],
            'limit' => (int) $data['limit'],
            'total' =>  $data['count']
        ];
    }

    /**
     * Rerponse Dashboard api show .
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    static public function show($data)
    {
        return [
            'data' =>  $data['object'],
        ];
    }
}
