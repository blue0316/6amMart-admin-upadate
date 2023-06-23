@foreach($modules as $key=>$module)
<tr>
    <td class="pl-4">{{$key+1}}</td>
    <td>{{$module->id}}</td>
    <td>
        <span class="d-block font-size-sm text-body">
            {{Str::limit($module['module_name'], 20,'...')}}
        </span>
    </td>
    <td>
        <span class="d-block font-size-sm text-body text-capitalize">
            {{Str::limit($module['module_type'], 20,'...')}}
        </span>
    </td>
    <td>
        <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$module->id}}">
        <input type="checkbox" onclick="location.href='{{route('admin.business-settings.module.status',[$module['id'],$module->status?0:1])}}'"class="toggle-switch-input" id="stocksCheckbox{{$module->id}}" {{$module->status?'checked':''}}>
            <span class="toggle-switch-label">
                <span class="toggle-switch-indicator"></span>
            </span>
        </label>
    </td>
    <td class="text-center">{{$module->stores_count}}</td>
    <td>
        <div class="btn--container justify-content-center">
            <a class="btn action-btn btn--primary btn-outline-primary"
                href="{{route('admin.business-settings.module.edit',[$module['id']])}}" title="{{translate('messages.edit')}} {{translate('messages.category')}}"><i class="tio-edit"></i>
            </a>
        </div>
    </td>
</tr>
@endforeach
