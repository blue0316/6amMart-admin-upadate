@php($data=[])
<?php
foreach ($store->schedules as $schedule)
{
    $data[$schedule->day][]=['id'=>$schedule->id,'start_time'=>$schedule->opening_time, 'end_time'=>$schedule->closing_time];
}
?>
<div class="schedule-item">
    <span class="btn">{{translate('messages.monday')}} :</span>
    <div class="schedult-date-content">
        @if(isset($data['1']) && count($data['1']))
            @foreach ($data['1'] as $day)
            <div class="d-inline-flex align-items-center">
                <span class="start--time">
                    <span class="clock--icon">
                        <i class="tio-time"></i>
                    </span>
                    <span class="info">
                        <span>{{translate('messages.opening_time')}}</span>
                        {{date(config('timeformat'), strtotime($day['start_time']))}}
                    </span>
                </span>
                <span class="end--time">
                    <span class="clock--icon">
                        <i class="tio-time"></i>
                    </span>
                    <span class="info">
                        <span>{{translate('messages.closing_time')}}</span>
                        {{date(config('timeformat'), strtotime($day['end_time']))}}
                    </span>
                </span>
                <span class="dismiss--date" onclick="delete_schedule('{{route('vendor.business-settings.remove-schedule',['store_schedule'=>$day['id']])}}')"><i class="tio-clear-circle-outlined"></i></span></span>
            </div>
            @endforeach
        @else
            <span class="btn btn-sm btn-outline-danger m-1 disabled">{{translate('messages.Offday')}}</span>
        @endif
        <span class="btn add--primary" data-toggle="modal" data-target="#exampleModal" data-dayid="1" data-day="{{translate('messages.monday')}}"><i class="tio-add"></i></span>
    </div>
</div>

<div class="schedule-item">
    <span class="btn">{{translate('messages.tuesday')}} :</span>
    <div class="schedult-date-content">
        @if(isset($data['2']) && count($data['2']))
            @foreach ($data['2'] as $day)
            <div class="d-inline-flex align-items-center">
                <span class="start--time">
                    <span class="clock--icon">
                        <i class="tio-time"></i>
                    </span>
                    <span class="info">
                        <span>{{translate('messages.opening_time')}}</span>
                        {{date(config('timeformat'), strtotime($day['start_time']))}}
                    </span>
                </span>
                <span class="end--time">
                    <span class="clock--icon">
                        <i class="tio-time"></i>
                    </span>
                    <span class="info">
                        <span>{{translate('messages.closing_time')}}</span>
                        {{date(config('timeformat'), strtotime($day['end_time']))}}
                    </span>
                </span>
                <span class="dismiss--date" onclick="delete_schedule('{{route('vendor.business-settings.remove-schedule',['store_schedule'=>$day['id']])}}')"><i class="tio-clear-circle-outlined"></i></span></span>
            </div>
            @endforeach
        @else
            <span class="btn btn-sm btn-outline-danger m-1 disabled">{{translate('messages.Offday')}}</span>
        @endif
        <span class="btn add--primary" data-toggle="modal" data-target="#exampleModal" data-dayid="2" data-day="{{translate('messages.tuesday')}}"><i class="tio-add"></i></span>
    </div>
</div>

<div class="schedule-item">
    <span class="btn">{{translate('messages.wednesday')}} :</span>
    <div class="schedult-date-content">
        @if(isset($data['3']) && count($data['3']))
            @foreach ($data['3'] as $day)
            <div class="d-inline-flex align-items-center">
                <span class="start--time">
                    <span class="clock--icon">
                        <i class="tio-time"></i>
                    </span>
                    <span class="info">
                        <span>{{translate('messages.opening_time')}}</span>
                        {{date(config('timeformat'), strtotime($day['start_time']))}}
                    </span>
                </span>
                <span class="end--time">
                    <span class="clock--icon">
                        <i class="tio-time"></i>
                    </span>
                    <span class="info">
                        <span>{{translate('messages.closing_time')}}</span>
                        {{date(config('timeformat'), strtotime($day['end_time']))}}
                    </span>
                </span>
                <span class="dismiss--date" onclick="delete_schedule('{{route('vendor.business-settings.remove-schedule',['store_schedule'=>$day['id']])}}')"><i class="tio-clear-circle-outlined"></i></span></span>
            </div>
            @endforeach
        @else
            <span class="btn btn-sm btn-outline-danger m-1 disabled">{{translate('messages.Offday')}}</span>
        @endif
        <span class="btn add--primary" data-toggle="modal" data-target="#exampleModal" data-dayid="3" data-day="{{translate('messages.wednesday')}}"><i class="tio-add"></i></span>
    </div>
</div>

