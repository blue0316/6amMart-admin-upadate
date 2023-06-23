@extends('layouts.admin.app')

@section('title',translate('messages.add_new_addon'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/addon.png')}}" class="w--20" alt="">
                </span>
                <span>
                    {{translate('messages.add').' '.translate('messages.new')}} {{translate('messages.addon')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        @php($language=\App\Models\BusinessSetting::where('key','language')->first())
        @php($language = $language->value ?? null)
        @php($default_lang = 'en')
        <div class="card">
            <div class="card-body">
                <form action="{{isset($addon)?route('admin.addon.update',[$addon['id']]):route('admin.addon.store')}}" method="post">
                    @csrf
                    @if($language)
                        @php($default_lang = json_decode($language)[0])
                        <ul class="nav nav-tabs mb-4">
                            @foreach(json_decode($language) as $lang)
                                <li class="nav-item">
                                    <a class="nav-link lang_link {{$lang == $default_lang? 'active':''}}" href="#" id="{{$lang}}-link">{{\App\CentralLogics\Helpers::get_language_name($lang).'('.strtoupper($lang).')'}}</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    <div class="row">
                        <div class="col-sm-6 col-lg-4">
                        @if ($language)
                            @foreach(json_decode($language) as $lang)
                                <div class="form-group {{$lang != $default_lang ? 'd-none':''}} lang_form" id="{{$lang}}-form">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('messages.name')}} ({{strtoupper($lang)}})</label>
                                    <input type="text" name="name[]" class="form-control" placeholder="{{translate('messages.new_addon')}}" maxlength="191" {{$lang == $default_lang? 'required':''}} oninvalid="document.getElementById('en-link').click()">
                                </div>
                                <input type="hidden" name="lang[]" value="{{$lang}}">
                            @endforeach
                        @else
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.name')}}</label>
                                <input type="text" name="name" class="form-control" placeholder="{{translate('messages.new_addon')}}" value="{{old('name')}}" required maxlength="191">
                            </div>
                            <input type="hidden" name="lang[]" value="{{$lang}}">
                        @endif
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.store')}}<span
                                        class="input-label-secondary"></span></label>
                                <select name="store_id" id="store_id" class="js-data-example-ajax form-control"  data-placeholder="{{translate('messages.select')}} {{translate('messages.store')}}" oninvalid="this.setCustomValidity('{{translate('messages.please_select_store')}}')">

                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.price')}}</label>
                                <input type="number" min="0" max="999999999999.99" name="price" step="0.01" value="{{old('price')}}" class="form-control" placeholder="100" required>
                            </div>
                        </div>
                    </div>

                    <div class="btn--container justify-content-end">
                        <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('messages.reset')}}</button>
                        <button type="submit" class="btn btn--primary">{{isset($addon)?translate('messages.update'):translate('messages.add')}}</button>
                    </div>

                </form>
            </div>
        </div>

        <div class="card mt-1">
            <div class="card-header py-2 border-0">
                <div class="search--button-wrapper justify-content-end">
                    <h5 class="card-title"> {{translate('messages.addon')}} {{translate('messages.list')}}<span class="badge badge-soft-dark ml-2" id="itemCount">{{$addons->total()}}</span></h5>
                    <div class="min--220">
                        <select name="store_id" id="store" onchange="set_store_filter('{{route('admin.addon.add-new')}}',this.value)" data-placeholder="{{translate('messages.select')}} {{translate('messages.store')}}" class="js-data-example-ajax form-control"   title="Select Restaurant">
                            @if(isset($store))
                            <option value="{{$store->id}}" selected>{{$store->name}}</option>
                            @else
                            <option value="all" selected>{{translate('messages.all_stores')}}</option>
                            @endif
                        </select>
                    </div>
                    <div>
                        <form id="search-form" class="search-form">
                            @csrf
                            <!-- Search -->
                            <div class="input-group input--group">
                                <input id="datatableSearch" type="search" name="search" class="form-control min-height-45" placeholder="{{translate('messages.ex_:_addons_name')}}" aria-label="Search addons">
                                <button type="submit" class="btn btn--secondary min-height-45"><i class="tio-search"></i></button>
                            </div>
                            <!-- End Search -->
                        </form>
                    </div>
                    <div class="hs-unfold">
                        <a class="js-hs-unfold-invoker btn btn-white min-height-45" href="javascript:;"
                            data-hs-unfold-options='{
                            "target": "#showHideDropdown",
                            "type": "css-animation"
                            }'>
                            <i class="tio-table mr-1"></i> {{translate('messages.columns')}} <span class="badge badge-soft-dark rounded-circle ml-1">5</span>
                        </a>

                        <div id="showHideDropdown" class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right dropdown-card min--240">
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
                                        <span class="mr-2">{{translate('messages.price')}}</span>
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
                                        <span class="mr-2">{{translate('messages.store')}}</span>

                                        <!-- Checkbox Switch -->
                                        <label class="toggle-switch toggle-switch-sm" for="toggleColumn_vendor">
                                            <input type="checkbox" class="toggle-switch-input" id="toggleColumn_vendor" checked>
                                            <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                        <!-- End Checkbox Switch -->
                                    </div>


                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="mr-2">{{translate('messages.status')}}</span>

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
                        <!-- End Unfold -->
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive datatable-custom">
                    <table id="datatable" class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"  data-hs-datatables-options='{
                        "search": "#datatableSearch",
                        "entries": "#datatableEntries",
                        "isResponsive": false,
                        "isShowPaging": false,
                        "paging":false
                            }'>
                        <thead class="thead-light">
                        <tr>
                            <th>{{translate('sl')}}</th>
                            <th>{{translate('messages.name')}}</th>
                            <th>{{translate('messages.price')}}</th>
                            <th>{{translate('messages.store')}}</th>
                            <th class="text-center">{{translate('messages.status')}}</th>
                            <th class="text-center">{{translate('messages.action')}}</th>
                        </tr>
                        </thead>

                        <tbody id="set-rows">
                        @foreach($addons as $key=>$addon)
                            <tr>
                                <td>{{$key+ $addons->firstItem()}}</td>
                                <td>
                                <span class="d-block font-size-sm text-body">
                                    {{Str::limit($addon['name'],20,'...')}}
                                </span>
                                </td>
                                <td>{{\App\CentralLogics\Helpers::format_currency($addon['price'])}}</td>
                                <td>{{Str::limit($addon->store?$addon->store->name:translate('messages.store').' '.translate('messages.deleted'),25,'...')}}</td>
                                <td>
                                    <label class="toggle-switch toggle-switch-sm" for="stausCheckbox{{$addon->id}}">
                                    <input type="checkbox" onclick="location.href='{{route('admin.addon.status',[$addon['id'],$addon->status?0:1])}}'"class="toggle-switch-input" id="stausCheckbox{{$addon->id}}" {{$addon->status?'checked':''}}>
                                        <span class="toggle-switch-label mx-auto">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn action-btn btn--primary btn-outline-primary"
                                            href="{{route('admin.addon.edit',[$addon['id']])}}" title="{{translate('messages.edit')}} {{translate('messages.addon')}}"><i class="tio-edit"></i></a>
                                        <a class="btn action-btn btn--danger btn-outline-danger"     href="javascript:"
                                            onclick="form_alert('addon-{{$addon['id']}}','{{ translate('Want to delete this addon ?') }}')" title="{{translate('messages.delete')}} {{translate('messages.addon')}}"><i class="tio-delete-outlined"></i></a>
                                        <form action="{{route('admin.addon.delete',[$addon['id']])}}"
                                                    method="post" id="addon-{{$addon['id']}}">
                                            @csrf @method('delete')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if(count($addons) !== 0)
            <hr>
            @endif
            <div class="page-area">
                {!! $addons->links() !!}
            </div>
            @if(count($addons) === 0)
            <div class="empty--data">
                <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                <h5>
                    {{translate('no_data_found')}}
                </h5>
            </div>
            @endif
        </div>
    </div>

