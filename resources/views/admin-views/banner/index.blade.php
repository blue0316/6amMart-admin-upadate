@extends('layouts.admin.app')

@section('title',translate('messages.banner'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/banner.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.add')}} {{translate('messages.new')}} {{translate('messages.banner')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.banner.store')}}" method="post" id="banner_form">
                            @csrf
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.title')}}</label>
                                        <input type="text" name="title" class="form-control" placeholder="{{translate('messages.new_banner')}}" required>
                                    </div>
                                {{-- 
                                    <div class="form-group">
                                        <label class="input-label">{{translate('messages.module')}}</label>
                                        <select name="module_id" required
                                                class="form-control js-select2-custom"  data-placeholder="{{translate('messages.select')}} {{translate('messages.module')}}" id="module_select">
                                                <option value="" selected disabled>{{translate('messages.select')}} {{translate('messages.module')}}</option>
                                            @foreach(\App\Models\Module::notParcel()->get() as $module)
                                                <option value="{{$module->id}}">{{$module->module_name}}</option>
                                            @endforeach
                                        </select>
                                    </div> --}}

                                    <div class="form-group">
                                        <label class="input-label" for="title">{{translate('messages.zone')}}</label>
                                        <select name="zone_id" id="zone" class="form-control js-select2-custom">
                                            <option disabled selected>---{{translate('messages.select')}}---</option>
                                            @php($zones=\App\Models\Zone::active()->get())
                                            @foreach($zones as $zone)
                                                @if(isset(auth('admin')->user()->zone_id))
                                                    @if(auth('admin')->user()->zone_id == $zone->id)
                                                        <option value="{{$zone->id}}" selected>{{$zone->name}}</option>
                                                    @endif
                                                @else
                                                    <option value="{{$zone['id']}}">{{$zone['name']}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.banner')}} {{translate('messages.type')}}</label>
                                        <select name="banner_type" id="banner_type" class="form-control" onchange="banner_type_change(this.value)">
                                            <option value="store_wise">{{translate('messages.store')}} {{translate('messages.wise')}}</option>
                                            <option value="item_wise">{{translate('messages.item')}} {{translate('messages.wise')}}</option>
                                            <option value="default">{{translate('messages.default')}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-0" id="store_wise">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.store')}}<span
                                                class="input-label-secondary"></span></label>
                                        <select name="store_id" id="store_id" class="js-data-example-ajax form-control"  title="Select Restaurant">

                                        </select>
                                    </div>
                                    <div class="form-group mb-0" id="item_wise">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.select')}} {{translate('messages.item')}}</label>
                                        <select name="item_id" id="choice_item" class="form-control js-select2-custom" placeholder="{{translate('messages.select_item')}}">

                                        </select>
                                    </div>
                                    <div class="form-group mb-0" id="default">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.default_link')}}({{ translate('messages.optional') }})</label>
                                        <input type="text" name="default_link" class="form-control" placeholder="{{translate('messages.default_link')}}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="h-100 d-flex flex-column">
                                        <label class="mt-auto mb-0 d-block text-center">{{translate('messages.campaign')}} {{translate('messages.image')}} <small class="text-danger">* ( {{translate('messages.ratio')}} 3:1 )</small></label>
                                        <center class="py-3 my-auto">
                                            <img class="img--vertical" id="viewer"
                                                src="{{asset('public/assets/admin/img/900x400/img1.jpg')}}" alt="campaign image"/>
                                        </center>
                                        <div class="custom-file">
                                            <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                                            <label class="custom-file-label" for="customFileEg1">{{translate('messages.choose')}} {{translate('messages.file')}}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <div class="btn--container justify-content-end">
                                        <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('messages.reset')}}</button>
                                        <button type="submit" class="btn btn--primary">{{translate('messages.submit')}}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-header py-2 border-0">
                        <div class="search--button-wrapper">
                            <h5 class="card-title">
                                {{translate('messages.banner')}} {{translate('messages.list')}}<span class="badge badge-soft-dark ml-2" id="itemCount">{{$banners->count()}}</span>
                            </h5>
                            <form id="search-form" class="search-form">
                                @csrf
                                <!-- Search -->
                                <div class="input-group input--group">
                                    <input id="datatableSearch" type="search" name="search" class="form-control" placeholder="{{translate('messages.search_by_title')}}" aria-label="{{translate('messages.search_here')}}">
                                    <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                                </div>
                                <!-- End Search -->
                            </form>
                        </div>
                    </div>
                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                               class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                               data-hs-datatables-options='{
                                "order": [],
                                "orderCellsTop": true,
                                "search": "#datatableSearch",
                                "entries": "#datatableEntries",
                                "isResponsive": false,
                                "isShowPaging": false,
                                "paging": false
                               }'
                               >
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-0">{{ translate('messages.SL') }}</th>
                                    <th class="border-0">{{translate('messages.title')}}</th>
                                    <th class="border-0">{{translate('messages.module')}}</th>
                                    <th class="border-0">{{translate('messages.type')}}</th>
                                    <th class="border-0 text-center">{{translate('messages.featured')}}</th>
                                    <th class="border-0 text-center">{{translate('messages.status')}}</th>
                                    <th class="border-0 text-center">{{translate('messages.action')}}</th>
                                </tr>
                            </thead>

                            <tbody id="set-rows">
                            @foreach($banners as $key=>$banner)
                                <tr>
                                    <td>{{$key+$banners->firstItem()}}</td>
                                    <td>
                                        <span class="media align-items-center">
                                            <img class="img--ratio-3 w-auto h--50px rounded mr-2" src="{{asset('storage/app/public/banner')}}/{{$banner['image']}}"
                                                 onerror="this.src='{{asset('/public/assets/admin/img/900x400/img1.jpg')}}'" alt="{{$banner->name}} image">
                                            <div class="media-body">
                                                <h5 class="text-hover-primary mb-0">{{Str::limit($banner['title'], 25, '...')}}</h5>
                                            </div>
                                        </span>
                                    <span class="d-block font-size-sm text-body">

                                    </span>
                                    </td>
                                    <td>{{Str::limit($banner->module->module_name, 15, '...')}}</td>
                                    <td>{{translate('messages.'.$banner['type'])}}</td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <label class="toggle-switch toggle-switch-sm" for="featuredCheckbox{{$banner->id}}">
                                                <input type="checkbox" onclick="location.href='{{route('admin.banner.featured',[$banner['id'],$banner->featured?0:1])}}'" class="toggle-switch-input" id="featuredCheckbox{{$banner->id}}" {{$banner->featured?'checked':''}}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <label class="toggle-switch toggle-switch-sm" for="statusCheckbox{{$banner->id}}">
                                            <input type="checkbox" onclick="location.href='{{route('admin.banner.status',[$banner['id'],$banner->status?0:1])}}'" class="toggle-switch-input" id="statusCheckbox{{$banner->id}}" {{$banner->status?'checked':''}}>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="btn action-btn btn--primary btn-outline-primary" href="{{route('admin.banner.edit',[$banner['id']])}}"title="{{translate('messages.edit')}} {{translate('messages.banner')}}"><i class="tio-edit"></i>
                                            </a>
                                            <a class="btn action-btn btn--danger btn-outline-danger" href="javascript:" onclick="form_alert('banner-{{$banner['id']}}','{{ translate('Want to delete this banner ?') }}')" title="{{translate('messages.delete')}} {{translate('messages.banner')}}"><i class="tio-delete-outlined"></i>
                                            </a>
                                            <form action="{{route('admin.banner.delete',[$banner['id']])}}"
                                                        method="post" id="banner-{{$banner['id']}}">
                                                    @csrf @method('delete')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @if(count($banners) !== 0)
                        <hr>
                        @endif
                        <div class="page-area">
                            {!! $banners->links() !!}
                        </div>
                        @if(count($banners) === 0)
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
            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')
