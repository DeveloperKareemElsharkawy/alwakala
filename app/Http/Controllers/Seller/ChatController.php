<?php

namespace App\Http\Controllers\Seller;

use App\Events\Chat\MessageSent;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\SellerApp\Chat\SendMessageRequest;
use App\Http\Requests\SellerApp\Chat\ShowConversationRequest;
use App\Http\Resources\Seller\Chat\ConversationResource;
use App\Http\Resources\Seller\Chat\MessageResource;
use App\Http\Resources\Seller\Chat\MessagesCollection;
use App\Http\Resources\Seller\Orders\UserResource;
use App\Lib\Helpers\StoreId\StoreId;
use App\Lib\Helpers\UserId\UserId;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Store;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatController extends BaseController
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function conversations(Request $request)
    {
        try {
            $storeId = StoreId::getStoreID($request);

             $conversations = Conversation::query()->with('message')
                ->where('store_receiver_id', $storeId)->orWhere('store_sender_id', $storeId)->get();

            return $this->success(['message' => trans('messages.general.listed'), 'data' => ConversationResource::collection($conversations)]);
        } catch (Exception $e) {
            Log::error('error in index of Seller Cart ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @throws Exception
     */
    public function sendMessage(SendMessageRequest $request)
    {
        try {
            $data = $request->validated();

            $data['image'] = $request['image'] ? UploadImage::uploadVideoToStorage($request['image'], 'chat/images') : null;
            $data['video'] = $request['video'] ? UploadImage::uploadVideoToStorage($request['video'], 'chat/videos') : null;
            $data['record'] = $request['record'] ? UploadImage::uploadRecordToStorage($request['record'], 'chat/record') : null;

            $conversation = Conversation::query()->with('message')
                ->where([['store_receiver_id', $data['store_sender_id']], ['store_sender_id', $data['store_receiver_id']]])
                ->orWhere([['store_receiver_id', $data['store_receiver_id']], ['store_sender_id', $data['store_sender_id']]])->first();

            if (!$conversation) {
                $conversation = Conversation::query()->create([
                    'sender_id' => $data['sender_id'],
                    'store_sender_id' => $data['store_sender_id'],
                    'receiver_id' => $data['receiver_id'],
                    'store_receiver_id' => $data['store_receiver_id'],
                ]);
            }

            $data['conversation_id'] = $conversation['id'];

            $message = Message::query()->create($data);

            event(new MessageSent($data['store_receiver_id'], $data['store_sender_id'], new UserResource($request->user('api')), new MessageResource($message)));

            return $this->success(['message' => trans('messages.general.listed'), 'data' => new MessageResource($message)]);
        } catch (Exception $e) {
            Log::error('error in index of Seller Cart ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /***
     * @param ShowConversationRequest $request
     * @return MessagesCollection|JsonResponse
     */
    public function showConversation(ShowConversationRequest $request)
    {
        try {
            $storeId = StoreId::getStoreID($request);

            $messages = Message::query()->where('store_receiver_id', $storeId)->orWhere('store_sender_id', $storeId)->latest()->paginate(10);

            return new MessagesCollection($messages);

        } catch (Exception $e) {
            Log::error('error in index of Seller Cart ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
