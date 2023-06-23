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
                <span dir="ltr">{{ $user['phone'] }}</span>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="scroll-down">
            @foreach($convs as $con)
                @if($con->sender_id == $receiver->id)
                    <div class="pt1 pb-1">
                        <div class="conv-reply-1">
                                <h6>{{$con->message}}</h6>
                                @if($con->file!=null)
                                @foreach (json_decode($con->file) as $img)
                                <br>
                                    <img class="w-100"
                                    src="{{asset('storage/app/public/conversation').'/'.$img}}">
                                    @endforeach
                                @endif
                        </div>
                        <div class="pl-1">
                            <small>{{date('d M Y',strtotime($con->created_at))}} {{date(config('timeformat'),strtotime($con->created_at))}}</small>
                        </div>
                    </div>
                @else
                    <div class="pt-1 pb-1">
                        <div class="conv-reply-2">
                            <h6>{{$con->message}}</h6>
                            @if($con->file!=null)
                            @foreach (json_decode($con->file) as $img)
                            <br>
                                <img class="w-100"
                                src="{{asset('storage/app/public/conversation').'/'.$img}}">
                                @endforeach
                            @endif
                        </div>
                        <div class="text-right pr-1">
                            <small>{{date('d M Y',strtotime($con->created_at))}} {{date(config('timeformat'),strtotime($con->created_at))}}</small>
                            @if ($con->is_seen == 1)
                            <span class="text-primary"><i class="tio-checkmark-circle"></i></span>
                            @else
                            <span><i class="tio-checkmark-circle-outlined"></i></span>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
            <div id="scroll-here"></div>
        </div>

    </div>
    <!-- Body -->
    <div class="card-footer border-0 conv-reply-form">

        <form action="javascript:" method="post" id="reply-form" enctype="multipart/form-data">
            @csrf
            <div class="quill-custom_">
                <label for="msg" class="layer-msg">

                </label>
                <textarea class="form-control pr--180" id="msg" rows = "1" name="reply"></textarea>
                <div id="coba">
                </div>
                <button type="submit"
                 {{-- onclick="replyConvs('{{route('admin.message.store',[$user->id])}}')" --}}
                        class="btn btn-primary btn--primary con-reply-btn">{{__('messages.send')}}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.scroll-down').animate({
            scrollTop: $('#scroll-here').offset().top
        },0);
    });


    $(function() {
        $("#coba").spartanMultiImagePicker({
            fieldName: 'images[]',
            maxCount: 3,
            rowHeight: '55px',
            groupClassName: 'attc--img',
            maxFileSize: '',
            placeholderImage: {
                image: '{{ asset('public/assets/admin/img/attatchments.png') }}',
                width: '100%'
            },
            dropFileLabel: "Drop Here",
            onAddRow: function(index, file) {

            },
            onRenderedPreview: function(index) {

            },
            onRemoveRow: function(index) {

            },
            onExtensionErr: function(index, file) {
                toastr.error('{{ __('messages.please_only_input_png_or_jpg_type_file') }}', {
                    CloseButton: true,
                    ProgressBar: true
                });
            },
            onSizeErr: function(index, file) {
                toastr.error('{{ __('messages.file_size_too_big') }}', {
                    CloseButton: true,
                    ProgressBar: true
                });
            }
        });
    });


    $('#reply-form').on('submit', function() {
        $('button[type=submit], input[type=submit]').prop('disabled',true);
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{ route('admin.message.store', [$user->user_id]) }}',
                data: $('reply-form').serialize(),
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    if (data.errors && data.errors.length > 0) {
                        $('button[type=submit], input[type=submit]').prop('disabled',false);
                        toastr.error('Write something to send massage!', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }else{

                        toastr.success('Message sent', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        $('#view-conversation').html(data.view);
                    }
                },
                error() {
                    toastr.error('Write something to send massage!', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        });
</script>
