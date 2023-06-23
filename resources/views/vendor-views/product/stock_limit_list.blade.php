
@extends('layouts.vendor.app')

@section('title',translate('messages.stock_limit_list'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-filter-list"></i> {{translate('messages.stock_limit_list')}}<span class="badge badge-soft-dark ml-2" id="itemCount">{{$items->total()}}</span></h1>
                </div>
                @if ($toggle_veg_non_veg)
                <!-- Veg/NonVeg filter -->
                <div class="col-sm-auto mb-1 mb-sm-0">
                    <select name="category_id" onchange="set_filter('{{url()->full()}}',this.value, 'type')" data-placeholder="{{translate('messages.all')}}" class="form-control h--37px">
                        <option value="all" {{$type=='all'?'selected':''}}>{{translate('messages.all')}}</option>
                        <option value="veg" {{$type=='veg'?'selected':''}}>{{translate('messages.veg')}}</option>
                        <option value="non_veg" {{$type=='non_veg'?'selected':''}}>{{translate('messages.non_veg')}}</option>
                    </select>
                </div>
                <!-- End Veg/NonVeg filter -->
                @endif
                <div class="col-sm-auto">
                    <a href="{{route('vendor.item.add-new')}}" class="btn btn--primary m-0 pull-right h--37px d-flex align-items-center justify-content-center"><i class="tio-add-circle"></i> {{translate('messages.add')}} {{translate('messages.new')}} {{translate('messages.item')}}</a>
                </div>

            </div>
        </div>
        <!-- End Page Header -->
        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header py-2  border-0">
                <div class="search--button-wrapper justify-content-end">
                    <form id="search-form" class="search-form">
                        @csrf
                        <!-- Search -->
                        <div class="input-group input--group">
                            <input id="datatableSearch" type="search" name="search" class="form-control" placeholder="{{translate('messages.ex_search_name')}}" aria-label="{{translate('messages.search_here')}}">
                            <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                        </div>
                        <!-- End Search -->
                    </form>
                    <!-- Unfold -->
                    <div class="hs-unfold mr-2 min--250">
                        <select name="category_id" id="category" onchange="set_filter('{{url()->full()}}',this.value, 'category_id')" data-placeholder="{{translate('messages.select_category')}}" class="js-data-example-ajax form-control">
                            @if($category)
                                <option value="{{$category->id}}" selected>{{$category->name}} ({{$category->position == 0?translate('messages.main'):translate('messages.sub')}})</option>
                            @else
                                <option value="all" selected>{{translate('messages.all_categories')}}</option>
                            @endif
                        </select>
                    </div>
                    <!-- End Unfold -->

                    <!-- Unfold -->
                    <div class="hs-unfold d-none">
                        {{-- <a class="js-hs-unfold-invoker btn btn-white" href="javascript:;"
                            data-hs-unfold-options='{
                            "target": "#showHideDropdown",
                            "type": "css-animation"
                            }'>
                            <i class="tio-table mr-1"></i> Columns <span class="badge badge-soft-dark rounded-circle ml-1">6</span>
                        </a> --}}

                        <div id="showHideDropdown" class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right dropdown-card">
                            <div class="card card-sm">
                                <div class="card-body">
                                    {{--<div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="mr-2">#</span>
                                        <!-- Checkbox Switch -->
                                        <label class="toggle-switch toggle-switch-sm" for="toggleColumn_index">
                                            <input type="checkbox" class="toggle-switch-input" id="toggleColumn_index" checked>
                                            <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    <!-- End Checkbox Switch -->
                                    </div>--}}
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="mr-2">{{translate('messages.name')}}</span>
                                        <!-- Checkbox Switch -->
                                        <label class="toggle-switch toggle-switch-sm" for="toggleColumn_name">
                                            <input type="checkbox" class="toggle-switch-input" id="toggleColumn_name" checked>
                                            <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    <!-- End Checkbox Switch -->
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="mr-2">{{translate('messages.category')}}</span>

                                        <!-- Checkbox Switch -->
                                        <label class="toggle-switch toggle-switch-sm" for="toggleColumn_type">
                                            <input type="checkbox" class="toggle-switch-input" id="toggleColumn_type" checked>
                                            <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    <!-- End Checkbox Switch -->
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="mr-2">{{translate('messages.price')}}</span>

                                        <!-- Checkbox Switch -->
                                        <label class="toggle-switch toggle-switch-sm" for="toggleColumn_status">
                                            <input type="checkbox" class="toggle-switch-input" id="toggleColumn_status" checked>
                                            <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                        <!-- End Checkbox Switch -->
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="mr-2">{{translate('messages.stock')}}</span>

                                        <!-- Checkbox Switch -->
                                        <label class="toggle-switch toggle-switch-sm" for="toggleColumn_price">
                                            <input type="checkbox" class="toggle-switch-input" id="toggleColumn_price" checked>
                                            <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                        <!-- End Checkbox Switch -->
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="mr-2">{{translate('messages.action')}}</span>

                                        <!-- Checkbox Switch -->
                                        <label class="toggle-switch toggle-switch-sm" for="toggleColumn_action">
                                            <input type="checkbox" class="toggle-switch-input" id="toggleColumn_action" checked>
                                            <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                        <!-- End Checkbox Switch -->
                                    </div>
                            </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Unfold -->
                </div>
            </div>
            <!-- End Header -->


            <!-- Table -->
            <div class="table-responsive datatable-custom">
                <table id="datatable" class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                    data-hs-datatables-options='{
                        "columnDefs": [{
                            "targets": [],
                            "width": "5%",
                            "orderable": false
                        }],
                        "order": [],
                        "info": {
                        "totalQty": "#datatableWithPaginationInfoTotalQty"
                        },

                        "entries": "#datatableEntries",
                        "isResponsive": false,
                        "isShowPaging": false,
                            "paging":false
                    }'>
                    <thead class="thead-light">
                    <tr>
                        <th class="border-0">{{translate('messages.#')}}</th>
                        <th class="border-0 w-20p">{{translate('messages.name')}}</th>
                        <th class="border-0 w-20p">{{translate('messages.category')}}</th>
                        <th class="border-0">{{translate('messages.price')}}</th>
                        <th class="border-0 text-center">{{translate('messages.stock')}}</th>
                        <th class="border-0 text-center">{{translate('messages.action')}}</th>
                    </tr>
                    </thead>

                    <tbody id="set-rows">
                    @foreach($items as $key=>$item)
                        <tr>
                            <td>{{$key+$items->firstItem()}}</td>
                            <td>
                                <a class="media align-items-center" href="{{route('vendor.item.view',[$item['id']])}}">
                                    <img class="avatar avatar-lg mr-3" src="{{asset('storage/app/public/product')}}/{{$item['image']}}"
                                            onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'" alt="{{$item->name}} image">
                                    <div class="media-body">
                                        <h5 class="text-hover-primary mb-0">{{Str::limit($item['name'],20,'...')}}</h5>
                                    </div>
                                </a>
                            </td>
                            <td>
                            {{Str::limit($item->category?$item->category->name:translate('messages.category_deleted'),20,'...')}}
                            </td>
                            <td>
                                {{\App\CentralLogics\Helpers::format_currency($item['price'])}}
                            </td>
                            <td>
                                {{-- <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$item->id}}">
                                    <input type="checkbox" onclick="location.href='{{route('vendor.item.status',[$item['id'],$item->status?0:1])}}'"class="toggle-switch-input" id="stocksCheckbox{{$item->id}}" {{$item->status?'checked':''}}>
                                    <span class="toggle-switch-label">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label> --}}
                                <div class=" text-center">
                                    {{($item['stock'])}}
                                </div>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="btn btn-sm btn--primary btn-outline-primary action-btn"
                                        href="javascript:" title="{{translate('messages.edit')}} {{translate('messages.quantity')}}" onclick="update_quantity({{ $item->id }})" data-toggle="modal" data-target="#update-quantity"><i class="tio-edit"></i>
                                    </a>
                                </div>
                                {{-- <a class="btn btn-sm btn--danger btn-outline-danger action-btn" href="javascript:"
                                    onclick="form_alert('food-{{$item['id']}}','Want to delete this item ?')" title="{{translate('messages.delete')}} {{translate('messages.item')}}"><i class="tio-delete-outlined"></i>
                                </a> --}}
                                {{-- <form action="{{route('vendor.item.delete',[$item['id']])}}"
                                        method="post" id="food-{{$item['id']}}">
                                    @csrf @method('delete')
                                </form> --}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <hr>
                <div class="page-area">
                    <table>
                        <tfoot class="border-top">
                        {!! $items->links() !!}
                        </tfoot>
                    </table>
                </div>
                @if(count($items) === 0)
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


<div class="modal fade" id="update-quantity" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body py-2">
                <form action="{{route('vendor.item.stock-update')}}" method="post">
                    @csrf
                    <div class="mt-2 rest-part w-100"></div>
                    <div class="btn--container justify-content-end">
                        <button type="button" class="btn btn--danger" data-dismiss="modal" aria-label="Close">
                            {{translate('messages.close')}}
                        </button>
                        <button class="btn btn--primary" type="submit">{{translate('messages.update')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



@push('script_2')

<script>
    function update_quantity(val) {

        $.get({
            url: '{{url('/')}}/store-panel/item/get-variations?id='+val,
            dataType: 'json',
            success: function (data) {
               console.log(data);
                $('.rest-part').empty().html(data.view);
            },
        });
    }

    function update_qty() {
            var total_qty = 0;
            var qty_elements = $('input[name^="stock_"]');
            for (var i = 0; i < qty_elements.length; i++) {
                total_qty += parseInt(qty_elements.eq(i).val());
            }
            if(qty_elements.length > 0)
            {

                $('input[name="current_stock"]').attr("readonly", 'readonly');
                $('input[name="current_stock"]').val(total_qty);
            }
            else{
                $('input[name="current_stock"]').attr("readonly", false);
            }
        }

</script>

    <script>
        $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#datatable'), {
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

        $('#toggleColumn_index').change(function (e) {
          datatable.columns(0).visible(e.target.checked)
        })
        $('#toggleColumn_name').change(function (e) {
          datatable.columns(1).visible(e.target.checked)
        })

        $('#toggleColumn_type').change(function (e) {
          datatable.columns(2).visible(e.target.checked)
        })

        $('#toggleColumn_status').change(function (e) {
          datatable.columns(4).visible(e.target.checked)
        })
        $('#toggleColumn_price').change(function (e) {
          datatable.columns(3).visible(e.target.checked)
        })
        $('#toggleColumn_action').change(function (e) {
          datatable.columns(5).visible(e.target.checked)
        })


            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });

        $('#category').select2({
            ajax: {
                url: '{{route("vendor.category.get-all")}}',
                data: function (params) {
                    return {
                        q: params.term, // search term
                        all:true,
                        page: params.page
                    };
                },
                processResults: function (data) {
                    return {
                    results: data
                    };
                },
                __port: function (params, success, failure) {
                    var $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
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
                url: '{{route('vendor.item.search')}}',
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
