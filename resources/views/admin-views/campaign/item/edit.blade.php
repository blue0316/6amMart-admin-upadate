@extends('layouts.admin.app')

@section('title','Update campaign')

@push('css_or_js')
    <link href="{{asset('public/assets/admin/css/tags-input.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/edit.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.update')}} {{translate('messages.campaign')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <form action="javascript:" method="post" id="campaign_form"
                enctype="multipart/form-data">
                <div class="row g-2">
                @csrf
                @php($language=\App\Models\BusinessSetting::where('key','language')->first())
                @php($language = $language->value ?? null)
                @php($default_lang = 'bn')
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
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon">
                                    <i class="tio-fastfood"></i>
                                </span>
                                <span>{{ translate('messages.Item Info') }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($language)
                            @foreach(json_decode($language) as $lang)
                                <?php
                                    if(count($campaign['translations'])){
                                        $translate = [];
                                        foreach($campaign['translations'] as $t)
                                        {
                                            if($t->locale == $lang && $t->key=="title"){
                                                $translate[$lang]['title'] = $t->value;
                                            }
                                            if($t->locale == $lang && $t->key=="description"){
                                                $translate[$lang]['description'] = $t->value;
                                            }
                                        }
                                    }
                                ?>
                                <div class="{{$lang != $default_lang ? 'd-none':''}} lang_form" id="{{$lang}}-form">
                                    <div class="form-group">
                                        <label class="input-label" for="{{$lang}}_title">{{translate('messages.title')}} ({{strtoupper($lang)}})</label>
                                        <input type="text" {{$lang == $default_lang? 'required':''}} name="title[]" id="{{$lang}}_title" class="form-control" placeholder="{{translate('messages.new_campaign')}}" value="{{$translate[$lang]['title']??$campaign['title']}}" oninvalid="document.getElementById('en-link').click()">
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{$lang}}">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.short')}} {{translate('messages.description')}} ({{strtoupper($lang)}})</label>
                                        <textarea type="text" name="description[]" class="form-control ckeditor">{!! $translate[$lang]['description']??$campaign['description'] !!}</textarea>
                                    </div>
                                </div>
                            @endforeach
                            @else
                                <div id="{{$default_lang}}-form">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.title')}} (EN)</label>
                                        <input type="text" name="title[]" class="form-control" placeholder="{{translate('messages.new_campaign')}}" value="{{$campaign['title']}}" required>
                                    </div>
                                    <input type="hidden" name="lang[]" value="en">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.short')}} {{translate('messages.description')}}</label>
                                        <textarea type="text" name="description[]" class="form-control ckeditor">{!! $campaign['description'] !!}</textarea>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon">
                                    <i class="tio-comment-image-outlined"></i>
                                </span>
                                <span>{{ translate('messages.Item Image') }}</span>
                            </h5>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <label>
                                {{translate('messages.item')}} {{translate('messages.image')}}
                                <small class="text-danger">* ( {{translate('messages.ratio')}} 1:1 )</small>
                            </label>

                            <center id="image-viewer-section" class="py-3 my-auto">
                                <img class="img--120" id="viewer"
                                src="{{asset('storage/app/public/campaign')}}/{{$campaign->image}}" alt="campaign image" onerror='this.src="{{asset('public/assets/admin/img/100x100/2.png')}}"'/>
                            </center>
                            <div class="custom-file">
                                <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                        accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                <label class="custom-file-label" for="customFileEg1">{{translate('messages.choose')}} {{translate('messages.file')}}</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon">
                                    <i class="tio-dashboard-outlined"></i>
                                </span>
                                <span>{{ translate('messages.Item Details') }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label">{{translate('messages.module')}}</label>
                                        <select name="module_id" class="form-control js-select2-custom"  data-placeholder="{{translate('messages.select')}} {{translate('messages.module')}}" onchange="modulChange(this.value)" disabled>
                                                <option value="" selected disabled>{{translate('messages.select')}} {{translate('messages.module')}}</option>
                                            @foreach(\App\Models\Module::notParcel()->get() as $module)
                                                <option value="{{$module->id}}" {{$module->id == $campaign->module_id?'selected':''}}>{{$module->module_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.store')}}<span
                                                class="input-label-secondary"></span></label>
                                        <select name="store_id" class="js-data-example-ajax form-control" id="store_id" onchange="getStoreData('{{url('/')}}/admin/store/get-addons?data[]=0&store_id='+this.value,'add_on')"  data-toggle="tooltip" data-placement="right" data-original-title="{{translate('messages.select_store')}}" disabled>
                                            @if($campaign->store)
                                            <option value="{{$campaign->store->id}}" selected>{{$campaign->store->name}}</option>
                                            @else
                                            <option selected>{{translate('messages.select_store')}}</option>
                                            @endif
                                        </select>
                                    </div>

                                </div>
                                <div class="col-md-3 col-sm-6" id="stock_input">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="total_stock">{{translate('messages.total_stock')}}</label>
                                        <input type="number" class="form-control" name="current_stock" value="{{$campaign->stock}}" id="quantity">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6" id="addon_input">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.addon')}}<span
                                                class="input-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{translate('messages.store_required_warning')}}"><img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{translate('messages.store_required_warning')}}"></span></label>
                                        <select name="addon_ids[]" id="add_on" class="form-control js-select2-custom" multiple="multiple">
                                            @foreach(\App\Models\AddOn::orderBy('name')->get() as $addon)
                                                <option value="{{$addon['id']}}" {{in_array($addon->id,json_decode($campaign['add_ons'],true))?'selected':''}}>{{$addon['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.category')}}<span
                                                class="input-label-secondary">*</span></label>
                                        <select name="category_id" class="js-data-example-ajax form-control" id="category_id" onchange="categoryChange(this.value)">
                                            @if($category)
                                                <option value="{{$category['id']}}" >{{$category['name']}}</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.sub_category')}}<span
                                                class="input-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{translate('messages.category_required_warning')}}"><img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{translate('messages.category_required_warning')}}"></span></label>
                                        <select name="sub_category_id" class="js-data-example-ajax form-control" id="sub-categories">
                                            @if(isset($sub_category))
                                            <option value="{{$sub_category['id']}}" >{{$sub_category['name']}}</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 co-sml-6">
                                    <div class="form-group" id="veg_non_veg">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.item_type')}}</label>
                                        <select name="veg" class="form-control js-select2-custom">
                                            <option value="0" {{$campaign['veg']==0?'selected':''}}>{{translate('messages.non_veg')}}</option>
                                            <option value="1" {{$campaign['veg']==1?'selected':''}}>{{translate('messages.veg')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon"><i class="tio-dollar-outlined"></i></span>
                                <span>
                                    {{ translate('messages.amount') }}
                                </span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.price')}}</label>
                                        <input type="number" min="1" max="100000" step="0.01" value="{{$campaign->price}}" name="price" class="form-control"
                                                placeholder="{{ translate('messages.Ex:') }} 100" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.discount')}}<span class="input-label-secondary text--title" data-toggle="tooltip"
                                            data-placement="right"
                                            data-original-title="{{ translate('Currently you need to manage discount with store.') }}">
                                            <i class="tio-info-outined"></i>
                                        </span></label>
                                        <input type="number" min="0" max="100000" value="{{$campaign->discount}}" name="discount" class="form-control"
                                                placeholder="{{ translate('messages.Ex:') }} 100" >
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.discount')}} {{translate('messages.type')}}</label>
                                        <select name="discount_type" class="form-control js-select2-custom">
                                            <option value="percent" {{$campaign->discount_type == 'percent'?'selected':''}}>{{translate('messages.percent')}}</option>
                                            <option value="amount" {{$campaign->discount_type == 'amount'?'selected':''}}>{{translate('messages.amount')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 co-sml-6">
                                    <div class="form-group mb-0" id="unit_input">
                                        <label class="input-label text-capitalize" for="unit">{{translate('messages.unit')}}</label>
                                        <select name="unit" class="form-control js-select2-custom">
                                            @foreach (\App\Models\Unit::all() as $unit)
                                                <option value="{{$unit->id}}" {{$unit->id == $campaign->unit_id? 'selected':''}}>{{$unit->unit}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12" id="food_variation_section">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon">
                                    <i class="tio-canvas-text"></i>
                                </span>
                                <span> {{ translate('messages.food_variations') }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-md-12" >
                                    <div id="add_new_option">
                                    @if (isset($campaign->food_variations))
                                        @foreach (json_decode($campaign->food_variations,true) as $key_choice_options=>$item)
                                            @if (isset($item["price"]))
                                                @break
                                            @else
                                                @include('admin-views.product.partials._new_variations',['item'=>$item,'key'=>$key_choice_options+1])
                                            @endif
                                        @endforeach
                                    @endif
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-outline-success" id="add_new_option_button">{{translate('add_new_variation')}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12" id="attribute_section">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon">
                                    <i class="tio-canvas-text"></i>
                                </span>
                                <span>{{ translate('messages.Add Attribute') }}</span>
                            </h5>
                        </div>
                        <div class="card-body pb-0">
                            <div class="row g-2">
                                <div class="col-12">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.attribute')}}<span class="input-label-secondary"></span></label>
                                        <select name="attribute_id[]" id="choice_attributes" class="form-control js-select2-custom" multiple="multiple">
                                            @foreach(\App\Models\Attribute::orderBy('name')->get() as $attribute)
                                            <option value="{{$attribute['id']}}" {{in_array($attribute->id,json_decode($campaign['attributes'],true))?'selected':''}}>{{$attribute['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="customer_choice_options" id="customer_choice_options">
                                        @include('admin-views.product.partials._choices',['choice_no'=>json_decode($campaign['attributes']),'choice_options'=>json_decode($campaign['choice_options'],true)])
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="variant_combination" id="variant_combination">
                                        @include('admin-views.product.partials._edit-combinations',['combinations'=>json_decode($campaign['variations'],true),'stock'=>config('module.'.$campaign->module->module_type)['stock']])
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon"><i class="tio-date-range"></i></span>
                                <span>{{ translate('messages.time_schedule') }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label" for="title">{{translate('messages.start')}} {{translate('messages.date')}}</label>
                                        <input type="date" id="date_from" class="form-control" required="" name="start_date" value="{{$campaign->start_date->format('Y-m-d')}}">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label" for="title">{{translate('messages.end')}} {{translate('messages.date')}}</label>
                                        <input type="date" id="date_to" class="form-control" required="" name="end_date" value="{{$campaign->end_date->format('Y-m-d')}}">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label" for="title">{{translate('messages.start')}} {{translate('messages.time')}}</label>
                                        <input type="time" id="start_time" class="form-control" name="start_time" value="{{$campaign->start_time->format('H:i')}}">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label" for="title">{{translate('messages.end')}} {{translate('messages.time')}}</label>
                                        <input type="time" id="end_time" class="form-control" name="end_time" value="{{$campaign->end_time->format('H:i')}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="btn--container justify-content-end mt-2">
                        <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('messages.reset')}}</button>
                        <button type="submit" class="btn btn--primary">{{translate('messages.update')}}</button>
                    </div>
                </div>

            </div>
        </form>
    </div>

@endsection

@push('script_2')
    <script>
        function getStoreData(route, id) {
            $.get({
                url: route,
                dataType: 'json',
                success: function (data) {
                    $('#' + id).empty().append(data.options);
                },
            });
        }
        function getRequest(route, id) {
            $.get({
                url: route,
                dataType: 'json',
                success: function (data) {
                    $('#' + id).empty().append(data.options);
                },
            });
        }

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
            $('#viewer').show(1000)
        });

        $(document).ready(function () {
            @if(count(json_decode($campaign['add_ons'], true))>0)
            getStoreData('{{url('/')}}/admin/store/get-addons?store_id={{$campaign['store_id']}}@foreach(json_decode($campaign['add_ons'], true) as $addon)&data[]={{$addon}}@endforeach','add_on');
            @else
            getStoreData('{{url('/')}}/admin/store/get-addons?data[]=0&store_id={{$campaign['store_id']}}','add_on');
            @endif
        });
    </script>
    <script src="{{asset('public/assets/admin')}}/js/tags-input.min.js"></script>

    <script>
        $('#choice_attributes').on('change', function () {
            $('#customer_choice_options').html(null);
            $.each($("#choice_attributes option:selected"), function () {
                add_more_customer_choice_option($(this).val(), $(this).text());
            });
        });

        function add_more_customer_choice_option(i, name) {
            let n = name.split(' ').join('');
            $('#customer_choice_options').append('<div class="row gy-1"><div class="col-sm-3"><input type="hidden" name="choice_no[]" value="' + i + '"><input type="text" class="form-control" name="choice[]" value="' + n + '" placeholder="Choice Title" readonly></div><div class="col-sm-9"><input type="text" class="form-control" name="choice_options_' + i + '[]" placeholder="{{translate('messages.enter_choice_values')}}" data-role="tagsinput" onchange="combination_update()"></div></div>');
            $("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
        }

        function combination_update() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: '{{route('admin.item.variant-combination')}}',
                data: $('#campaign_form').serialize(),
                success: function (data) {
                    console.log(data.view);
                    $('#variant_combination').html(data.view);
                    if (data.length < 1) {
                        $('input[name="current_stock"]').attr("readonly", false);
                    }
                }
            });
        }
    </script>
    <script>
        var module_id = {{$campaign->module_id}};
        var module_type = "{{$campaign->module->module_type}}";
        if (module_type == 'food') {
                $('#food_variation_section').show();
                $('#attribute_section').hide();
            } else {
                $('#food_variation_section').hide();
                $('#attribute_section').show();
            }
        var parent_category_id = {{$category?$category->id:0}};
        <?php
            $module_data = config('module.'.$campaign->module->module_type);
            unset($module_data['description']);
        ?>
        var module_data = {{str_replace('"','',json_encode($module_data))}};
        input_field_visibility_update();
        function modulChange(id)
        {
            $.get({
                url: "{{url('/')}}/admin/module/"+id,
                dataType: 'json',
                success: function (data) {
                    module_data = data.data;
                    stock = module_data.stock;
                    input_field_visibility_update();
                    combination_update();
                },
            });
            module_id = id;
        }

        function input_field_visibility_update()
        {
            if(module_data.stock)
            {
                $('#stock_input').show();
            }
            else
            {
                $('#stock_input').hide();
            }
            if(module_data.add_on)
            {
                $('#addon_input').show();
            }
            else{
                $('#addon_input').hide();
            }

            if(module_data.item_available_time)
            {
                $('#time_input').hide();
            }
            else{
                $('#time_input').show();
            }

            if(module_data.veg_non_veg)
            {
                $('#veg_input').show();
            }
            else{
                $('#veg_input').hide();
            }

            if(module_data.unit)
            {
                $('#unit_input').show();
            }
            else{
                $('#unit_input').hide();
            }
        }


        function categoryChange(id)
        {
            parent_category_id = id;
            console.log(parent_category_id);
        }
        $('#store_id').select2({
            ajax: {
                url: '{{url('/')}}/admin/store/get-stores',
                data: function (params) {
                    return {
                        q: params.term, // search term
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

        $('#category_id').select2({
            ajax: {
                url: '{{url('/')}}/admin/item/get-categories?parent_id=0',
                data: function (params) {
                    return {
                        q: params.term, // search term
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

        $('#sub-categories').select2({
            ajax: {
                url: '{{url('/')}}/admin/item/get-categories',
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                        module_id: module_id,
                        parent_id: parent_category_id,
                        sub_category: true
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
        $("#date_from").on("change", function () {
            $('#date_to').attr('min',$(this).val());
        });

        $("#date_to").on("change", function () {
            $('#date_from').attr('max',$(this).val());
        });

        $(document).ready(function(){
            $('#date_to').attr('min',('{{$campaign->start_date->format('Y-m-d')}}'));
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });

        $('#campaign_form').on('submit', function () {
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.campaign.update-item', [$campaign->id])}}',
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
                        toastr.success('Campaign uploaded successfully!', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function () {
                            location.href = '{{route('admin.campaign.list', 'item')}}';
                        }, 2000);
                    }
                }
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
            if(lang == 'en')
            {
                $("#from_part_2").removeClass('d-none');
            }
            else
            {
                $("#from_part_2").addClass('d-none');
            }
        })
    </script>
    <script>
            $('#reset_btn').click(function(){
                location.reload(true);
            })

        </script>

        <script>
    function show_min_max(data){
        console.log(data);
        $('#min_max1_'+data).removeAttr("readonly");
        $('#min_max2_'+data).removeAttr("readonly");
        $('#min_max1_'+data).attr("required","true");
        $('#min_max2_'+data).attr("required","true");
    }
    function hide_min_max (data){
        console.log(data);
        $('#min_max1_'+data).val(null).trigger('change');
        $('#min_max2_'+data).val(null).trigger('change');
        $('#min_max1_'+data).attr("readonly","true");
        $('#min_max2_'+data).attr("readonly","true");
        $('#min_max1_'+data).attr("required","false");
        $('#min_max2_'+data).attr("required","false");
    }



    var count= {{isset($product->food_variations)?count(json_decode($product->food_variations,true)):0}};

    $(document).ready(function(){
        console.log(count);

        $("#add_new_option_button").click(function(e){
        count++;
        var add_option_view = `
        <div class="card view_new_option mb-2" >
        <div class="card-header">
            <label for="" id=new_option_name_`+count+`> {{  translate('add new variation')}}</label>
        </div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-lg-3 col-md-6">
                    <label for="">{{ translate('name')}}</label>
                    <input required name=options[`+count+`][name] class="form-control" type="text" onkeyup="new_option_name(this.value,`+count+`)">
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="form-group">
                        <label class="input-label text-capitalize d-flex alig-items-center"><span class="line--limit-1">{{ translate('messages.selcetion_type') }} </span>
                        </label>
                        <div class="resturant-type-group border">
                            <label class="form-check form--check mr-2 mr-md-4">
                                <input class="form-check-input" type="radio" value="multi"
                                name="options[`+count+`][type]" id="type`+count+`" checked onchange="show_min_max(`+count+`)"
                                >
                                <span class="form-check-label">
                                    {{ translate('Multiple') }}
                                </span>
                            </label>

                            <label class="form-check form--check mr-2 mr-md-4">
                                <input class="form-check-input" type="radio" value="single"
                                name="options[`+count+`][type]" id="type`+count+`" onchange="hide_min_max(`+count+`)"
                                >
                                <span class="form-check-label">
                                    {{ translate('Single') }}
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="row g-2">
                        <div class="col-sm-6 col-md-4">
                            <label for="">{{  translate('Min')}}</label>
                            <input id="min_max1_`+count+`" required  name="options[`+count+`][min]" class="form-control" type="number" min="1">
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <label for="">{{  translate('Max')}}</label>
                            <input id="min_max2_`+count+`" required  name="options[`+count+`][max]" class="form-control" type="number" min="1">
                        </div>

                        <div class="col-md-4">
                            <label class="d-md-block d-none">&nbsp;</label>
                                <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <input id="options[`+count+`][required]" name="options[`+count+`][required]" type="checkbox">
                                    <label for="options[`+count+`][required]" class="m-0">{{  translate('Required')}}</label>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-danger btn-sm delete_input_button" onclick="removeOption(this)"
                                        title="{{  translate('Delete')}}">
                                        <i class="tio-add-to-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>





        <div id="option_price_` + count + `" >
                <div class="border rounded p-3 pb-0 mt-3">
                    <div  id="option_price_view_` + count + `">
                        <div class="row g-3 add_new_view_row_class mb-3">
                            <div class="col-md-4 col-sm-6">
                                <label for="">{{ translate('Option_name') }}</label>
                                <input class="form-control" required type="text" name="options[` + count +`][values][0][label]" id="">
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <label for="">{{ translate('Additional_price') }}</label>
                                <input class="form-control" required type="number" min="0" step="0.01" name="options[` + count + `][values][0][optionPrice]" id="">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3 p-3 mr-1 d-flex "  id="add_new_button_` + count + `">
                        <button type="button" class="btn btn-outline-primary" onclick="add_new_row_button(` +
                count + `)" >{{ translate('Add_New_Option') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>`;

        $("#add_new_option").append(add_option_view);

        });

    });

    function new_option_name(value,data)
    {
        $("#new_option_name_"+data).empty();
        $("#new_option_name_"+data).text(value)
        console.log(value);
    }
    function removeOption(e)
    {
        element = $(e);
        element.parents('.view_new_option').remove();
    }
    function deleteRow(e)
    {
        element = $(e);
        element.parents('.add_new_view_row_class').remove();
    }


    function add_new_row_button(data)
    {
        count = data;
        countRow = 1 + $('#option_price_view_'+data).children('.add_new_view_row_class').length;
        var add_new_row_view = `
        <div class="row add_new_view_row_class mb-3 position-relative pt-3 pt-md-0">
                <div class="col-md-4 col-sm-5">
                        <label for="">{{translate('Option_name')}}</label>
                        <input class="form-control" required type="text" name="options[`+count+`][values][`+countRow+`][label]" id="">
                    </div>
                    <div class="col-md-4 col-sm-5">
                        <label for="">{{translate('Additional_price')}}</label>
                        <input class="form-control"  required type="number" min="0" step="0.01" name="options[`+count+`][values][`+countRow+`][optionPrice]" id="">
                    </div>
                    <div class="col-sm-2 max-sm-absolute">
                        <label class="d-none d-md-block">&nbsp;</label>
                        <div class="mt-1">
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this)"
                                title="{{translate('Delete')}}">
                                <i class="tio-add-to-trash"></i>
                            </button>
                        </div>
                </div>
            </div>`;
        $('#option_price_view_'+data).append(add_new_row_view);

    }
    // function apendPriceView(data)
    // {
    // countRow = 1 + $('#option_price_view_'+data).children('.add_new_view_row_class').length;
    //     var apend_Price_View = `
    //     <div class="row  border mt-3 p-3 ml-1 mr-1 ">
    //         <div class="col-8">
    //             <label for="">{{translate('price')}}</label>
    //             <input class="form-control" type="text" name="options[`+count+`][values][`+countRow+`][optionPrice]" id="">
    //         </div>
    //     </div>`;
    //     $('#option_price_view_'+data).append(apend_Price_View);

    // }
    // function choice_option_type(optionValue,data){
    //     var text = ["field","textarea"];
    //     var select = ["dropdown","checkbox","custom checkbox","radio button","custom radio button","multiple select"];
    //     var date = ["date","date & time","time"];

    //     if(jQuery.inArray( optionValue, text )>-1 || jQuery.inArray( optionValue, date )>-1){
    //         $("#option_price_view_"+data).empty();
    //         apendPriceView(data);
    //         $('#add_new_button_'+data).hide();
    //     }else if(jQuery.inArray( optionValue, select ) > -1)
    //     {
    //         $("#option_price_view_"+data).empty();
    //         add_new_row_button(data);
    //         $('#add_new_button_'+data).show();
    //     }

    // }


</script>
@endpush
