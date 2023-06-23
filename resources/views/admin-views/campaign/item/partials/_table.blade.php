@foreach($campaigns as $key=>$campaign)
    <tr>
        <td>{{$key+1}}</td>
        <td>
            <a href="{{route('admin.campaign.view',['item',$campaign->id])}}" class="d-block text-body">{{Str::limit($campaign['title'],25,'...')}}</a>
        </td>
        <td>
            <span class="bg-gradient-light text-dark">{{$campaign->start_date?$campaign->start_date->format('d/M/Y'). ' - ' .$campaign->end_date->format('d/M/Y'): 'N/A'}}</span>
        </td>
        <td>
            <span class="bg-gradient-light text-dark">{{$campaign->start_time?$campaign->start_time->format(config('timeformat')). ' - ' .$campaign->end_time->format(config('timeformat')): 'N/A'}}</span>
        </td>
        <td>{{$campaign->price}}</td>
        <td>
            <div class="d-flex flex-wrap justify-content-center">
                <label class="toggle-switch toggle-switch-sm" for="campaignCheckbox{{$campaign->id}}">
                    <input type="checkbox" onclick="location.href='{{route('admin.campaign.status',['item',$campaign['id'],$campaign->status?0:1])}}'"class="toggle-switch-input" id="campaignCheckbox{{$campaign->id}}" {{$campaign->status?'checked':''}}>
                    <span class="toggle-switch-label">
                        <span class="toggle-switch-indicator"></span>
                    </span>
                </label>
            </div>
        </td>
        <td>
            <div class="btn--container justify-content-center">
                <a class="btn action-btn btn--primary btn-outline-primary"
                    href="{{route('admin.campaign.edit',['item',$campaign['id']])}}" title="{{translate('messages.edit')}} {{translate('messages.campaign')}}"><i class="tio-edit"></i>
                </a>
                <a class="btn action-btn btn--danger btn-outline-danger" href="javascript:"
                    onclick="form_alert('campaign-{{$campaign['id']}}','Want to delete this item ?')" title="{{translate('messages.delete')}} {{translate('messages.campaign')}}"><i class="tio-delete-outlined"></i>
                </a>
                <form action="{{route('admin.campaign.delete-item',[$campaign['id']])}}"
                            method="post" id="campaign-{{$campaign['id']}}">
                    @csrf @method('delete')
                </form>
            </div>
        </td>
    </tr>
@endforeach