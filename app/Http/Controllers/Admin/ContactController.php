<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Contact;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required',
            'subject' => 'required',
            'message' => 'required',
            'email' => 'required|email:rfc,dns'
        ], [
            'mobile_number.required' => 'Mobile Number is Empty!',
            'subject.required' => ' Subject is Empty!',
            'message.required' => 'Message is Empty!',

        ]);
        $contact = new Contact;
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->mobile_number = $request->mobile_number;
        $contact->subject = $request->subject;
        $contact->message = $request->message;
        $contact->save();

        return response()->json(['success' => 'Your Message Send Successfully']);
    }

    public function list(Request $request)
    {
        $contacts = Contact::orderBy('name')->paginate(config('default_pagination'));
        return view('admin-views.contacts.list', compact('contacts'));

    }

    public function view($id)
    {
        $contact = Contact::findOrFail($id);
        return view('admin-views.contacts.view', compact('contact'));
    }

    public function update(Request $request, $id)
    {
        $contact = Contact::find($id);
        $contact->feedback = $request->feedback;
        $contact->seen = 1;
        $contact->update();
        Toastr::success('Feedback  Update successfully!');
        return redirect()->route('admin.contact.contact-list');
    }

    public function destroy(Request $request)
    {
        $contact = Contact::findOrFail($request->id);
        $contact->delete();
        Toastr::success(translate('messages.contact_deleted_successfully'));
        return back();
    }

    public function send_mail(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);
        $data = array('body' => $request['mail_body'], 'name' => $contact->name);
        try {
            Mail::send('email-templates.customer-message', $data, function ($message) use ($contact, $request) {
                $message->to($contact['email'], BusinessSetting::where(['key' => 'business_name'])->first()->value)
                    ->subject($request['subject']);
            });

            Contact::where(['id' => $id])->update([
                'reply' => json_encode([
                    'subject' => $request['subject'],
                    'body' => $request['mail_body']
                ]),
                'seen'=>1
            ]);
        } catch (\Exception $exception) {
            Toastr::error(translate('Invalied Email Id!'));
            return back();
        }

        Toastr::success('Mail sent successfully!');
        return back();
    }

    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $contacts=Contact::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%")
                    ->orWhere('email', 'like', "%{$value}%");
            }
        })->limit(50)->get();
        return response()->json([
            'view'=>view('admin-views.contacts.partials._table',compact('contacts'))->render(),
            'count'=>$contacts->count()
        ]);
    }
}
