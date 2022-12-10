<?php


namespace App\Repositories;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationType;

class NotificationRepository extends Controller
{

    public static function listNotifications($userId)
    {
        $notification = Notification::query()->where('user_id', $userId)->paginate(10);
        return self::prepareNotificationData($notification);
    }

    public static function makeAllRead($userId)
    {

        return Notification::query()->where('user_id', $userId)->update(['is_read' => 1]);
    }

    public static function makeReadById($id)
    {
        return Notification::query()->where('id', $id)->update(['is_read' => 1]);
    }

    public static function unReadCount($userId)
    {
        return Notification::query()->where('user_id', $userId)->where('is_read', 0)->get()->count();
    }

    public static function Save($data)
    {
        $notify = new Notification();
        $notify->title = $data['title'];
        $notify->body = $data['body'];
        $notify->item_id = $data['item_id'];
        $notify->image = $data['image'];
        $notify->item_type = $data['item_type'];
        $notify->user_id = $data['user_id'];
        $notify->save();
    }

    public static function getNotificationTypeId($name)
    {
        $row = NotificationType::query()->where('type', $name)->first();
        if ($row) {
            return $row->id;
        } else {
            return null;
        }
    }

    private static function prepareNotificationData($notification)
    {
        $data = [];
        $i = 1;
        foreach ($notification as $item) {
            $itemType = NotificationType::query()->where('id', $item->item_type)->first();
            $body = $item->body;
            $data[$i]['body'] = trans("messages.notifications.$body");
            $data[$i]['user_id'] = $item->user_id;
            $data[$i]['is_read'] = $item->is_read;
            $data[$i]['item_id'] = $item->item_id;
            $data[$i]['item_type'] = ($itemType) ? $itemType->type : '';
            $data[$i]['image'] = ($item->image) ? config('filesystems.aws_base_url') . $item->image : '';
            $data[$i]['created_at'] = $item->created_at;
            $i++;
        }
        return $data;
    }

}
