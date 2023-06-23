<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Zone;
use Illuminate\Http\Request;


class ModuleController extends Controller
{

    public function index(Request $request)
    {
        if ($request->hasHeader('zoneId')) {
            $zone_id=$request->header('zoneId');
            $modules = Module::with('zones')->whereHas('zones',function($query) use ($zone_id){
                $query->whereIn('zone_id',json_decode($zone_id, true));
            })->active()->get();
        }else{
            $modules = Module::when($request->zone_id, function($query)use($request){
                $query->whereHas('zones',function($query) use ($request){
                    $query->where('zone_id',$request->zone_id);
                })->notParcel();
            })->active()->get();
        }

        $modules = array_map(function($item){
            if(count($item['translations'])>0)
            {
                foreach($item['translations'] as $translation){
                    if($translation['key']=='module_name')
                    {
                        $item['module_name'] = $translation['value'];
                    }

                    if($translation['key']=='description')
                    {
                        $item['description'] = $translation['value'];
                    }
                }

            }
            return $item;
        },$modules->toArray());
        return response()->json($modules);
    }

}
