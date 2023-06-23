<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewsController extends Controller
{
    public function list(){
        $reviews=Review::with(['item','customer'])->latest()->paginate(config('default_pagination'));
        return view('admin-views.reviews.list',compact('reviews'));
    }

    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $foods=Item::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->pluck('id')->toArray();
        $reviews=Review::whereIn('item_id',$foods)->get();
        return response()->json([
            'view'=>view('admin-views.reviews.partials._table',compact('reviews'))->render()
        ]);
    }
}
