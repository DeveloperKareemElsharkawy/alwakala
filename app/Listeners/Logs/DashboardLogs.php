<?php

namespace App\Listeners\Logs;

use App\Models\DashboardLog;
use App\Repositories\ActivitiesRepository;
use Illuminate\Support\Facades\DB;

class DashboardLogs
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param \App\Events\Logs\DashboardLogs $event
     */
    public function handle(\App\Events\Logs\DashboardLogs $event)
    {
        $logData['id'] = $event->data['id'];
        $logData['action'] = $event->data['action'];
        $logData['name_ar'] = $event->data['ref_name_ar'];
        $logData['name_en'] = $event->data['ref_name_en'];
        $logData['editor_name'] = $event->data['user']->name;
        $logData['editor_id'] = $event->data['user']->id;
        $logData['role'] = ActivitiesRepository::getRoleName($event->data['user']->id);
        $logData['description'] = $event->data['user']->name .' '. trans('messages.actions.'.$event->data['action']). ' with id #'. $event->data['id'];
        $logData['updated_at'] = date("Y-m-d H:i:s");
        $logData['created_at'] = date("Y-m-d H:i:s");
        // DB::connection('mongodb')->collection($event->type)->insert($logData);
    }
}
