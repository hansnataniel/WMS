<?php

/*
	Use Name Space Here
*/
namespace App\Http\Controllers\Front;

/*
	Call Model Here
*/
use App\Models\Setting;
use App\Models\Admingroup;
use App\Models\Que;

use App\Models\Contact;

/*
	Call Mail file & mail facades
*/
use App\Mail\Front\Contactus;

use Illuminate\Support\Facades\Mail;


/*
	Call Another Function you want to use
*/
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Auth;
use Validator;
use Crypt;
use URL;
use Image;
use Session;
use File;


class ContactController extends Controller
{
	/* Get the list of the resource*/
	public function index(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		return view('front.contact_us.index', $data);
	}

	public function store(Request $request)
	{
		$setting = Setting::first();
		$inputs = $request->all();
		$rules = array(
			'name'		=> 'required',
			'email'		=> 'required|email',
			'message'	=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$name = htmlspecialchars($request->input('name'));
			$email = htmlspecialchars($request->input('email'));
			$phone = htmlspecialchars($request->input('phone'));
			$subject = htmlspecialchars($request->input('subject'));
			$whatsapp = htmlspecialchars($request->input('whatsapp'));
			$line = htmlspecialchars($request->input('line'));
			$bbm = htmlspecialchars($request->input('bbm'));
			$message_input = htmlspecialchars($request->input('message'));

			$contact = new Contact;
			$contact->name = $name;
			$contact->email = $email;
			$contact->phone = $phone;
			$contact->subject = $subject;
			$contact->whatsapp = $whatsapp;
			$contact->line = $line;
			$contact->bbm = $bbm;
			$contact->message = $message_input;
			$contact->is_read = false;
			$contact->save();

			Mail::to($setting->receiver_email)
				    ->send(new contactus($contact));
			
			return redirect('contact-us')->with('success-message', 'Your message has been sent.');
		}
		else
		{
			return redirect('contact-us')->withInput()->withErrors($validator);
		}
	}
}