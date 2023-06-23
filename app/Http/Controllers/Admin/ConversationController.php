<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\CentralLogics\Helpers;
use App\Models\Conversation;
use App\Models\UserInfo;
use App\Models\Message;
use App\Models\User;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConversationController extends Controller
{
    public function list(Request $request)
    {
        $conversations = Conversation::with(['sender', 'receiver', 'last_message'])->WhereUserType('admin');
        if($request->query('key')) {
            $key = explode(' ', $request->get('key'));
            $conversations = $conversations->where(function($qu)use($key){
                    $qu->whereHas('sender',function($query)use($key){
                    foreach ($key as $value) {
                        $query->where('f_name', 'like', "%{$value}%")->orWhere('l_name', 'like', "%{$value}%")->orWhere('phone', 'like', "%{$value}%");
                    }
                })
                ->orWhereHas('receiver',function($query1)use($key){
                    foreach ($key as $value) {
                        $query1->where('f_name', 'like', "%{$value}%")->orWhere('l_name', 'like', "%{$value}%")->orWhere('phone', 'like', "%{$value}%");
                    }
                });
            });
        }
        $conversations = $conversations->orderBy('last_message_time', 'DESC')
        ->paginate(8);

        if ($request->ajax()) {
            $view = view('admin-views.messages.data',compact('conversations'))->render();
            return response()->json(['html'=>$view]);
        }

        return view('admin-views.messages.index', compact('conversations'));
    }

    public function view($conversation_id,$user_id)
    {
        $conversation = Conversation::find($conversation_id);
        $lastmessage = $conversation->last_message;
        if($lastmessage && $lastmessage->sender_id == $user_id ) {
            $conversation->unread_message_count = 0;
            $conversation->save();
        }
        Message::where(['conversation_id' => $conversation->id])->where('sender_id',$user_id)->update(['is_seen' => 1]);
        $convs = Message::where(['conversation_id' => $conversation_id])->get();
        $receiver = UserInfo::find($user_id);
        // $user = User::find($receiver->user_id);
        $user = $receiver;
        return response()->json([
            'view' => view('admin-views.messages.partials._conversations', compact('convs', 'user', 'receiver'))->render()
        ]);
    }

    public function store(Request $request, $user_id)
    {


        if ($request->has('images')) {
            $image_name=[];
            foreach($request->images as $key=>$img)
            {
                $name = Helpers::upload('conversation/', 'png', $img);
                array_push($image_name,$name);
            }
        } else {
            $image_name = null;
            $validator = Validator::make($request->all(), [
                'reply' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)]);
            }
        }


        $admin = Admin::find(auth('admin')->id());
        $sender = UserInfo::where('admin_id',$admin->id)->first();
        if(!$sender){
            $sender = new UserInfo();
            $sender->admin_id = $admin->id;
            $sender->f_name = $admin->f_name;
            $sender->l_name = $admin->l_name;
            $sender->phone = $admin->phone;
            $sender->email = $admin->email;
            $sender->image = $admin->image;
            $sender->save();
        }

        $user = User::find($user_id);
        $fcm_token=$user->cm_firebase_token;
        $receiver = UserInfo::where('user_id', $user->id)->first();
        $user = $receiver;
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

        $conversation = Conversation::whereConversation($receiver->id,0)->first();


        if(!$conversation){
            $conversation = new Conversation;
            $conversation->sender_id = 0;
            $conversation->sender_type = 'admin';
            $conversation->receiver_id = $receiver->id;
            $conversation->receiver_type = 'user';
            $conversation->last_message_time = Carbon::now()->toDateTimeString();
            $conversation->save();

            $conversation= Conversation::find($conversation->id);
        }

        $message = new Message();
        $message->conversation_id = $conversation->id;
        $message->sender_id = $sender->id;
        $message->message = $request->reply;
        $message->file = $image_name?json_encode($image_name, JSON_UNESCAPED_SLASHES):null;
        try {
            if($message->save())
            $conversation->unread_message_count = $conversation->unread_message_count? $conversation->unread_message_count+1:1;
            $conversation->last_message_id=$message->id;
            $conversation->last_message_time = Carbon::now()->toDateTimeString();
            $conversation->save();
            {
                $data = [
                    'title' =>translate('messages.message'),
                    'description' =>translate('messages.message_description'),
                    'order_id' => '',
                    'image' => '',
                    'message' => json_encode($message),
                    'type'=> 'message',
                    'conversation_id'=> $conversation->id,
                    'sender_type'=> 'admin'
                ];
                Helpers::send_push_notif_to_device($fcm_token, $data);
            }

        } catch (\Exception $e) {
            info($e);
        }

        $convs = Message::where(['conversation_id' => $conversation->id])->get();
        return response()->json([
            'view' => view('admin-views.messages.partials._conversations', compact('convs', 'user', 'receiver'))->render()
        ]);
    }
}
