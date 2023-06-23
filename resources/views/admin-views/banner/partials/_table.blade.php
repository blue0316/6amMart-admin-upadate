@foreach($banners as $key=>$banner)
    <tr>
        <td>{{$key+1}}</td>
        <td>
            <span class="media align-items-center">
                <img class="img--ratio-3 w-auto h--50px rounded mr-2" src="{{asset('storage/app/public/banner')}}/{{$banner['image']}}" 
                        onerror="this.src='{{asset('/public/assets/admin/img/900x400/img1.jpg')}}'" alt="{{$banner->name}} image">
                <div class="media-body">
                    <h5 class="text-hover-primary mb-0">{{Str::limit($banner['title'], 25, '...')}}</h5>
                </div>
            </span>
        <span class="d-block font-size-sm text-body">
            
        </span>
        </td>
        <td>{{Str::limit($banner->module->module_name, 15, '...')}}</td>
        <td>{{translate('messages.'.$banner['type'])}}</td>
        <td>
            <div class="d-flex justify-content-center">
                <label class="toggle-switch toggle-switch-sm" for="featuredCheckbox{{$banner->id}}">
                    <input type="checkbox" onclick="location.href='{{route('admin.banner.featured',[$banner['id'],$banner->featured?0:1])}}'" class="toggle-switch-input" id="featuredCheckbox{{$banner->id}}" {{$banner->featured?'checked':''}}>
                    <span class="toggle-switch-label">
                        <span class="toggle-switch-indicator"></span>
                    </span>
                </label>
            </div>
        </td>
        <td>
            <div class="d-flex justify-content-center">
                <label class="toggle-switch toggle-switch-sm" for="statusCheckbox{{$banner->id}}">
                <input type="checkbox" onclick="location.href='{{route('admin.banner.status',[$banner['id'],$banner->status?0:1])}}'" class="toggle-switch-input" id="statusCheckbox{{$banner->id}}" {{$banner->status?'checked':''}}>
                <span class="toggle-switch-label">
                    <span class="toggle-switch-indicator"></span>
                </span>
            </label>
            </div>
        </td>
        <td>
            <div class="btn--container justify-content-center">
                <a class="btn action-btn btn--primary btn-outline-primary" href="{{route('admin.banner.edit',[$banner['id']])}}"title="{{translate('messages.edit')}} {{translate('messages.banner')}}"><i class="tio-edit"></i>
                </a>
                <a class="btn action-btn btn--danger btn-outline-danger" href="javascript:" onclick="form_alert('banner-{{$banner['id']}}','Want to delete this banner ?')" title="{{translate('messages.delete')}} {{translate('messages.banner')}}"><i class="tio-delete-outlined"></i>
                </a>
                <form action="{{route('admin.banner.delete',[$banner['id']])}}"
                            method="post" id="banner-{{$banner['id']}}">
                        @csrf @method('delete')
                </form>
            </div>
        </td>
    </tr>
@endforeach