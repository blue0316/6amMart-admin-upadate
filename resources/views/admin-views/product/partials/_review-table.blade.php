@foreach($reviews as $key=>$review)
<tr>
    <td>{{$key+1}}</td>
    <td>
        @if ($review->item)
            <a class="media align-items-center" href="{{route('admin.item.view',[$review->item['id']])}}">
                <img class="avatar avatar-lg mr-3" src="{{asset('storage/app/public/product')}}/{{$review->item['image']}}"
                    onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'" alt="{{$review->item->name}} image">
                <div class="media-body">
                    <h5 class="text-hover-primary mb-0">{{Str::limit($review->item['name'],20,'...')}}</h5>
                </div>
            </a>
            <span class="ml-10"><a href="{{route('admin.order.details',['id'=>$review->order_id])}}">{{ translate('messages.order_id') }}: {{$review->order_id}}</a></span>
        @else
            {{translate('messages.Item deleted!')}}
        @endif

    </td>
    <td>
        <a href="{{route('admin.customer.view',[$review->user_id])}}">
            {{$review->customer?$review->customer->f_name:""}} {{$review->customer?$review->customer->l_name:""}}
        </a>
    </td>
    <td>
        <p class="text-wrap">{{$review->comment}}</p>
    </td>
    <td>
        <label class="badge badge-soft-info">
            {{$review->rating}} <i class="tio-star"></i>
        </label>
    </td>
    <td>
        <label class="toggle-switch toggle-switch-sm" for="reviewCheckbox{{$review->id}}">
            <input type="checkbox" onclick="status_form_alert('status-{{$review['id']}}','{{$review->status?translate('messages.you_want_to_hide_this_review_for_customer'):translate('messages.you_want_to_show_this_review_for_customer')}}', event)" class="toggle-switch-input" id="reviewCheckbox{{$review->id}}" {{$review->status?'checked':''}}>
            <span class="toggle-switch-label">
                <span class="toggle-switch-indicator"></span>
            </span>
        </label>
        <form action="{{route('admin.item.reviews.status',[$review['id'],$review->status?0:1])}}" method="get" id="status-{{$review['id']}}">
        </form>
    </td>
</tr>
@endforeach