@endsection

@push('script_2')
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
                '<img class="mb-3 w-7rem" src="{{asset('public/assets/admin/svg/illustrations/sorry.svg')}}" alt="Image Description">' +

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

        $('#toggleColumn_vendor').change(function (e) {
          datatable.columns(3).visible(e.target.checked)
        })

        $('#toggleColumn_status').change(function (e) {
          datatable.columns(4).visible(e.target.checked)
        })
        $('#toggleColumn_price').change(function (e) {
          datatable.columns(2).visible(e.target.checked)
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

        $('#store').select2({
            ajax: {
                url: '{{url('/')}}/admin/store/get-stores',
                data: function (params) {
                    return {
                        q: params.term, // search term
                        all:true,
                        module_type:'food',
                        module_id:{{Config::get('module.current_module_id')}},
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

        $('#store_id').select2({
            ajax: {
                url: '{{url('/')}}/admin/store/get-stores',
                data: function (params) {
                    return {
                        q: params.term, // search term
                        module_type:'food',
                        module_id:{{Config::get('module.current_module_id')}},
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


        $('#search-form').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.addon.search')}}',
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
    <script>
        $(".lang_link").click(function(e){
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.substring(0, form_id.length - 5);
            console.log(lang);
            $("#"+lang+"-form").removeClass('d-none');
            if(lang == '{{$default_lang}}')
            {
                $(".from_part_2").removeClass('d-none');
            }
            else
            {
                $(".from_part_2").addClass('d-none');
            }
        });

        $('#reset_btn').click(function(){
            $('#store_id').val(null).trigger('change');
        })
    </script>
@endpush