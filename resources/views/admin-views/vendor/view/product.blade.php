@extends('layouts.admin.app')

@section('title',$store->name."'s ".translate('messages.items'))

@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{asset('public/assets/admin/css/croppie.css')}}" rel="stylesheet">

@endpush

@section('content')
<div class="content container-fluid">
    @include('admin-views.vendor.view.partials._header',['store'=>$store])
    <!-- Page Heading -->
    @php($foods = \App\Models\Item::withoutGlobalScope(\App\Scopes\StoreScope::class)->where('store_id', $store->id)->latest()->paginate(25))
    <div class="tab-content">
        <div class="tab-pane fade show active" id="product">

            <div class="resturant-card-navbar px-xl-4 justify-content-evenly">
                <div class="order-info-item">
                    <div class="order-info-icon icon-sm">
                        <img src="{{asset('/public/assets/admin/img/all.png')}}" alt="public">
                    </div>
                    @php($item = \App\Models\Item::withoutGlobalScope(\App\Scopes\StoreScope::class)->where(['store_id'=>$store->id])->count())
                    @php($item = ($item == null) ? 0 : $item)
                    <h6 class="card-subtitle">
                        {{translate('All Items')}}
                        <span class="amount text--primary">{{ $item }}  </span>
                    </h6>
                </div>
                <span class="order-info-seperator"></span>
                <div class="order-info-item">
                    <div class="order-info-icon icon-sm">
                <img src="{{asset('/public/assets/admin/img/active.png')}}" alt="public">
                    </div>
                    @php($item = \App\Models\Item::withoutGlobalScope(\App\Scopes\StoreScope::class)->where(['store_id'=>$store->id, 'status'=>1])->count())
                    @php($item = ($item == null) ? 0 : $item)
                    <h6 class="card-subtitle">
                        {{translate('Active Items')}}
                        <span class="amount text--success">{{ $item }}  </span>
                    </h6>
                </div>
                <span class="order-info-seperator"></span>
                <div class="order-info-item">
                    <div class="order-info-icon icon-sm">
                <img src="{{asset('/public/assets/admin/img/inactive.png')}}" alt="public">
                    </div>
                    @php($item = \App\Models\Item::withoutGlobalScope(\App\Scopes\StoreScope::class)->where(['store_id'=>$store->id, 'status'=>0])->count())
                    @php($item = ($item == null) ? 0 : $item)
                    <h6 class="card-subtitle">
                        {{translate('Inactive Items')}}
                        <span class="amount text--danger">{{ $item }}  </span>
                    </h6>
                </div>
            </div>


            <div class="card">
                <div class="card-header border-0 py-2">
                    <div class="search--button-wrapper">
                        <h3 class="card-title">
                            {{translate('messages.items')}} <span class="badge badge-soft-dark ml-2"><span class="total_items">{{$foods->total()}}</span></span>
                        </h3>

                    <form id="search-form" class="search-form">
                    <input type="hidden" name="store_id" value="{{$store->id}}">
                        <!-- Search -->
                        <div class="input-group input--group">
                            <input id="datatableSearch" name="search" type="search" class="form-control h--40px" placeholder="{{ translate('Search by name...') }}" aria-label="{{translate('messages.search_here')}}">
                            <button type="submit" class="btn btn--secondary h--40px"><i class="tio-search"></i></button>
                        </div>
                        <!-- End Search -->
                    </form>

                    <!-- Unfold -->
                    <div class="hs-unfold mr-2">
                        <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle min-height-40" href="javascript:;"
                            data-hs-unfold-options='{
                                    "target": "#usersExportDropdown",
                                    "type": "css-animation"
                                }'>
                            <i class="tio-download-to mr-1"></i> {{ translate('messages.export') }}
                        </a>

                        <div id="usersExportDropdown"
                            class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                            {{-- <span class="dropdown-header">{{ translate('messages.options') }}</span>
                            <a id="export-copy" class="dropdown-item" href="javascript:;">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/illustrations/copy.svg"
                                    alt="Image Description">
                                {{ translate('messages.copy') }}
                            </a>
                            <a id="export-print" class="dropdown-item" href="javascript:;">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/illustrations/print.svg"
                                    alt="Image Description">
                                {{ translate('messages.print') }}
                            </a>
                            <div class="dropdown-divider"></div> --}}
                            <span class="dropdown-header">{{ translate('messages.download') }}
                                {{ translate('messages.options') }}</span>
                            <a id="export-excel" class="dropdown-item" href="{{route('admin.item.store-item-export', ['type'=>'excel', 'store_id'=>$store->id])}}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                    alt="Image Description">
                                {{ translate('messages.excel') }}
                            </a>
                            <a id="export-csv" class="dropdown-item" href="{{route('admin.item.store-item-export', ['type'=>'csv', 'store_id'=>$store->id])}}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                    alt="Image Description">
                                .{{ translate('messages.csv') }}
                            </a>
                            {{-- <a id="export-pdf" class="dropdown-item" href="javascript:;">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/pdf.svg"
                                    alt="Image Description">
                                {{ translate('messages.pdf') }}
                            </a> --}}
                        </div>
                    </div>
                    <!-- End Unfold -->
                        <a href="{{route('admin.item.add-new')}}" class="btn btn--primary pull-right"><i class="tio-add-circle"></i> {{translate('messages.add')}} {{translate('messages.new')}} {{translate('messages.item')}}</a>
                    </div>
                </div>
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
                                <th class="border-0">{{translate('messages.name')}}</th>
                                <th class="border-0">{{translate('messages.type')}}</th>
                                <th class="border-0">{{translate('messages.price')}}</th>
                                <th class="border-0">{{translate('messages.status')}}</th>
                                <th class="border-0 text-center">{{translate('messages.action')}}</th>
                            </tr>
                        </thead>

                        <tbody id="setrows">
                        @php($foods = \App\Models\Item::withoutGlobalScope(\App\Scopes\StoreScope::class)->where('store_id', $store->id)->latest()->paginate(25))
                        @foreach($foods as $key=>$food)

                        <tr>
                            <td>{{$key+1}}</td>
                            <td>
                                <a class="media align-items-center" href="{{route('admin.item.view',[$food['id']])}}">
                                    <img class="avatar avatar-lg mr-3" src="{{asset('storage/app/public/product')}}/{{$food['image']}}"
                                            onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'" alt="{{$food->name}} image">
                                    <div class="media-body">
                                        <h5 class="text-hover-primary mb-0">{{Str::limit($food['name'],20,'...')}}</h5>
                                    </div>
                                </a>
                            </td>
                            <td>
                            {{Str::limit($food->category?$food->category->name:translate('messages.category_deleted'),20,'...')}}
                            </td>
                            <td>{{\App\CentralLogics\Helpers::format_currency($food['price'])}}</td>
                            <td>
                                <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$food->id}}">
                                    <input type="checkbox" onclick="location.href='{{route('admin.item.status',[$food['id'],$food->status?0:1])}}'"class="toggle-switch-input" id="stocksCheckbox{{$food->id}}" {{$food->status?'checked':''}}>
                                    <span class="toggle-switch-label">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="btn action-btn btn--primary btn-outline-primary"
                                        href="{{route('admin.item.edit',[$food['id']])}}" title="{{translate('messages.edit')}} {{translate('messages.item')}}"><i class="tio-edit"></i>
                                    </a>
                                    <a class="btn action-btn btn--danger btn-outline-danger" href="javascript:"
                                        onclick="form_alert('food-{{$food['id']}}','{{ translate('messages.Want to delete this item ?') }}')" title="{{translate('messages.delete')}} {{translate('messages.item')}}"><i class="tio-delete-outlined"></i>
                                    </a>
                                </div>
                                <form action="{{route('admin.item.delete',[$food['id']])}}"
                                        method="post" id="food-{{$food['id']}}">
                                    @csrf @method('delete')
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @if(count($foods) !== 0)
                    <hr>
                    @endif
                    <div class="page-area">
                        {!! $foods->links() !!}
                    </div>
                    @if(count($foods) === 0)
                    <div class="empty--data">
                        <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                        <h5>
                            {{translate('no_data_found')}}
                        </h5>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script_2')
    <!-- Page level plugins -->
    <script>
        // Call the dataTables jQuery plugin
        $(document).ready(function () {
            $('#dataTable').DataTable();
        });
    </script>
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

            $('#column3_search').on('change', function () {
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

        $('#search-form').on('submit', function (e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.post({
                    url: '{{route('admin.item.store-search', $store->id)}}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        $('#loading').show();
                    },
                    success: function (data) {
                        $('#setrows').html(data.view);
                        $('#total_items').html(data.count);
                        $('.page-area').hide();
                    },
                    complete: function () {
                        $('#loading').hide();
                    },
                });
            });

        // $('#search-form').on('submit', function () {
        //     var formData = new FormData(this);
        //     $.ajaxSetup({
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         }
        //     });
        //     $.post({
        //         url: '{{route('admin.item.store-search', $store->id)}}',
        //         data: formData,
        //         cache: false,
        //         contentType: false,
        //         processData: false,
        //         beforeSend: function () {
        //             $('#loading').show();
        //         },
        //         success: function (data) {
        //             $('#setrows').html(data.view);
        //             $('.page-area').hide();
        //         },
        //         complete: function () {
        //             $('#loading').hide();
        //         },
        //     });
        // });
    </script>
@endpush
