<?php

namespace App\Http\Controllers\Vendor;

use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\Review;
use App\Http\Controllers\Controller;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::whereHas('item', function($query){
            return $query->where('store_id', Helpers::get_store_id());
        })->latest()->paginate(config('default_pagination'));
        return view('vendor-views.review.index', compact('reviews'));
    }
}
