@extends('layouts.admin.app')

@section('title',translate('messages.parcel_category'))


@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/parcel.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.parcel_category')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->

        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.parcel.category.store')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                    @php($language=\App\Models\BusinessSetting::where('key','language')->first())
                    @php($language = $language->value ?? null)
                    @php($default_lang = 'en')
                    @if($language)
                    <div class="col-12">
                        @php($default_lang = json_decode($language)[0])
                        <ul class="nav nav-tabs mb-3 border-0">
                            @foreach(json_decode($language) as $lang)
                                <li class="nav-item">
                                    <a class="nav-link lang_link {{$lang == $default_lang? 'active':''}}" href="#" id="{{$lang}}-link">{{\App\CentralLogics\Helpers::get_language_name($lang).'('.strtoupper($lang).')'}}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="col-md-6">
                        @if ($language)
                            @foreach(json_decode($language) as $lang)
                                <div class="{{$lang != $default_lang ? 'd-none':''}} lang_form" id="{{$lang}}-form">
                                    <div class="form-group">
                                        <label class="input-label" for="{{$lang}}_name">{{translate('messages.name')}} ({{strtoupper($lang)}})</label>
                                        <input type="text" name="name[]" id="{{$lang}}_name" class="form-control" placeholder="{{translate('messages.new_item')}}" {{$lang == $default_lang? 'required':''}} oninvalid="document.getElementById('en-link').click()">
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{$lang}}">
                                    <div class="form-group">
                                        <label class="input-label" for="description">{{translate('messages.short')}} {{translate('messages.description')}} ({{strtoupper($lang)}})</label>
                                        <textarea type="text" name="description[]" class="form-control ckeditor" {{$lang == $default_lang? 'required':''}} oninvalid="document.getElementById('en-link').click()"></textarea>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div id="{{$default_lang}}-form">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('messages.name')}} (EN)</label>
                                    <input type="text" name="name[]" class="form-control" placeholder="{{translate('messages.new_item')}}" required>
                                </div>
                                <input type="hidden" name="lang[]" value="en">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('messages.short')}} {{translate('messages.description')}}</label>
                                    <textarea type="text" name="description[]" class="form-control ckeditor"></textarea>
                                </div>
                            </div>
                        @endif
                        {{-- <div class="form-group mb-0">
                            <label class="input-label">{{translate('messages.module')}}</label>
                            <select name="module_id" id="module_id" required class="form-control js-select2-custom"  data-placeholder="{{translate('messages.select')}} {{translate('messages.module')}}">
                                    <option value="" selected disabled>{{translate('messages.select')}} {{translate('messages.module')}}</option>
                                @foreach(\App\Models\Module::parcel()->get() as $module)
                                    <option value="{{$module->id}}" >{{$module->module_name}}</option>
                                @endforeach
                            </select>
                        </div> --}}
                        <input name="position" value="0" class="initial-hidden">
                    </div>
                    <div class="col-md-6">
                        <div class="h-100 d-flex flex-column">
                            <label class="text-center d-block mt-auto">
                                {{translate('messages.image')}}
                                <small class="text-danger">* ( {{translate('messages.ratio')}} 200x200)</small>
                            </label>
                            <center class="py-3 my-auto">
                                <img class="img--120" id="viewer"
                                    src="{{asset('public/assets/admin/img/900x400/img1.jpg')}}"
                                    alt="image"/>
                            </center>
                            <div class="custom-file">
                                <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                    accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                                <label class="custom-file-label" for="customFileEg1">{{translate('messages.choose')}} {{translate('messages.file')}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label  class="input-label text-capitalize">{{translate('messages.per_km_shipping_charge')}}</label>
                            <input type="number" step=".01" min="0" placeholder="{{translate('messages.per_km_shipping_charge')}}" class="form-control" name="parcel_per_km_shipping_charge">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-label text-capitalize">{{translate('messages.minimum_shipping_charge')}}</label>
                            <input type="number" step=".01" min="0" placeholder="{{translate('messages.minimum_shipping_charge')}}" class="form-control" name="parcel_minimum_shipping_charge">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="btn--container justify-content-end">
                            <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('messages.reset')}}</button>
                            <button type="submit" class="btn btn--primary">{{translate('messages.Add Parcel Category')}}</button>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header py-2 border-0">
                <div class="search--button-wrapper">
                    <h5 class="card-title">
                        {{translate('messages.parcel_category')}} {{translate('messages.list')}}
                        <span class="badge badge-soft-dark ml-2" id="itemCount">{{$parcel_categories->total()}}</span>
                    </h5>
                    {{-- <div class="min--240">
                        <select name="module_id" class="form-control js-select2-custom" onchange="set_filter('{{url()->full()}}',this.value,'module_id')" title="{{translate('messages.select')}} {{translate('messages.modules')}}">
                            <option value="" {{!request('module_id') ? 'selected':''}}>{{translate('messages.all')}} {{translate('messages.modules')}}</option>
                            @foreach (\App\Models\Module::Parcel()->get() as $module)
                                <option
                                    value="{{$module->id}}" {{request('module_id') == $module->id?'selected':''}}>
                                    {{$module['module_name']}}
                                </option>
                            @endforeach
                        </select>
                    </div> --}}
                </div>

                {{--<form id="dataSearch" class="col">
                    @csrf
                    <!-- Search -->
                    <div class="input-group input-group-merge input-group-flush">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="tio-search"></i>
                            </div>
                        </div>
                        <input type="search" name="search" class="form-control" placeholder="{{translate('messages.search_categories')}}" aria-label="{{translate('messages.search_categories')}}">
                        <button type="submit" class="btn btn-light">{{translate('messages.search')}}</button>
                    </div>
                    <!-- End Search -->
                </form>--}}
            </div>
            <div class="card-body p-0">
                <div class="table-responsive datatable-custom">
                    <table id="columnSearchDatatable"
                        class="table table-borderless table-thead-bordered table-align-middle" data-hs-datatables-options='{
                            "isResponsive": false,
                            "isShowPaging": false,
                            "paging":false,
                        }'>
                        <thead class="thead-light">
                            <tr>
                                <th class="border-0">{{ translate('messages.SL') }}</th>
                                <th class="border-0">{{translate('messages.id')}}</th>
                                <th class="border-0">{{translate('messages.name')}}</th>
                                <th class="border-0">{{translate('messages.module')}}</th>
                                <th class="border-0">{{translate('messages.status')}}</th>
                                <th class="border-0 text-center">{{translate('messages.orders')}} {{translate('messages.count')}}</th>
                                <th class="border-0 text-center">{{translate('messages.per_km_shipping_charge')}}</th>
                                <th class="border-0 text-center">{{translate('messages.minimum_shipping_charge')}}</th>
                                <th class="border-0 text-center">{{translate('messages.action')}}</th>
                            </tr>
                        </thead>

                        <tbody id="table-div">
                        @foreach($parcel_categories as $key=>$category)
                            <tr>
                                <td>{{$key+$parcel_categories->firstItem()}}</td>
                                <td>{{$category->id}}</td>
                                <td>
                                    <span class="d-block font-size-sm text-body">
                                        {{Str::limit($category['name'], 20,'...')}}
                                    </span>
                                </td>
                                <td>
                                    <span class="d-block font-size-sm text-body">
                                        {{Str::limit($category->module->module_name, 15,'...')}}
                                    </span>
                                </td>
                                <td>
                                    <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$category->id}}">
                                    <input type="checkbox" onclick="location.href='{{route('admin.parcel.category.status',[$category['id'],$category->status?0:1])}}'"class="toggle-switch-input" id="stocksCheckbox{{$category->id}}" {{$category->status?'checked':''}}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </td>
                                <td>
                                    <div class="text-center">
                                        {{$category->orders_count}}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-center">
                                        {{$category->parcel_per_km_shipping_charge?\App\CentralLogics\Helpers::format_currency($category->parcel_per_km_shipping_charge): 'N/A'}}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-center">
                                        {{$category->parcel_minimum_shipping_charge?\App\CentralLogics\Helpers::format_currency($category->parcel_minimum_shipping_charge): 'N/A'}}
                                    </div>
                                </td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn action-btn btn--primary btn-outline-primary"
                                            href="{{route('admin.parcel.category.edit',[$category['id']])}}" title="{{translate('messages.edit')}} {{translate('messages.category')}}"><i class="tio-edit"></i>
                                        </a>
                                        <a class="btn action-btn btn--danger btn-outline-danger" href="javascript:"
                                        onclick="form_alert('category-{{$category['id']}}','{{ translate('Want to delete this category') }}')" title="{{translate('messages.delete')}} {{translate('messages.category')}}"><i class="tio-delete-outlined"></i>
                                        </a>
                                        <form action="{{route('admin.parcel.category.destroy',[$category['id']])}}" method="post" id="category-{{$category['id']}}">
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
            @if(count($parcel_categories) !== 0)
            <hr>
            @endif
            <div class="page-area">
                {!! $parcel_categories->links() !!}
            </div>
            @if(count($parcel_categories) === 0)
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


            $('#dataSearch').on('submit', function (e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.post({
                    url: '{{route('admin.category.search')}}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        $('#loading').show();
                    },
                    success: function (data) {
                        $('#table-div').html(data.view);
                        $('#itemCount').html(data.count);
                        $('.page-area').hide();
                    },
                    complete: function () {
                        $('#loading').hide();
                    },
                });
            });


            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
    </script>

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
    </script>
    <script>
        $('#reset_btn').click(function(){
            $('#module_id').val(null).trigger('change');
            $('#viewer').attr('src', "{{asset('public/assets/admin/img/900x400/img1.jpg')}}");
        })
    </script>
@endpush
