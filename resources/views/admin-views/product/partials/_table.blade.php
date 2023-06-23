@foreach($items as $key=>$item)
    <tr>
        <td>{{$key+1}}</td>
        <td>
            <a class="media align-items-center" href="{{route('admin.item.view',[$item['id']])}}">
                <img class="avatar avatar-lg mr-3" src="{{asset('storage/app/public/product')}}/{{$item['image']}}" 
                        onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'" alt="{{$item->name}} image">
                <div class="media-body">
                    <h5 class="text-hover-primary mb-0">{{Str::limit($item['name'],20,'...')}}</h5>
                </div>
            </a>
        </td>
        <td>
        {{Str::limit($item->category?$item->category->name:translate('messages.category_deleted'),20,'...')}}
        </td>
        <td>
        {{Str::limit($item->store?$item->store->name:translate('messages.store deleted!'), 20, '...')}}
        </td>
        <td>
            <div class="text-right mw--85px">
                {{\App\CentralLogics\Helpers::format_currency($item['price'])}}
            </div>
        </td>
        <td>
            <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$item->id}}">
                <input type="checkbox" onclick="location.href='{{route('admin.item.status',[$item['id'],$item->status?0:1])}}'"class="toggle-switch-input" id="stocksCheckbox{{$item->id}}" {{$item->status?'checked':''}}>
                <span class="toggle-switch-label mx-auto">
                    <span class="toggle-switch-indicator"></span>
                </span>
            </label>
        </td>
        <td>
            <div class="btn--container justify-content-center">
                <a class="btn action-btn btn--primary btn-outline-primary"
                    href="{{route('admin.item.edit',[$item['id']])}}" title="{{translate('messages.edit')}} {{translate('messages.item')}}"><i class="tio-edit"></i>
                </a>
                <a class="btn  action-btn btn--danger btn-outline-danger" href="javascript:"
                    onclick="form_alert('food-{{$item['id']}}','{{translate('messages.Want_to_delete_this_item')}}')" title="{{translate('messages.delete')}} {{translate('messages.item')}}"><i class="tio-delete-outlined"></i>
                </a>
                <form action="{{route('admin.item.delete',[$item['id']])}}"
                        method="post" id="food-{{$item['id']}}">
                    @csrf @method('delete')
                </form>
            </div>
        </td>
    </tr>
@endforeach
