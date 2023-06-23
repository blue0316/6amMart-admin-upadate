<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Brian2694\Toastr\Facades\Toastr;


class BannerController extends Controller
{
    function list()
    {
        $banners=Banner::latest()->paginate(config('default_pagination'));
        return view('vendor-views.banner.list',compact('banners'));
    }


    public function status(Request $request)
    {
        $banner = Banner::findOrFail($request->id);
        $store_id = $request->status;
        $store_ids = json_decode($banner->restaurant_ids);
        if(in_array($store_id, $store_ids))
        {
            unset($store_ids[array_search($store_id, $store_ids)]);
        }
        else
        {
            array_push($store_ids, $store_id);
        }

        $banner->restaurant_ids = json_encode($store_ids);
        $banner->save();
        Toastr::success(translate('messages.capmaign_participation_updated'));
        return back();
    }

}
