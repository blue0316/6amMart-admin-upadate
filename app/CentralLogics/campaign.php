<?php

namespace App\CentralLogics;

use App\Models\Campaign;
use App\CentralLogics\Helpers;

class CampaignLogic
{
    public static function get_basic_campaigns($store_id, $limit = 25, $offset = 1)
    {
        $data = [];
        $paginator=Campaign::with('stores')
        ->when(config('module.current_module_data'), function($query){
            $query->module(config('module.current_module_data')['id']);
        })
        ->latest()->paginate($limit, ['*'], 'page', $offset);
        foreach ($paginator->items() as $item) {
            $store_ids = count($item->stores)?$item->stores->pluck('id')->toArray():[];
            if($item->start_date)
            {
                $item['available_date_starts']=$item->start_date->format('Y-m-d');
                unset($item['start_date']);
            }
            if($item->end_date)
            {
                $item['available_date_ends']=$item->end_date->format('Y-m-d');
                unset($item['end_date']);
            }
            $item['is_joined'] = in_array($store_id, $store_ids)?true:false;
            unset($item['stores']);
            array_push($data, $item);
        }
        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'campaigns' => $data
        ];
    }
}
