<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;


class AttributeController extends Controller
{
    function list(Request $request)
    {
        $attributes = Attribute::orderBy('name')->get();
        return response()->json($attributes,200);
    }
}
