@foreach($zones as $key=>$zone)
<tr>
    <td>{{$key+1}}</td>
    <td>{{$zone->id}}</td>
    <td>
    <span class="d-block font-size-sm text-body">
        {{$zone['name']}}
    </span>
    </td>
    <td>{{$zone->stores_count}}</td>
    <td>{{$zone->deliverymen_count}}</td>
    <td>
        <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$zone->id}}">
            <input type="checkbox" onclick="status_form_alert('status-{{$zone['id']}}','{{ translate('Want to change status for this zone ?') }}', event)" class="toggle-switch-input" id="stocksCheckbox{{$zone->id}}" {{$zone->status?'checked':''}}>
            <span class="toggle-switch-label">
                <span class="toggle-switch-indicator"></span>
            </span>
        </label>
        <form action="{{route('admin.zone.status',[$zone['id'],$zone->status?0:1])}}" method="get" id="status-{{$zone['id']}}">
        </form>
    </td>
    <td>
        <div class="btn--container justify-content-center">
            <a class="btn action-btn btn--primary btn-outline-primary"
                href="{{route('admin.zone.edit',[$zone['id']])}}" title="{{translate('messages.edit')}} {{translate('messages.zone')}}"><i class="tio-edit"></i>
            </a>
            <a class="btn action-btn btn--warning btn-outline-warning" title="Module Setup"
            href="{{route('admin.zone.module-setup',[$zone['id']])}}"><i class="tio-settings"></i>
        </a>
            <a class="btn action-btn btn--danger btn-outline-danger" href="javascript:"
            onclick="form_alert('zone-{{$zone['id']}}','{{ translate('Want to delete this zone ?') }}')" title="{{translate('messages.delete')}} {{translate('messages.zone')}}"><i class="tio-delete-outlined"></i>
            </a>
            <form action="{{route('admin.zone.delete',[$zone['id']])}}" method="post" id="zone-{{$zone['id']}}">
                @csrf @method('delete')
            </form>
        </div>
    </td>
</tr>
@endforeach
