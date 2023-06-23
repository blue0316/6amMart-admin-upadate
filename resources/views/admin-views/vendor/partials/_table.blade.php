@foreach($stores as $key=>$store)
<tr>
    <td>{{$key+1}}</td>
    <td>
        <div>
            <a href="{{route('admin.store.view', $store->id)}}" class="table-rest-info" alt="view store">
            <img class="img--60 circle" onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'"
                    src="{{asset('storage/app/public/store')}}/{{$store['logo']}}">
                <div class="info"><div class="text--title">
                    {{Str::limit($store->name,20,'...')}}
                    </div>
                    <div class="font-light">
                        {{translate('messages.id')}}:{{$store->id}}
                    </div>
                </div>
            </a>
        </div>
    </td>
    <td>
        <span class="d-block font-size-sm text-body">
            {{Str::limit($store->module->module_name,20,'...')}}
        </span>
    </td>
    <td>
        <span class="d-block font-size-sm text-body">
            {{Str::limit($store->vendor->f_name.' '.$store->vendor->l_name,20,'...')}}
        </span>
        <div>
            {{$store['phone']}}
        </div>
    </td>
    <td>
        {{$store->zone?$store->zone->name:translate('messages.zone').' '.translate('messages.deleted')}}
        {{--<span class="d-block font-size-sm">{{$banner['image']}}</span>--}}
    </td>
    <td>
        <label class="toggle-switch toggle-switch-sm" for="featuredCheckbox{{$store->id}}">
            <input type="checkbox" onclick="location.href='{{route('admin.store.featured',[$store->id,$store->featured?0:1])}}'" class="toggle-switch-input" id="featuredCheckbox{{$store->id}}" {{$store->featured?'checked':''}}>
            <span class="toggle-switch-label">
                <span class="toggle-switch-indicator"></span>
            </span>
        </label>
    </td>

    <td>
        @if(isset($store->vendor->status))
            @if($store->vendor->status)
            <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$store->id}}">
                <input type="checkbox" onclick="status_change_alert('{{route('admin.store.status',[$store->id,$store->status?0:1])}}', '{{translate('messages.you_want_to_change_this_store_status')}}', event)" class="toggle-switch-input" id="stocksCheckbox{{$store->id}}" {{$store->status?'checked':''}}>
                <span class="toggle-switch-label">
                    <span class="toggle-switch-indicator"></span>
                </span>
            </label>
            @else
            <span class="badge badge-soft-danger">{{translate('messages.denied')}}</span>
            @endif
        @else
            <span class="badge badge-soft-danger">{{translate('messages.pending')}}</span>
        @endif
    </td>

    <td>
        <div class="btn--container justify-content-center">
            <a class="btn action-btn btn--primary btn-outline-primary"
            href="{{route('admin.store.edit',[$store['id']])}}" title="{{translate('messages.edit')}} {{translate('messages.store')}}"><i class="tio-edit"></i>
            </a>
            <a class="btn action-btn btn--danger btn-outline-danger" href="javascript:"
            onclick="form_alert('vendor-{{$store['id']}}','{{translate('You want to remove this store')}}')" title="{{translate('messages.delete')}} {{translate('messages.store')}}"><i class="tio-delete-outlined"></i>
            </a>
            <form action="{{route('admin.store.delete',[$store['id']])}}" method="post" id="vendor-{{$store['id']}}">
                @csrf @method('delete')
            </form>
        </div>
    </td>
</tr>
@endforeach
