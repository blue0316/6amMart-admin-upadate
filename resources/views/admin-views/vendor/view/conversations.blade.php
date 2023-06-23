@extends('layouts.admin.app')

@section('title',$store->name."'s ".translate('messages.conversation'))

@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{asset('public/assets/admin/css/croppie.css')}}" rel="stylesheet">

@endpush

@section('content')
<div class="content container-fluid">
    @include('admin-views.vendor.view.partials._header',['store'=>$store])
    <!-- Page Heading -->
    <div class="tab-content">
        <div class="tab-pane fade show active" id="product">
            <div class="row pt-2">
                <div class="content container-fluid">
                    <!-- Page Header -->
                    <div class="page-header">
                        <h1 class="page-header-title">{{ translate('messages.conversation') }} {{ translate('messages.list') }}</h1>
                    </div>
                    <!-- End Page Header -->

                    <div class="row g-3">
                        <div class="col-lg-4 col-md-6">
                            <!-- Card -->
                            <div class="card">
                                <div class="card-header border-0">
                                    <div class="input-group input---group">
                                        <div class="input-group-prepend border-inline-end-0">
                                            <span class="input-group-text border-inline-end-0" id="basic-addon1"><i class="tio-search"></i></span>
                                        </div>
                                        <input type="text" class="form-control border-inline-start-0 pl-1" id="serach" placeholder="Search" aria-label="Username"
                                            aria-describedby="basic-addon1" autocomplete="off">
                                    </div>
                                </div>
                                <input type="hidden" id="vendor_id" value="{{ $store->id }}">
                                <!-- Body -->
                                <div class="card-body p-0" style="overflow-y: scroll;height: 600px" id="vendor-conversation-list">
                                    <div class="border-bottom"></div>
                                    @include('admin-views.vendor.view.partials._conversation_list')
                                </div>
                                <!-- End Body -->
                            </div>
                            <!-- End Card -->
                        </div>
                        <div class="col-lg-8 col-nd-6" id="vendor-view-conversation">
                            <center style="margin-top: 10%">
                                <h4 style="color: rgba(113,120,133,0.62)">{{ translate('messages.view') }} {{ translate('messages.conversation') }}
                                </h4>
                            </center>
                            {{-- view here --}}
                        </div>
                    </div>
                    <!-- End Row -->
                </div>


            </div>
        </div>
    </div>
</div>
@endsection

@push('script_2')
<script>
    function viewConvs(url, id_to_active, conv_id, sender_id) {
        $('.customer-list').removeClass('conv-active');
        $('#' + id_to_active).addClass('conv-active');
        let new_url= "{{route('admin.store.view', ['store'=>$store->id, 'tab'=> 'conversations'])}}" + '?conversation=' + conv_id+ '&user=' + sender_id;
            $.get({
                url: url,
                success: function(data) {
                    window.history.pushState('', 'New Page Title', new_url);
                    $('#vendor-view-conversation').html(data.view);
                }
            });
    }

    var page = 1;
    var user_id =  $('#vendor_id').val();
    $('#vendor-conversation-list').scroll(function() {
        if ($('#vendor-conversation-list').scrollTop() + $('#vendor-conversation-list').height() >= $('#vendor-conversation-list')
            .height()) {
            page++;
            loadMoreData(page);
        }
    });

    function loadMoreData(page) {
        $.ajax({
                url: "{{ route('admin.store.message-list') }}" + '?page=' + page,
                type: "get",
                data:{"user_id":user_id},
                beforeSend: function() {

                }
            })
            .done(function(data) {
                if (data.html == " ") {
                    return;
                }
                $("#vendor-conversation-list").append(data.html);
            })
            .fail(function(jqXHR, ajaxOptions, thrownError) {
                alert('server not responding...');
            });
    };

    function fetch_data(page, query) {
            $.ajax({
                url: "{{ route('admin.store.message-list') }}" + '?page=' + page + "&key=" + query,
                type: "get",
                data:{"user_id":user_id},
                success: function(data) {
                    $('#vendor-conversation-list').empty();
                    $("#vendor-conversation-list").append(data.html);
                }
            })
        };

        $(document).on('keyup', '#serach', function() {
            var query = $('#serach').val();
            fetch_data(page, query);
        });
</script>
@endpush
