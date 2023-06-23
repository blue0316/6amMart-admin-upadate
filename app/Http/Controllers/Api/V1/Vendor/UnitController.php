<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Unit;

class UnitController extends Controller
{

    public function index()
    {
        return response()->json(Unit::all());
    }
}
