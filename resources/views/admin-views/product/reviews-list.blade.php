@extends('layouts.admin.app')

@section('title',translate('Review List'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/items.png')}}" class="w--22" alt="">
                </span>
                <span>
                    {{translate('messages.item')}} {{translate('messages.reviews')}} <span class="badge badge-soft-dark ml-2" id="itemCount">{{$reviews->total()}}</span>
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header border-0 py-2">
                <div class="search--button-wrapper justify-content-end">
                    <form id="search-form" class="search-form">
                        <!-- Search -->
                        <div class="input-group input--group">
                            <input id="datatableSearch" name="search" type="search" class="form-control min-height-45" placeholder="{{translate('ex_:_search_item_name')}}" aria-label="{{translate('messages.search_here')}}">
                            <button type="submit" class="btn btn--secondary min-height-45"><i class="tio-search"></i></button>
                        </div>
                        <!-- End Search -->
                    </form>
                </div>
            </div>
            <!-- End Header -->

            <!-- Table -->
            <div class="table-responsive datatable-custom">
                <table id="columnSearchDatatable"
                        class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                        data-hs-datatables-options='{
                            "order": [],
                            "orderCellsTop": true,
                            "paging": false
                        }'>
                    <thead class="thead-light">
                    <tr>
                        <th class="border-0">{{translate('sl')}}</th>
                        <th class="border-0 w--1">{{translate('messages.item')}}</th>
                        <th class="border-0 w--2">{{translate('messages.customer')}}</th>
                        <th class="border-0 w--3">{{translate('messages.review')}}</th>
                        <th class="border-0">{{translate('messages.rating')}}</th>
                        <th class="border-0">{{translate('messages.status')}}</th>
                    </tr>
                    </thead>

                    <tbody id="set-rows">
                    @foreach($reviews as $key=>$review)
                        <tr>
                            <td>{{$key+$reviews->firstItem()}}</td>
                            {{-- {{ dd($review) }} --}}
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
                                <p class="text-danger">{{translate('messages.Item deleted!')}}</p>
                                @endif

                            </td>
                            <td>
                                @if ($review->customer)

                                <a href="{{route('admin.customer.view',[$review->user_id])}}">
                                    {{$review->customer?$review->customer->f_name:""}} {{$review->customer?$review->customer->l_name:""}}
                                </a>
                                @else
                                <p class="text-danger">{{ translate('messages.Customer Not found!') }}</p>
                                @endif
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
                    </tbody>
                </table>
                @if(count($reviews) !== 0)
                <hr>
                @endif
                <div class="page-area">
                    {!! $reviews->links() !!}
                </div>
                @if(count($reviews) === 0)
                <div class="empty--data">
                    <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                    <h5>
                        {{translate('no_data_found')}}
                    </h5>
                </div>
                @endif
            </div>
            <!-- End Table -->
        </div>
        <!-- End Card -->
    </div>

@endsection

@push('script_2')
    <script>
        $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

        });

        function status_form_alert(id, message, e) {
            e.preventDefault();
            Swal.fire({
                title: '{{translate('messages.are_you_sure')}}',
                text: message,
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#FC6A57',
                cancelButtonText: 'No',
                confirmButtonText: 'Yes',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#'+id).submit()
                }
            })
        }

        $('#search-form').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.item.reviews.search')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#set-rows').html(data.view);
                    $('.page-area').hide();
                    $('#itemCount').html(data.count);
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });
    </script>
@endpush
