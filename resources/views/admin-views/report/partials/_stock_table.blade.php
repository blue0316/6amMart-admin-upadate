@foreach($items as $key=>$item)
<tr>
    <td>{{$key+1}}</td>
    <td>
        <a class="media align-items-center" href="{{route('admin.item.view',[$item['id'],'module_id'=>$item['module_id']])}}">
            <img class="avatar avatar-lg mr-3" src="{{asset('storage/app/public/product')}}/{{$item['image']}}" onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'" alt="{{$item->name}} image">
            <div class="media-body">
                <h5 class="text-hover-primary mb-0 max-width-200px word-break line--limit-2">{{$item['name']}}</h5>
            </div>
        </a>
    </td>
    <td>
        @if($item->store)
        {{Str::limit($item->store->name,25,'...')}}
        @else
        {{translate('messages.store')}} {{translate('messages.deleted')}}
        @endif
    </td>
    <td>
        @if($item->store)
        {{$item->store->zone->name}}
        @else
        {{translate('messages.not_found')}}
        @endif
    </td>
    <td>
        {{$item->stock}}
    </td>
    <td>
        <a class="btn action-btn btn--primary btn-outline-primary" href="javascript:" title="{{translate('messages.edit')}} {{translate('messages.quantity')}}" onclick="update_quantity({{ $item->id }})" data-toggle="modal" data-target="#update-quantity"><i class="tio-edit"></i>
        </a>
    </td>
</tr>
@endforeach
