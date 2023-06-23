<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\DeliveryMan;
use App\Models\UserInfo;
use App\Models\Message;
use App\Models\Vendor;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ConversationController extends Controller
{
    public function messages_store(Request $request)
    {
        if ($request->has('image')) {
            $image_name=[];
            foreach($request->file('image') as $key=>$img)
            {

                $name = Helpers::upload('conversation/', 'png', $img);
                array_push($image_name,$name);
            }
        } else {
            $image_name = null;
        }

        $limit = $request['limit']??10;
        $offset = $request['offset']??1;

        $vendor = Vendor::find($request->vendor->id);
        $sender = UserInfo::where('vendor_id', $vendor->id)->first();
        if(!$sender){
            $sender = new UserInfo();
            $sender->vendor_id = $vendor->id;
            $sender->f_name = $vendor->stores[0]->name;
            $sender->l_name = '';
            $sender->phone = $vendor->phone;
            $sender->email = $vendor->email;
            $sender->image = $vendor->stores[0]->logo;
            $sender->save();
        }

        if($request->conversation_id){
            $conversation = Conversation::find($request->conversation_id);

            if($conversation->sender_id == $sender->id){
                $receiver_id = $conversation->receiver_id;
                $receiver = UserInfo::find($receiver_id);
                if($receiver->deliveryman_id){
                    $delivery_man = DeliveryMan::find($receiver->deliveryman_id);
                    $fcm_token=$delivery_man->fcm_token;
                }elseif($receiver->user_id){
                    $user = User::find($receiver->user_id);
                    $fcm_token=$user->cm_firebase_token;
                }
            }else{
                $receiver_id =$conversation->sender_id;
                $receiver = UserInfo::find($receiver_id);
                if($receiver->deliveryman_id){
                    $delivery_man = DeliveryMan::find($receiver->deliveryman_id);
                    $fcm_token=$delivery_man->fcm_token;
                }elseif($receiver->user_id){
                    $user = User::find($receiver->user_id);
                    $fcm_token=$user->cm_firebase_token;
                }
            }
        }else{

            if($request->receiver_type == 'customer'){
                $receiver = UserInfo::where('user_id',$request->receiver_id)->first();
                $user = User::find($request->receiver_id);

                if(!$receiver){
                    $receiver = new UserInfo();
                    $receiver->user_id = $user->id;
                    $receiver->f_name = $user->f_name;
                    $receiver->l_name = $user->l_name;
                    $receiver->phone = $user->phone;
                    $receiver->email = $user->email;
                    $receiver->image = $user->image;
                    $receiver->save();
                }
                $receiver_id = $receiver->id;
                $fcm_token=$user->cm_firebase_token;

            }else if($request->receiver_type == 'delivery_man'){
                $receiver = UserInfo::where('deliveryman_id',$request->receiver_id)->first();
                $delivery_man = DeliveryMan::find($request->receiver_id);

                if(!$receiver){
                    $receiver = new UserInfo();
                    $receiver->deliveryman_id = $delivery_man->id;
                    $receiver->f_name = $delivery_man->f_name;
                    $receiver->l_name = $delivery_man->l_name;
                    $receiver->phone = $delivery_man->phone;
                    $receiver->email = $delivery_man->email;
                    $receiver->image = $delivery_man->image;
                    $receiver->save();
                }
                $receiver_id = $receiver->id;
                $fcm_token=$delivery_man->fcm_token;
            }
        }
        $conversation = Conversation::with('sender','receiver','last_message')->WhereConversation($sender->id,$receiver_id)->first();


        if(!$conversation){
            $conversation = new Conversation;
            $conversation->sender_id = $sender->id;
            $conversation->sender_type = 'vendor';
            $conversation->receiver_id = $receiver->id;
            $conversation->receiver_type = $request->receiver_type;
            $conversation->unread_message_count = 0;
            $conversation->last_message_time = Carbon::now()->toDateTimeString();
            $conversation->save();
            $conversation= Conversation::find($conversation->id);
        }

        $message = new Message();
        $message->conversation_id = $conversation->id;
        $message->sender_id = $sender->id;
        $message->message = $request->message;
        $message->file = $image_name?json_encode($image_name, JSON_UNESCAPED_SLASHES):null;
        try {
            if($message->save())
            {
                $conversation->unread_message_count = $conversation->unread_message_count? $conversation->unread_message_count+1:1;
                $conversation->last_message_id=$message->id;
                $conversation->last_message_time = Carbon::now()->toDateTimeString();
                $conversation->save();

                $data = [
                    'title' =>'Message',
                    'description' =>'You have received new message',
                    'order_id' => '',
                    'image' => '',
                    'type'=> 'message',
                    'conversation_id'=> $conversation->id,
                    'sender_type'=> 'vendor'
                ];
                Helpers::send_push_notif_to_device($fcm_token, $data);

            }

        } catch (\Exception $e) {
            info($e);
        }

        $messages = Message::where(['conversation_id' => $conversation->id])->latest()->paginate($limit, ['*'], 'page', $offset);

        $conv = Conversation::with('sender','receiver','last_message')->find($conversation->id);

        if($conv->sender_type == 'customer' && $conversation->sender){
            $user = User::find($conv->sender->user_id);
            $order = Order::where('store_id',$vendor->stores[0]->id)->where('user_id', $user->id)->whereIn('order_status', ['pending','accepted','confirmed','processing','handover','picked_up'])->count();
        }else if($conv->receiver_type == 'customer' && $conversation->receiver){
            $user = User::find($conv->receiver->user_id);
            $order = Order::where('store_id',$vendor->stores[0]->id)->where('user_id', $user->id)->whereIn('order_status', ['pending','accepted','confirmed','processing','handover','picked_up'])->count();
        }else if($conv->sender_type == 'delivery_man' && $conversation->sender){
            $user2 = DeliveryMan::find($conv->sender->deliveryman_id);
            $order = Order::where('store_id',$vendor->stores[0]->id)->where('delivery_man_id', $user2->id)->whereIn('order_status', ['pending','accepted','confirmed','processing','handover','picked_up'])->count();
        }else if($conv->receiver_type == 'delivery_man' && $conversation->receiver){
            $user2 = DeliveryMan::find($conv->receiver->deliveryman_id);
            $order = Order::where('store_id',$vendor->stores[0]->id)->where('delivery_man_id', $user2->id)->whereIn('order_status', ['pending','accepted','confirmed','processing','handover','picked_up'])->count();
        }
        else{
            $order=0;
        }


        $data =  [
            'total_size' => intval($messages->total()),
            'limit' => intval($limit),
            'offset' => intval($offset),
            'status' => ($order>0)?true:false,
            'message' => 'successfully sent!',
            'messages' => $messages->items(),
            'conversation' => $conv,
        ];
        return response()->json($data, 200);
    }

    public function conversations(Request $request)
    {
        $limit = $request['limit']??10;
        $offset = $request['offset']??1;

        $vendor = Vendor::find($request->vendor->id);

        $sender = UserInfo::where('vendor_id', $vendor->id)->first();

        if(!$sender){
            $sender = new UserInfo();
            $sender->vendor_id = $vendor->id;
            $sender->f_name = $vendor->stores[0]->name;
            $sender->l_name = '';
            $sender->phone = $vendor->phone;
            $sender->email = $vendor->email;
            $sender->image = $vendor->stores[0]->logo;
            $sender->save();
        }

        $conversations = Conversation::with(['sender', 'receiver','last_message'])->where(['sender_id' => $sender->id])->orWhere(['receiver_id' => $sender->id])->orderBy('last_message_time', 'DESC')->paginate($limit, ['*'], 'page', $offset);


        $data =  [
            'total_size' => intval($conversations->total()),
            'limit' => intval($limit),
            'offset' => intval($offset),
            'conversation' => $conversations->items()
        ];

        return response()->json($data, 200);
    }

    public function search_conversations(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $key = explode(' ', $request['name']);

        $limit = $request['limit']??10;
        $offset = $request['offset']??1;

        $vendor = Vendor::find($request->vendor->id);

        $sender = UserInfo::where('vendor_id', $vendor->id)->first();

        if(!$sender){
            $sender = new UserInfo();
            $sender->vendor_id = $vendor->id;
            $sender->f_name = $vendor->stores[0]->name;
            $sender->l_name = '';
            $sender->phone = $vendor->phone;
            $sender->email = $vendor->email;
            $sender->image = $vendor->stores[0]->logo;
            $sender->save();
        }

        $conversations = Conversation::with('sender','receiver','last_message')->WhereUser($sender->id)->where(function($qu)use($key){
                    $qu->whereHas('sender',function($query)use($key){
                    foreach ($key as $value) {
                        $query->where('f_name', 'like', "%{$value}%")->orWhere('l_name', 'like', "%{$value}%");
                    }
                })
                ->orWhereHas('receiver',function($query1)use($key){
                    foreach ($key as $value) {
                        $query1->where('f_name', 'like', "%{$value}%")->orWhere('l_name', 'like', "%{$value}%");
                    }
                });
            });

        $conversations = $conversations->orderBy('last_message_time', 'DESC')->paginate($limit, ['*'], 'page', $offset);

        $data =  [
            'total_size' => intval($conversations->total()),
            'limit' => intval($limit),
            'offset' => intval($offset),
            'conversation' => $conversations->items()
        ];
        return response()->json($data, 200);
    }

    public function messages(Request $request)
    {

        $limit = $request['limit']??10;
        $offset = $request['offset']??1;

        $vnd = Vendor::find($request->vendor->id);
        $vendor = UserInfo::where('vendor_id', $vnd->id)->first();
        if(!$vendor){
            $vendor = new UserInfo();
            $vendor->vendor_id = $vnd->id;
            $vendor->f_name = $vnd->stores[0]->name;
            $vendor->l_name = '';
            $vendor->phone = $vnd->phone;
            $vendor->email = $vnd->email;
            $vendor->image = $vnd->stores[0]->logo;
            $vendor->save();
        }

        if($request->conversation_id){
            $conversation = Conversation::with(['sender','receiver'])->find($request->conversation_id);
        }else if($request->delivery_man_id){
            $dm = UserInfo::where('deliveryman_id', $request->delivery_man_id)->first();
            if(!$dm){
                $user = DeliveryMan::find($request->delivery_man_id);
                $dm = new UserInfo();
                $dm->deliveryman_id = $user->id;
                $dm->f_name = $user->f_name;
                $dm->l_name = $user->l_name;
                $dm->phone = $user->phone;
                $dm->email = $user->email;
                $dm->image = $user->image;
                $dm->save();
            }
            $conversation = Conversation::with(['sender','receiver','last_message'])->WhereConversation($vendor->id,$dm->id)->first();
        }else if($request->user_id){
            $user = UserInfo::where('user_id', $request->user_id)->first();
            if(!$user){
                $customer = User::find($request->user_id);
                $user = new UserInfo();
                $user->user_id = $customer->id;
                $user->f_name = $customer->f_name;
                $user->l_name = $customer->l_name;
                $user->phone = $customer->phone;
                $user->email = $customer->email;
                $user->image = $customer->image;
                $user->save();
            }
            $conversation = Conversation::with(['sender','receiver','last_message'])->WhereConversation($vendor->id,$user->id)->first();
        }


        if($conversation){

            if($conversation->sender_type == 'customer' && $conversation->sender){
                $user = User::find($conversation->sender->user_id);
                $order = Order::where('store_id',$vnd->stores[0]->id)->where('user_id', $user->id)->whereIn('order_status', ['pending','accepted','confirmed','processing','handover','picked_up'])->count();
            }else if($conversation->receiver_type == 'customer'  && $conversation->receiver){
                $user = User::find($conversation->receiver->user_id);
                $order = Order::where('store_id',$vnd->stores[0]->id)->where('user_id', $user->id)->whereIn('order_status', ['pending','accepted','confirmed','processing','handover','picked_up'])->count();
            }else if($conversation->sender_type == 'delivery_man'&& $conversation->sender){
                $user2 = DeliveryMan::find($conversation->sender->deliveryman_id);
                $order = Order::where('store_id',$vnd->stores[0]->id)->where('delivery_man_id', $user2->id)->whereIn('order_status', ['pending','accepted','confirmed','processing','handover','picked_up'])->count();
            }else if($conversation->receiver_type == 'delivery_man' && $conversation->receiver){
                $user2 = DeliveryMan::find($conversation->receiver->deliveryman_id);
                $order = Order::where('store_id',$vnd->stores[0]->id)->where('delivery_man_id', $user2->id)->whereIn('order_status', ['pending','accepted','confirmed','processing','handover','picked_up'])->count();
            }
            else{
                $order=0;
            }

            $lastmessage = $conversation->last_message;
            if($lastmessage && $lastmessage->sender_id != $vendor->id ) {
                $conversation->unread_message_count = 0;
                $conversation->save();
            }
            Message::where(['conversation_id' => $conversation->id])->where('sender_id','!=', $vendor->id)->update(['is_seen' => 1]);
            $messages = Message::where(['conversation_id' => $conversation->id])->latest()->paginate($limit, ['*'], 'page', $offset);
        }else{
            $messages =[];
            $order=0;
        }


        $data =  [
            'total_size' => $messages? intval($messages->total()):0,
            'limit' => intval($limit),
            'offset' => intval($offset),
            'status' => ($order>0)?true:false,
            'messages' => $messages? $messages->items():[],
            'conversation' => $conversation
        ];
        return response()->json($data, 200);
    }
}
