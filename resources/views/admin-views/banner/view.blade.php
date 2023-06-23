@extends('layouts.admin.app')

@section('title',translate('Banner View'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-6">
                    <h1 class="page-header-title">{{$banner->title}}</h1>
                </div>
                <div class="col-6">
                    <a href="{{url()->previous()}}" class="btn btn-primary float-right">
                        <i class="tio-back-ui"></i> {{translate('messages.back')}}
                    </a>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <!-- Card -->
        <div class="card mb-3 mb-lg-5">
            <!-- Body -->
            <div class="card-body">
                <div class="row align-items-md-center gx-md-5">
                    <div class="col-md-auto mb-3 mb-md-0">
                        <div class="d-flex align-items-center">
                            <img class="avatar avatar-xxl avatar-4by3 mr-4"
                                 src="{{asset('storage/app/public/banner')}}/{{$banner->image}}"
                                 onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'"
                                 alt="Image Description">
                            <div class="d-block">


                            </div>
                        </div>
                    </div>

                    <div class="col-md">
                        <h4>{{translate('messages.short')}} {{translate('messages.description')}} : </h4>
                        <p>{{$banner->description}}</p>
                    </div>

                </div>
            </div>
            <!-- End Body -->
        </div>
        <!-- End Card -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <!-- Card -->
                <div class="card">
                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                               class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                               data-hs-datatables-options='{
                                 "order": [],
                                 "orderCellsTop": true
                               }'>
                            <thead class="thead-light">
                            <tr>
                                <th>{{translate('messages.#')}}</th>
                                <th class="w--15">{{translate('messages.logo')}}</th>
                                <th class="w--2">{{translate('messages.name')}}</th>
                                <th class="w--25">{{translate('messages.store')}}</th>
                                <th>{{translate('messages.email')}}</th>
                                <th>{{translate('messages.phone')}}</th>
                                <th>{{translate('messages.action')}}</th>
                            </tr>
                            <tr>
                                <th colspan="3">
                                    <form action="{{route('admin.banner.addstore',$banner->id)}}" id="store-add-form" method="POST">
                                        @csrf
                                        <!-- Search -->
                                        <div class="row">
                                            <div class="input-group-prepend col-md-7">
                                            @php($allstores=App\Models\Store::all())
                                                <select name="store_id" id="store_id" class="form-control">
                                                    @forelse($allstores as $store)
                                                    @if(!in_array($store->id, $store_ids))
                                                    <option value="{{$store->id}}" >{{$store->name}}</option>
                                                    @endif
                                                    @empty
                                                    <option value="">{{ translate('messages.no_data_found') }}</option>
                                                    @endforelse
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn--primary col-md-5">{{translate('messages.add')}} {{translate('messages.store')}}</button>

                                        </div>
                                        <!-- End Search -->
                                    </form>
                                </th>
                                <th></th>
                                <th colspan="3">
                                    <form action="javascript:" id="search-form">
                                        <!-- Start Search -->
                                        <div class="input-group input--group">
                                            <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="{{translate('messages.search')}}" aria-label="Search" required>
                                            <button type="submit" class="btn btn--secondary">
                                                <i class="tio-search"></i>
                                            </button>
                                        </div>
                                        <!-- End Search -->
                                    </form>
                                </th>

                                <th></th>
                                <th></th>
                            </tr>
                            </thead>

                            <tbody id="set-rows">
                            @foreach($stores as $key=>$dm)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>
                                        <div class="inline--1">
                                            <img class="img--60 img--circle"
                                                 onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'"
                                                 src="{{asset('storage/app/public/store')}}/{{$dm['logo']}}">
                                        </div>
                                    </td>
                                    <td>
                                        <span class="d-block font-size-sm text-body">
                                            {{$dm->name}}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="d-block font-size-sm text-body">
                                            {{$dm->vendor->f_name.' '.$dm->vendor->l_name}}
                                        </span>
                                    </td>
                                    <td>
                                        {{$dm->email}}
                                        {{--<span class="d-block font-size-sm">{{$banner['image']}}</span>--}}
                                    </td>
                                    <td>
                                        {{$dm['phone']}}
                                    </td>
                                    <td>
                                        <!-- Dropdown -->
                                        <div class="inline--2"
                                                 onclick="location.href='{{route('admin.banner.campaign',[$banner->id, $dm['id']])}}'">
                                                <span class="legend-indicator bg-danger"></span>{{ translate('messages.remove') }}
                                            </div>
                                        <!-- End Dropdown -->
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <hr>

                        <div class="page-area">
                            <table>
                                <tfoot>
                                {!! $stores->links() !!}
                                </tfoot>
                            </table>
                        </div>

                    </div>
                    <!-- End Table -->
                </div>
                <!-- End Card -->
            </div>
        </div>
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

        $('#search-form').on('submit', function () {
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.store.search')}}',
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
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });
    </script>
@endpush
