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

use App\Models\News;


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


class NewsController extends Controller
{
	/* Get the list of the resource*/
	public function index(Request $request)
	{
		$newses = News::where('is_active', '=', 1)->orderBy('id', 'desc')->paginate(7);
		$data['newses'] = $newses;

		$recent_newses = News::where('is_active', '=', 1)->orderBy('id', 'desc')->take(15)->get();
		$data['recent_newses'] = $recent_newses;

		return view('front.news.index', $data);
	}

	public function getSearch(Request $request)
	{
		$query = News::query();

		$data['criteria'] = '';

		$title = htmlspecialchars($request->input('src_title'));
		if ($title != null)
		{
			$query->where('title', 'LIKE', '%' . $title . '%');
			$data['criteria']['src_title'] = $title;
		}
		$data['src_title'] = $title;

		$query->orderBy('created_at', 'asc');

		$newses = $query->paginate(7);
		$data['newses'] = $newses;

		$recent_newses = News::where('is_active', '=', 1)->orderBy('id', 'desc')->take(15)->get();
		$data['recent_newses'] = $recent_newses;

		return view('front.news.search', $data);
	}

	public function getDetail(Request $request, $id, $name)
	{
		$news = News::find($id);
		$data['news'] = $news;

		$recent_newses = News::where('is_active', '=', 1)->orderBy('id', 'desc')->take(15)->get();
		$data['recent_newses'] = $recent_newses;

		return view('front.news.detail', $data);
	}

	public function getNewsletterUnsubscribe(Request $request, $id)
	{
		$setting = Setting::first();
		$setting_name = $setting->name;

		$newsletter = Newsletter::find(Crypt::decrypt($id));
		if($newsletter != null)
		{
			$newsletter->delete();
		}

		return redirect('/')->with('success-message', "Successfully unsubscribe in " . $setting_name . ".");
	}
}