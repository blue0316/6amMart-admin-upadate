@foreach($delivery_men as $key=>$dm)
<tr>
    <td>{{$key+1}}</td>
    <td>
        <a class="media align-items-center" href="{{route('vendor.delivery-man.preview',[$dm['id']])}}">
            <img class="avatar avatar-lg mr-3" onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'"
                    src="{{asset('storage/app/public/delivery-man')}}/{{$dm['image']}}" alt="{{$dm['f_name']}} {{$dm['l_name']}}">
            <div class="media-body">
                <h5 class="text-hover-primary mb-0">{{$dm['f_name'].' '.$dm['l_name']}}</h5>
                <span class="rating">
                    <i class="tio-star"></i> {{count($dm->rating)>0?number_format($dm->rating[0]->average, 1, '.', ' '):0}}
                </span>
            </div>
        </a>
    </td>
    <td>
        <div>
            {{translate('messages.currently_assigned_orders')}} : {{$dm->current_orders}}
        </div>
        <div>
            {{translate('messages.active_status')}} :
            @if($dm->application_status == 'approved')
                @if($dm->active)
                <strong class="text-capitalize text-success">{{translate('messages.online')}}</strong>
                @else
                <strong class="text-capitalize text-danger">{{translate('messages.offline')}}</strong>
                @endif
            @elseif ($dm->application_status == 'denied')
                <strong class="text-capitalize text-danger">{{translate('messages.denied')}}</strong>
            @else
                <strong class="text-capitalize text-primary">{{translate('messages.pending')}}</strong>
            @endif
        </div>
    </td>
    <td>
        <a class="deco-none" href="tel:{{$dm['phone']}}">{{$dm['phone']}}</a>
    </td>
    <td class="text-center">
        {{ $dm->orders ? count($dm->orders):0 }}
    </td>
    <td>
        <div class="btn--container justify-content-center">
            <a class="btn action-btn btn--primary btn-outline-primary" href="{{route('vendor.delivery-man.edit',[$dm['id']])}}" title="{{translate('messages.edit')}}"><i class="tio-edit"></i>
            </a>
            <a class="btn action-btn btn--danger btn-outline-danger" href="javascript:" onclick="form_alert('delivery-man-{{$dm['id']}}','Want to remove this deliveryman ?')" title="{{translate('messages.delete')}}"><i class="tio-delete-outlined"></i>
            </a>
        </div>
        <form action="{{route('vendor.delivery-man.delete',[$dm['id']])}}" method="post" id="delivery-man-{{$dm['id']}}">
            @csrf @method('delete')
        </form>
    </td>
</tr>
@endforeach
