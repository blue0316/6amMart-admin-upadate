@foreach($items as $key=>$item)
<tr>
    <td>{{$key+1}}</td>
    <td>
        <a class="media align-items-center" href="{{route('admin.item.view',[$item['id'],'module_id'=>$item['module_id']])}}">
            <img class="avatar avatar-lg mr-3" src="{{asset('storage/app/public/product')}}/{{$item['image']}}"
                    onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'" alt="{{$item->name}} image">
            <div class="media-body">
                <h5 class="text-hover-primary mb-0">{{$item['name']}}</h5>
            </div>
        </a>
    </td>
    <td>
        {{ $item->module->module_name }}
    </td>
    <td>
        @if($item->store)
        {{Str::limit($item->store->name,25,'...')}}
        @else
        {{translate('messages.store')}} {{translate('messages.deleted')}}
        @endif
    </td>
    <td>
        {{$item->orders_count}}
    </td>
    <td>
        {{ \App\CentralLogics\Helpers::format_currency($item->price) }}
    </td>
    <td>
        {{ \App\CentralLogics\Helpers::format_currency($item->orders_sum_price) }}
    </td>
    <td>
        {{ \App\CentralLogics\Helpers::format_currency($item->orders_sum_discount_on_item) }}
    </td>
    <td>
        {{ $item->orders_count>0? \App\CentralLogics\Helpers::format_currency(($item->orders_sum_price-$item->orders_sum_discount_on_item)/$item->orders_count):0 }}
    </td>
    <td>
        <div class="rating">
            <span><i class="tio-star"></i></span>{{ $item->avg_rating }} ({{ $item->rating_count }})
        </div>
    </td>
</tr>
@endforeach