@extends('layouts.vendor.app')

@section('title',translate('messages.sub_category'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/categories.png')}}" class="w--20" alt="">
                </span>
                <span>
                    {{translate('messages.sub_category')}} <span class="badge badge-soft-dark ml-2" id="itemCount">{{$categories->total()}}</span>
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header py-2 border-0">
                        <div class="search--button-wrapper justify-content-end">
                            <form action="javascript:;" id="search-form" class="search-form min--280">
                                @csrf
                                <!-- Search -->
                                <div class="input-group input--group">
                                    <input id="datatableSearch" type="search" name="search" class="form-control" placeholder="{{translate('messages.ex_:_search_sub_category')}}" aria-label="{{translate('messages.search_here')}}">
                                    <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                                </div>
                                <!-- End Search -->
                            </form>
                            <!-- Unfold -->
                            <div class="hs-unfold mr-2">
                                <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle h--40px" href="javascript:;"
                                    data-hs-unfold-options='{
                                        "target": "#usersExportDropdown",
                                        "type": "css-animation"
                                    }'>
                                    <i class="tio-download-to mr-1"></i> {{translate('messages.export')}}
                                </a>

                                <div id="usersExportDropdown"
                                        class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                                    {{-- <span class="dropdown-header">{{translate('messages.options')}}</span>
                                    <a id="export-copy" class="dropdown-item" href="javascript:;">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                                src="{{asset('public/assets/admin')}}/svg/illustrations/copy.svg"
                                                alt="Image Description">
                                        {{translate('messages.copy')}}
                                    </a>
                                    <a id="export-print" class="dropdown-item" href="javascript:;">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                                src="{{asset('public/assets/admin')}}/svg/illustrations/print.svg"
                                                alt="Image Description">
                                        {{translate('messages.print')}}
                                    </a>
                                    <div class="dropdown-divider"></div> --}}
                                    <span
                                        class="dropdown-header">{{translate('messages.download')}} {{translate('messages.options')}}</span>
                                    <a id="export-excel" class="dropdown-item" href="{{route('vendor.category.export-sub-categories', ['type'=>'excel'])}}">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                                src="{{asset('public/assets/admin')}}/svg/components/excel.svg"
                                                alt="Image Description">
                                        {{translate('messages.excel')}}
                                    </a>
                                    <a id="export-csv" class="dropdown-item" href="{{route('vendor.category.export-sub-categories', ['type'=>'csv'])}}">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                                src="{{asset('public/assets/admin')}}/svg/components/placeholder-csv-format.svg"
                                                alt="Image Description">
                                        .{{translate('messages.csv')}}
                                    </a>
                                    {{-- <a id="export-pdf" class="dropdown-item" href="javascript:;">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                                src="{{asset('public/assets/admin')}}/svg/components/pdf.svg"
                                                alt="Image Description">
                                        {{translate('messages.pdf')}}
                                    </a> --}}
                                </div>
                            </div>
                            <!-- End Unfold -->
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive datatable-custom">
                            <table id="columnSearchDatatable"
                                class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                                data-hs-datatables-options='{
                                    "search": "#datatableSearch",
                                    "entries": "#datatableEntries",
                                    "isResponsive": false,
                                    "isShowPaging": false,
                                    "paging":false,
                                }'>
                                <thead class="thead-light">
                                    <tr>
                                        <th class="border-0">{{translate('messages.#')}}</th>
                                        <th class="border-0">{{translate('messages.category_id')}}</th>
                                        <th class="border-0">{{translate('messages.main')}} {{translate('messages.category')}}</th>
                                        <th class="border-0">{{translate('messages.sub_category')}}</th>
                                    </tr>
                                </thead>

                                <tbody id="set-rows">
                                @foreach($categories as $key=>$category)
                                    <tr>
                                        <td>{{$key+$categories->firstItem()}}</td>
                                        <td>{{$category->id}}</td>
                                        <td>
                                            <span class="d-block font-size-sm text-body">
                                                {{Str::limit($category->parent?$category->parent['name']:translate('messages.category_deleted'),20,'...')}}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="d-block font-size-sm text-body">
                                                {{Str::limit($category->name,20,'...')}}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer page-area">
                        <!-- Pagination -->
                        {!! $categories->links() !!}
                        <!-- End Pagination -->
                        @if(count($categories) === 0)
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
    <script>
        $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'), {
                select: {
                    style: 'multi',
                    classMap: {
                        checkAll: '#datatableCheckAll',
                        counter: '#datatableCounter',
                        counterInfo: '#datatableCounterInfo'
                    }
                },
                language: {
                    zeroRecords: '<div class="text-center p-4">' +
                    '<img class="w-7rem mb-3" src="{{asset('public/assets/admin/svg/illustrations/sorry.svg')}}" alt="Image Description">' +

                    '</div>'
                }
            });

            $('#datatableSearch').on('mouseup', function (e) {
                var $input = $(this),
                    oldValue = $input.val();

                if (oldValue == "") return;

                setTimeout(function(){
                    var newValue = $input.val();

                    if (newValue == ""){
                    // Gotcha
                    datatable.search('').draw();
                    }
                }, 1);
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
            url: '{{route('vendor.category.sub-search')}}',
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
