<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Zone;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    public function get_zones(Request $request)
    {
        return response()->json(Zone::where('status',1)->get(), 200);
    }
}
