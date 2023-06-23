@extends('layouts.vendor.app')

@section('title',translate('messages.deliverymen'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/deliveryman.png')}}" class="w--30" alt="">
                </span>
                <span>
                   {{translate('messages.deliveryman')}}<span class="badge badge-soft-dark ml-2" id="itemCount">{{$delivery_men->total()}}</span>
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header justify-content-end">
                <form action="javascript:" id="search-form">
                    @csrf
                    <div class="input-group input--group">
                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                placeholder="{{translate('messages.ex_search_name')}}" aria-label="{{translate('messages.ex_search_name')}}" required>
                        <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>

                    </div>
                    <!-- End Search -->
                </form>
            </div>
            <!-- End Header -->

            <!-- Table -->
            <div class="table-responsive datatable-custom">
                <table id="columnSearchDatatable"
                        class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                        data-hs-datatables-options='{
                            "order": [],
                            "orderCellsTop": true,
                            "paging":false
                        }'>
                    <thead class="thead-light">
                    <tr>
                        <th class="border-0 text-capitalize">{{translate('messages.#')}}</th>
                        <th class="border-0 text-capitalize">{{translate('messages.name')}}</th>
                        <th class="border-0 text-capitalize">{{translate('messages.availability')}} {{translate('messages.status')}}</th>
                        <th class="border-0 text-capitalize">{{translate('messages.phone')}}</th>
                        <th class="border-0 text-capitalize text-center">{{translate('messages.active_orders')}}</th>
                        <th class="border-0 text-capitalize text-center">{{translate('messages.action')}}</th>
                    </tr>
                    </thead>

                    <tbody id="set-rows">
                    @foreach($delivery_men as $key=>$dm)
                        <tr>
                            <td>{{$key+$delivery_men->firstItem()}}</td>
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
                    </tbody>
                </table>
                @if(count($delivery_men) !== 0)
                <hr>
                @endif
                <div class="page-area">
                    <table>
                        <tfoot>
                        {!! $delivery_men->links() !!}
                        </tfoot>
                    </table>
                </div>
                    @if(count($delivery_men) === 0)
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

            $('#column1_search').on('keyup', function () {
                datatable
                    .columns(1)
                    .search(this.value)
                    .draw();
            });

            $('#column2_search').on('keyup', function () {
                datatable
                    .columns(2)
                    .search(this.value)
                    .draw();
            });

            $('#column3_search').on('keyup', function () {
                datatable
                    .columns(3)
                    .search(this.value)
                    .draw();
            });

            $('#column4_search').on('keyup', function () {
                datatable
                    .columns(4)
                    .search(this.value)
                    .draw();
            });


            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
    </script>

    <script>
        $('#search-form').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('vendor.delivery-man.search')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#set-rows').html(data.view);
                    $('#itemCount').html(data.count);
                    $('.page-area').hide();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });
    </script>
@endpush
