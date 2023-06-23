@foreach($reviews as $key=>$review)
@if(isset($review->delivery_man))
    <tr>
        <td>{{$key+1}}</td>
        <td>
        <span class="d-block font-size-sm text-body">
            <a href="{{route('admin.users.delivery-man.preview',[$review['delivery_man_id']])}}">
                {{$review->delivery_man->f_name.' '.$review->delivery_man->l_name}}
            </a>
        </span>
        </td>
        <td>
            @if ($review->customer)
            <a href="{{route('admin.users.customer.view',[$review->user_id])}}">
                {{$review->customer?$review->customer->f_name:""}} {{$review->customer?$review->customer->l_name:""}}
            </a>
            @else
                {{translate('messages.customer_not_found')}}
            @endif

        </td>
        <td>
            {{$review->comment}}
        </td>
        <td>
            <label class="rating">
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
            <form action="{{route('admin.users.delivery-man.reviews.status',[$review['id'],$review->status?0:1])}}" method="get" id="status-{{$review['id']}}">
            </form>
        </td>
    </tr>
@endif
@endforeach
