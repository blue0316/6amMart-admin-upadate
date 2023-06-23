<div class="card h-100">
    <!-- Header -->
    <div class="card-header">
        <div class="chat-user-info w-100 d-flex align-items-center">
            <div class="chat-user-info-img">
                <img class="avatar-img"
                    src="{{asset('storage/app/public/profile/'.$user['image'])}}"
                    onerror="this.src='{{asset('public/assets/admin')}}/img/160x160/img1.jpg'"
                    alt="Image Description">
            </div>
            <div class="chat-user-info-content">
                <h5 class="mb-0 text-capitalize">
                    {{$user['f_name'].' '.$user['l_name']}}</h5>
                <span>{{ $user['phone'] }}</span>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="scroll-down">
            @foreach($convs as $con)
                @if($con->sender_id == $user->id)
                    <div class="pt1 pb-1">
                        <div class="conv-reply-1">
                            <h6>{{$con->message}}</h6>
                            @if($con->file!=null)
                            @foreach (json_decode($con->file) as $img)
                            <br>
                                <img class="w-100" src="{{asset('storage/app/public/conversation').'/'.$img}}">
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="pl-1">
                        <small>{{date('d M Y',strtotime($con->created_at))}} {{date(config('timeformat'),strtotime($con->created_at))}}</small>
                    </div>
                @else
                    <div class="pt-1 pb-1">
                        <div class="conv-reply-2">
                            <h6>{{$con->message}}</h6>
                            @if($con->file!=null)
                            @foreach (json_decode($con->file) as $img)
                            <br>
                                <img class="w-100" src="{{asset('storage/app/public/conversation').'/'.$img}}">
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="text-right pr-1">
                        <small>{{date('d M Y',strtotime($con->created_at))}} {{date(config('timeformat'),strtotime($con->created_at))}}</small>
                    </div>
                @endif
            @endforeach
            <div id="scroll-here"></div>
        </div>

    </div>
</div>

<script>
    $(document).ready(function () {
        $('.scroll-down').animate({
            scrollTop: $('#scroll-here').offset().top
        },0);
    });
</script>
