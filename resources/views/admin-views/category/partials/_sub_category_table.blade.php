@foreach($categories as $key=>$category)
    <tr>
        <td>{{$key+1}}</td>
        <td>{{$category->id}}</td>
        <td>
            <span class="d-block font-size-sm text-body">
                {{Str::limit($category->parent['name'],20,'...')}}
            </span>
        </td>
        <td class="text-center">
            <span class="d-block font-size-sm text-body">
                {{Str::limit($category->name,20,'...')}}
            </span>
        </td>
        <td>
            <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$category->id}}">
            <input type="checkbox" onclick="location.href='{{route('admin.category.status',[$category['id'],$category->status?0:1])}}'"class="toggle-switch-input" id="stocksCheckbox{{$category->id}}" {{$category->status?'checked':''}}>
                <span class="toggle-switch-label mx-auto">
                    <span class="toggle-switch-indicator"></span>
                </span>
            </label>
        </td>
        <td>
            <form action="{{route('admin.category.priority',$category->id)}}">
                <select name="priority" id="priority" onchange="this.form.submit()" class="form-control form--control-select mx-auto {{$category->priority == 0 ? 'text-title':''}} {{$category->priority == 1 ? 'text-info':''}} {{$category->priority == 2 ? 'text-success':''}}">
                    <option value="0" {{$category->priority == 0?'selected':''}}>{{translate('messages.normal')}}</option>
                    <option value="1" {{$category->priority == 1?'selected':''}}>{{translate('messages.medium')}}</option>
                    <option value="2" {{$category->priority == 2?'selected':''}}>{{translate('messages.high')}}</option>
                </select>
            </form>
        </td>
        <td>
            <div class="btn--container justify-content-center">
                <a class="btn action-btn btn--primary btn-outline-primary"
                    href="{{route('admin.category.edit',[$category['id']])}}" title="{{translate('messages.edit')}} {{translate('messages.category')}}"><i class="tio-edit"></i>
                </a>
                <a class="btn action-btn btn--danger btn-outline-danger" href="javascript:"
                onclick="form_alert('category-{{$category['id']}}','{{ translate('messages.Want to delete this category') }}')" title="{{translate('messages.delete')}} {{translate('messages.category')}}"><i class="tio-delete-outlined"></i>
                </a>
                <form action="{{route('admin.category.delete',[$category['id']])}}" method="post" id="category-{{$category['id']}}">
                    @csrf @method('delete')
                </form>
            </div>
        </td>
    </tr>
@endforeach
