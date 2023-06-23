<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use Brian2694\Toastr\Facades\Toastr;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        /*$this->middleware('auth');*/
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // return redirect()->route('admin.auth.login');
        return view('home');
    }

    public function terms_and_conditions(Request $request)
    {
        $data = self::get_settings('terms_and_conditions');
        if ($request->expectsJson()) {
            return response()->json($data);
        }
        return view('terms-and-conditions', compact('data'));
    }

    public function about_us(Request $request)
    {
        $data = self::get_settings('about_us');
        if ($request->expectsJson()) {
            return response()->json($data);
        }
        return view('about-us', compact('data'));
    }

    public function contact_us()
    {
        return view('contact-us');
    }

    public function send_message(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'subject' => 'required',
            'message' => 'required',
        ]);

        $contact = new Contact;
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->subject = $request->subject;
        $contact->message = $request->message;
        $contact->save();

        Toastr::success('Message sent successfully!');
        return back();
    }

    public function privacy_policy(Request $request)
    {
        $data = self::get_settings('privacy_policy');
        if ($request->expectsJson()) {
            return response()->json($data);
        }
        return view('privacy-policy', compact('data'));
    }
    public function refund(Request $request)
    {
        $data = self::get_settings('refund');
        if ($request->expectsJson()) {
            return response()->json($data);
        }
        return view('refund', compact('data'));
    }
    public function shipping_policy(Request $request)
    {
        $data = self::get_settings('shipping_policy');
        if ($request->expectsJson()) {
            return response()->json($data);
        }
        return view('shipping-policy', compact('data'));
    }
    public function cancelation(Request $request)
    {
        $data = self::get_settings('cancelation');
        if ($request->expectsJson()) {
            return response()->json($data);
        }
        return view('cancelation', compact('data'));
    }

    public static function get_settings($name)
    {
        $config = null;
        $data = BusinessSetting::where(['key' => $name])->first();
        if (isset($data)) {
            $config = json_decode($data['value'], true);
            if (is_null($config)) {
                $config = $data['value'];
            }
        }
        return $config;
    }
}