<script>

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#viewer').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#customFileEg1").change(function () {
        readURL(this);
    });
</script>
<script>
    var zone_id = [];
    var module_id = {{Config::get('module.current_module_id')}};

    function get_items()
    {
        var nurl = '{{url('/')}}/admin/item/get-items?module_id='+module_id;

        if(!Array.isArray(zone_id))
        {
            nurl += '&zone_id='+zone_id;
        }

        $.get({
            url: nurl,
            dataType: 'json',
            success: function (data) {
                $('#choice_item').empty().append(data.options);
            }
        });
    }
    $(document).on('ready', function () {

        module_id = {{Config::get('module.current_module_id')}};
        get_items();

        $('#zone').on('change', function(){
            if($(this).val())
            {
                zone_id = $(this).val();
                get_items();
            }
            else
            {
                zone_id = [];
            }
        });

        $('.js-data-example-ajax').select2({
            ajax: {
                url: '{{url('/')}}/admin/store/get-stores',
                data: function (params) {
                    return {
                        q: params.term, // search term
                        zone_ids: [zone_id],
                        page: params.page,
                        module_id: module_id
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
        $('#item_wise').hide();
        $('#default').hide();
        function banner_type_change(order_type) {
           if(order_type=='item_wise')
            {
                $('#store_wise').hide();
                $('#item_wise').show();
                $('#default').hide();
            }
            else if(order_type=='store_wise')
            {
                $('#store_wise').show();
                $('#item_wise').hide();5
                $('#default').hide();
            }
            else if(order_type=='default')
            {
                $('#default').show();
                $('#store_wise').hide();
                $('#item_wise').hide();
            }
            else{
                $('#item_wise').hide();
                $('#store_wise').hide();
                $('#default').hide();
            }
        }

        $('#banner_form').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: "{{route('admin.banner.store')}}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data.errors) {
                        for (var i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        toastr.success('{{translate("messages.banner_added_successfully")}}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function () {
                            location.href = '{{route("admin.banner.add-new")}}';
                        }, 2000);
                    }
                }
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
                url: '{{route('admin.banner.search')}}',
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
        <script>
            $('#reset_btn').click(function(){
                $('#module_select').val(null).trigger('change');
                $('#zone').val(null).trigger('change');
                $('#store_id').val(null).trigger('change');
                $('#choice_item').val(null).trigger('change');
                $('#viewer').attr('src','{{asset('public/assets/admin/img/900x400/img1.jpg')}}');
            })
        </script>
@endpush
