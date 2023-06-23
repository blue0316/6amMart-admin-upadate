<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    // Save newsLetterSubscribe email
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'=>'required|email|unique:newsletters,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        Newsletter::create(['email'=>$request->email]);

        return response()->json(['message'=>translate('messages.subscription_successful')],200);

    }


}
