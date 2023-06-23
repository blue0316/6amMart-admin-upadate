@extends('layouts.admin.app')

@section('title',translate('messages.Delivery Man Preview'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title text-break">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/delivery-man.png')}}" class="w--26" alt="">
                </span>
                <span>{{$dm['f_name'].' '.$dm['l_name']}}</span>
            </h1>
            <div class="row">
                <div class="js-nav-scroller hs-nav-scroller-horizontal mt-2">
                    <!-- Nav -->
                    <ul class="nav nav-tabs nav--tabs border-0">
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('admin.users.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'info'])}}"  aria-disabled="true">{{translate('messages.info')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('admin.users.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'transaction'])}}"  aria-disabled="true">{{translate('messages.transaction')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="{{route('admin.users.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'conversation'])}}"  aria-disabled="true">{{translate('messages.conversations')}}</a>
                        </li>
                    </ul>
                    <!-- End Nav -->
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-header-title">{{ translate('messages.conversation') }} {{ translate('messages.list') }}</h1>
            </div>
            <!-- End Page Header -->

            <div class="row g-3">
                <div class="col-lg-4 col-md-6">
                    <!-- Card -->
                    <div class="card h-100">
                        <div class="card-header border-0">
                            <div class="input-group input---group">
                                <div class="input-group-prepend border-inline-end-0">
                                    <span class="input-group-text border-inline-end-0" id="basic-addon1"><i class="tio-search"></i></span>
                                </div>
                                <input type="text" class="form-control border-inline-start-0 pl-1" id="serach" placeholder="{{ translate('messages.search') }}" aria-label="Username"
                                    aria-describedby="basic-addon1" autocomplete="off">
                            </div>
                        </div>
                        <!-- Body -->
                        <div class="card-body p-0 initial-19" style="overflow-y: scroll;height: 600px"  id="dm-conversation-list">
                            <div class="border-bottom"></div>
                            @include('admin-views.delivery-man.partials._conversation_list')
                        </div>
                        <!-- End Body -->
                    </div>
                    <!-- End Card -->
                </div>
                <div class="col-lg-8 col-nd-6" id="dm-view-conversation">
                    <center class="mt-2">
                        <h4 class="initial-20">{{ translate('messages.view') }} {{ translate('messages.conversation') }}
                        </h4>
                    </center>
                    {{-- view here --}}
                </div>
            </div>
            <!-- End Row -->
        </div>

    </div>
@endsection

@push('script_2')
<script>
    function viewConvs(url, id_to_active, conv_id, sender_id) {
        $('.customer-list').removeClass('conv-active');
        $('#' + id_to_active).addClass('conv-active');
        let new_url= "{{route('admin.users.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'conversation'])}}" + '?conversation=' + conv_id+ '&user=' + sender_id;
            $.get({
                url: url,
                success: function(data) {
                    window.history.pushState('', 'New Page Title', new_url);
                    $('#dm-view-conversation').html(data.view);
                }
            });
    }

    var page = 1;
    var user_id =  $('#deliver_man').val();
    $('#dm-conversation-list').scroll(function() {
        if ($('#dm-conversation-list').scrollTop() + $('#dm-conversation-list').height() >= $('#dm-conversation-list')
            .height()) {
            page++;
            loadMoreData(page);
        }
    });

    function loadMoreData(page) {
        $.ajax({
                url: "{{ route('admin.delivery-man.message-list-search') }}" + '?page=' + page,
                type: "get",
                data:{"user_id":user_id},
                beforeSend: function() {

                }
            })
            .done(function(data) {
                if (data.html == " ") {
                    return;
                }
                $("#dm-conversation-list").append(data.html);
            })
            .fail(function(jqXHR, ajaxOptions, thrownError) {
                alert('server not responding...');
            });
    };

    function fetch_data(page, query) {
            $.ajax({
                url: "{{ route('admin.delivery-man.message-list-search') }}" + '?page=' + page + "&key=" + query,
                type: "get",
                data:{"user_id":user_id},
                success: function(data) {
                    $('#dm-conversation-list').empty();
                    $("#dm-conversation-list").append(data.html);
                }
            })
        };

        $(document).on('keyup', '#serach', function() {
            var query = $('#serach').val();
            fetch_data(page, query);
        });
</script>
@endpush