<div class="schedule-item">
    <span class="btn">{{translate('messages.thirsday')}} :</span>
    <div class="schedult-date-content">
        @if(isset($data['4']) && count($data['4']))
            @foreach ($data['4'] as $day)
            <div class="d-inline-flex align-items-center">
                <span class="start--time">
                    <span class="clock--icon">
                        <i class="tio-time"></i>
                    </span>
                    <span class="info">
                        <span>{{translate('messages.opening_time')}}</span>
                        {{date(config('timeformat'), strtotime($day['start_time']))}}
                    </span>
                </span>
                <span class="end--time">
                    <span class="clock--icon">
                        <i class="tio-time"></i>
                    </span>
                    <span class="info">
                        <span>{{translate('messages.closing_time')}}</span>
                        {{date(config('timeformat'), strtotime($day['end_time']))}}
                    </span>
                </span>
                <span class="dismiss--date" onclick="delete_schedule('{{route('vendor.business-settings.remove-schedule',['store_schedule'=>$day['id']])}}')"><i class="tio-clear-circle-outlined"></i></span></span>
            </div>
            @endforeach
        @else
            <span class="btn btn-sm btn-outline-danger m-1 disabled">{{translate('messages.Offday')}}</span>
        @endif
        <span class="btn add--primary" data-toggle="modal" data-target="#exampleModal" data-dayid="4" data-day="{{translate('messages.thirsday')}}"><i class="tio-add"></i></span>
    </div>
</div>

<div class="schedule-item">
    <span class="btn">{{translate('messages.friday')}} :</span>
    <div class="schedult-date-content">
        @if(isset($data['5']) && count($data['5']))
            @foreach ($data['5'] as $day)
            <div class="d-inline-flex align-items-center">
                <span class="start--time">
                    <span class="clock--icon">
                        <i class="tio-time"></i>
                    </span>
                    <span class="info">
                        <span>{{translate('messages.opening_time')}}</span>
                        {{date(config('timeformat'), strtotime($day['start_time']))}}
                    </span>
                </span>
                <span class="end--time">
                    <span class="clock--icon">
                        <i class="tio-time"></i>
                    </span>
                    <span class="info">
                        <span>{{translate('messages.closing_time')}}</span>
                        {{date(config('timeformat'), strtotime($day['end_time']))}}
                    </span>
                </span>
                <span class="dismiss--date" onclick="delete_schedule('{{route('vendor.business-settings.remove-schedule',['store_schedule'=>$day['id']])}}')"><i class="tio-clear-circle-outlined"></i></span></span>
            </div>
            @endforeach
        @else
            <span class="btn btn-sm btn-outline-danger m-1 disabled">{{translate('messages.Offday')}}</span>
        @endif
        <span class="btn add--primary" data-toggle="modal" data-target="#exampleModal" data-dayid="5" data-day="{{translate('messages.friday')}}"><i class="tio-add"></i></span>
    </div>
</div>

<div class="schedule-item">
    <span class="btn">{{translate('messages.saturday')}} :</span>
    <div class="schedult-date-content">
        @if(isset($data['6']) && count($data['6']))
            @foreach ($data['6'] as $day)
            <div class="d-inline-flex align-items-center">
                <span class="start--time">
                    <span class="clock--icon">
                        <i class="tio-time"></i>
                    </span>
                    <span class="info">
                        <span>{{translate('messages.opening_time')}}</span>
                        {{date(config('timeformat'), strtotime($day['start_time']))}}
                    </span>
                </span>
                <span class="end--time">
                    <span class="clock--icon">
                        <i class="tio-time"></i>
                    </span>
                    <span class="info">
                        <span>{{translate('messages.closing_time')}}</span>
                        {{date(config('timeformat'), strtotime($day['end_time']))}}
                    </span>
                </span>
                <span class="dismiss--date" onclick="delete_schedule('{{route('vendor.business-settings.remove-schedule',['store_schedule'=>$day['id']])}}')"><i class="tio-clear-circle-outlined"></i></span></span>
            </div>
            @endforeach
        @else
            <span class="btn btn-sm btn-outline-danger m-1 disabled">{{translate('messages.Offday')}}</span>
        @endif
        <span class="btn add--primary" data-toggle="modal" data-target="#exampleModal" data-dayid="6" data-day="{{translate('messages.saturday')}}"><i class="tio-add"></i></span>
    </div>
</div>

<div class="schedule-item">
    <span class="btn">{{translate('messages.sunday')}} :</span>
    <div class="schedult-date-content">
        @if(isset($data['0']) && count($data['0']))
            @foreach ($data['0'] as $day)
            <div class="d-inline-flex align-items-center">
                <span class="start--time">
                    <span class="clock--icon">
                        <i class="tio-time"></i>
                    </span>
                    <span class="info">
                        <span>{{translate('messages.opening_time')}}</span>
                        {{date(config('timeformat'), strtotime($day['start_time']))}}
                    </span>
                </span>
                <span class="end--time">
                    <span class="clock--icon">
                        <i class="tio-time"></i>
                    </span>
                    <span class="info">
                        <span>{{translate('messages.closing_time')}}</span>
                        {{date(config('timeformat'), strtotime($day['end_time']))}}
                    </span>
                </span>
                <span class="dismiss--date" onclick="delete_schedule('{{route('vendor.business-settings.remove-schedule',['store_schedule'=>$day['id']])}}')"><i class="tio-clear-circle-outlined"></i></span></span>
            </div>
            @endforeach
        @else
            <span class="btn btn-sm btn-outline-danger m-1 disabled">{{translate('messages.Offday')}}</span>
        @endif
        <span class="btn add--primary" data-toggle="modal" data-target="#exampleModal" data-dayid="0" data-day="{{translate('messages.sunday')}}"><i class="tio-add"></i></span>
    </div>
</div>