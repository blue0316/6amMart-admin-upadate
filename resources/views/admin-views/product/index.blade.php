@extends('layouts.admin.app')

@section('title', translate('messages.add_new_item'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('public/assets/admin/css/tags-input.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/items.png') }}" class="w--22" alt="">
                </span>
                <span>
                    {{ translate('messages.add_new_item') }}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <form action="javascript:" method="post" id="item_form" enctype="multipart/form-data">
            @csrf
            @php($language = \App\Models\BusinessSetting::where('key', 'language')->first())
            @php($language = $language->value ?? null)
            @php($default_lang = 'bn')
            <div class="row g-2">
                <div class="col-12">
                    @if ($language)
                        @php($default_lang = json_decode($language)[0])
                        <ul class="nav nav-tabs border-0">
                            @foreach (json_decode($language) as $lang)
                                <li class="nav-item">
                                    <a class="nav-link lang_link {{ $lang == $default_lang ? 'active' : '' }}"
                                        href="#"
                                        id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon">
                                    <i class="tio-dashboard-outlined"></i>
                                </span>
                                <span>{{ translate('item_info') }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            @if ($language)
                                @foreach (json_decode($language) as $lang)
                                    <div class="{{ $lang != $default_lang ? 'd-none' : '' }} lang_form"
                                        id="{{ $lang }}-form">
                                        <div class="form-group">
                                            <label class="input-label"
                                                for="{{ $lang }}_name">{{ translate('messages.name') }}
                                                ({{ strtoupper($lang) }})
                                            </label>
                                            <input type="text" name="name[]" id="{{ $lang }}_name"
                                                class="form-control" placeholder="{{ translate('messages.new_item') }}"
                                                {{ $lang == $default_lang ? 'required' : '' }}
                                                oninvalid="document.getElementById('en-link').click()">
                                        </div>
                                        <input type="hidden" name="lang[]" value="{{ $lang }}">
                                        <div class="form-group mb-0">
                                            <label class="input-label"
                                                for="exampleFormControlInput1">{{ translate('messages.short') }}
                                                {{ translate('messages.description') }} ({{ strtoupper($lang) }})</label>
                                            <textarea type="text" name="description[]" class="form-control min-h-90px ckeditor"></textarea>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div id="{{ $default_lang }}-form">
                                    <div class="form-group">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.name') }} (EN)</label>
                                        <input type="text" name="name[]" class="form-control"
                                            placeholder="{{ translate('messages.new_item') }}" required>
                                    </div>
                                    <input type="hidden" name="lang[]" value="en">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.short') }}
                                            {{ translate('messages.description') }}</label>
                                        <textarea type="text" name="description[]" class="form-control min-h-90px ckeditor"></textarea>
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
                                    <i class="tio-image"></i>
                                </span>
                                <span>{{ translate('item_image') }}</span>
                            </h5>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="my-auto">
                                <label class="input-label" for="exampleFormControlInput1">{{ translate('messages.item') }}
                                    {{ translate('messages.images') }}</label>
                                <div class="row py-2" id="coba"></div>
                            </div>
                            <label class="text-dark">
                                {{ translate('messages.item') }} {{ translate('messages.thumbnail') }}
                                <small class="text-danger">* ( {{ translate('messages.ratio') }} 1:1 )</small>
                            </label>
                            <center id="image-viewer-section" class="pt-2 pb-3 initial-hidden">
                                <img class="img--100" id="viewer"
                                    src="{{ asset('public/assets/admin/img/400x400/img2.jpg') }}" alt="banner image" />
                            </center>
                            <div class="custom-file">
                                <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                    accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                                <label class="custom-file-label" for="customFileEg1">{{ translate('messages.choose') }}
                                    {{ translate('messages.file') }}</label>
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
                                <span> {{ translate('item_details') }} </span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                {{-- <div class="col-sm-6 col-lg-4">
                                    <div class="form-group mb-0">
                                        <label class="input-label">{{ translate('messages.module') }}</label>
                                        <select name="module_id" id="module_id" required
                                            class="form-control js-select2-custom"
                                            data-placeholder="{{ translate('messages.select') }} {{ translate('messages.module') }}"
                                            onchange="modulChange(this.value)">
                                            <option value="" selected disabled>{{ translate('messages.select') }}
                                                {{ translate('messages.module') }}</option>
                                            @foreach (\App\Models\Module::notParcel()->get() as $module)
                                                <option value="{{ $module->id }}">{{ $module->module_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div> --}}

                                <div class="col-sm-6 col-lg-4">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="store_id">{{ translate('messages.store') }}<span
                                                class="input-label-secondary"></span></label>
                                        <select name="store_id" id="store_id"
                                            data-placeholder="{{ translate('messages.select') }} {{ translate('messages.store') }}"
                                            id="store_id" class="js-data-example-ajax form-control"
                                            onchange="getRestaurantData('{{ url('/') }}/admin/store/get-addons?data[]=0&store_id=',this.value,'add_on')"
                                            oninvalid="this.setCustomValidity('{{ translate('messages.please_select_store') }}')">

                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="category_id">{{ translate('messages.category') }}<span
                                                class="input-label-secondary">*</span></label>
                                        <select name="category_id" id="category_id"
                                            class="js-data-example-ajax form-control" id="category_id"
                                            onchange="categoryChange(this.value)">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="sub-categories">{{ translate('messages.sub_category') }}<span
                                                class="input-label-secondary"
                                                title="{{ translate('messages.category_required_warning') }}"><img
                                                    src="{{ asset('/public/assets/admin/img/info-circle.svg') }}"
                                                    alt="{{ translate('messages.category_required_warning') }}"></span></label>
                                        <select name="sub_category_id" class="js-data-example-ajax form-control"
                                            id="sub-categories">

                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4" id="unit_input">
                                    <div class="form-group mb-0">
                                        <label class="input-label text-capitalize"
                                            for="unit">{{ translate('messages.unit') }}</label>
                                        <select name="unit" id="unit" class="form-control js-select2-custom">
                                            @foreach (\App\Models\Unit::all() as $unit)
                                                <option value="{{ $unit->id }}">{{ $unit->unit }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4">
                                    <div class="form-group mb-0" id="veg_input">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.item_type') }}</label>
                                        <select name="veg" id="veg" class="form-control js-select2-custom"
                                            required>
                                            <option value="0">{{ translate('messages.non_veg') }}</option>
                                            <option value="1">{{ translate('messages.veg') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4" id="stock_input">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="total_stock">{{ translate('messages.total_stock') }}</label>
                                        <input type="number" class="form-control" name="current_stock" id="quantity">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4" id="addon_input">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="exampleFormControlSelect1">{{ translate('messages.addon') }}<span
                                                class="input-label-secondary"
                                                title="{{ translate('messages.addon') }}"><img
                                                    src="{{ asset('/public/assets/admin/img/info-circle.svg') }}"
                                                    alt="{{ translate('messages.store_required_warning') }}"></span></label>
                                        <select name="addon_ids[]" class="form-control js-select2-custom"
                                            multiple="multiple" id="add_on">

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
                                <span>{{ translate('amount') }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-sm-4 col-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.price') }}</label>
                                        <input type="number" min="0" max="999999999999.99" step="0.01"
                                            value="1" name="price" class="form-control"
                                            placeholder="{{ translate('messages.Ex:') }} 100" required>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.discount') }}
                                            {{ translate('messages.type') }}<span
                                                class="input-label-secondary text--title" data-toggle="tooltip"
                                                data-placement="right"
                                                data-original-title="{{ translate('Currently you need to manage discount with store.') }}">
                                                <i class="tio-info-outined"></i>
                                            </span></label>
                                        <select name="discount_type" id="discount_type"
                                            class="form-control js-select2-custom">
                                            <option value="percent">{{ translate('messages.percent') }}</option>
                                            <option value="amount">{{ translate('messages.amount') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.discount') }}</label>
                                        <input type="number" min="0" max="9999999999999999999999" value="0"
                                            name="discount" class="form-control"
                                            placeholder="{{ translate('messages.Ex:') }} 100">
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
                                <span>{{ translate('messages.food_variations') }}</span>
                            </h5>
                        </div>
                        <div class="card-body pb-0">
                            <div class="row g-2">
                                <div class="col-md-12">
                                    <div id="add_new_option">
                                    </div>
                                    <br>
                                    <div class="mt-2">
                                        <a class="btn btn-outline-success"
                                            id="add_new_option_button">{{ translate('add_new_variation') }}</a>
                                    </div> <br><br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12" id="attribute_section">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon"><i class="tio-canvas-text"></i></span>
                                <span>{{ translate('attribute') }}</span>
                            </h5>
                        </div>
                        <div class="card-body pb-0">
                            <div class="row g-2">
                                <div class="col-12">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="exampleFormControlSelect1">{{ translate('messages.attribute') }}<span
                                                class="input-label-secondary"></span></label>
                                        <select name="attribute_id[]" id="choice_attributes"
                                            class="form-control js-select2-custom" multiple="multiple">
                                            @foreach (\App\Models\Attribute::orderBy('name')->get() as $attribute)
                                                <option value="{{ $attribute['id'] }}">{{ $attribute['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="customer_choice_options" id="customer_choice_options">

                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="variant_combination" id="variant_combination">

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
                                <span class="card-header-icon"><i class="tio-label"></i></span>
                                <span>{{ translate('tags') }}</span>
                            </h5>
                        </div>
                        <div class="card-body pb-0">
                            <div class="row g-2">
                                <div class="col-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="tags" placeholder="{{translate('messages.search_tags')}}" data-role="tagsinput">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12" id="time_input">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon"><i class="tio-date-range"></i></span>
                                <span>{{ translate('time_schedule') }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.available') }}
                                            {{ translate('messages.time') }} {{ translate('messages.starts') }}</label>
                                        <input type="time" name="available_time_starts" class="form-control"
                                            id="available_time_starts"
                                            placeholder="{{ translate('messages.Ex:') }} 10:30 am">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.available') }}
                                            {{ translate('messages.time') }} {{ translate('messages.ends') }}</label>
                                        <input type="time" name="available_time_ends" class="form-control"
                                            id="available_time_ends" placeholder="5:45 pm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="btn--container justify-content-end">
                        <button type="reset" id="reset_btn"
                            class="btn btn--reset">{{ translate('messages.reset') }}</button>
                        <button type="submit" class="btn btn--primary">{{ translate('messages.submit') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection


@push('script_2')
    <script>
        var count = 0;
        // var countRow=0;
        $(document).ready(function() {

            $('#food_variation_section').hide();
            $("#add_new_option_button").click(function(e) {
                count++;
                var add_option_view = `
                    <div class="card view_new_option mb-2" >
                        <div class="card-header">
                            <label for="" id=new_option_name_` + count + `> {{ translate('add_new') }}</label>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-lg-3 col-md-6">
                                    <label for="">{{ translate('name') }}</label>
                                    <input required name=options[` + count +
                    `][name] class="form-control" type="text" onkeyup="new_option_name(this.value,` +
                    count + `)">
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    <div class="form-group">
                                        <label class="input-label text-capitalize d-flex alig-items-center"><span class="line--limit-1">{{ translate('messages.selcetion_type') }} </span>
                                        </label>
                                        <div class="resturant-type-group border">
                                            <label class="form-check form--check mr-2 mr-md-4">
                                                <input class="form-check-input" type="radio" value="multi"
                                                name="options[` + count + `][type]" id="type` + count +
                    `" checked onchange="show_min_max(` + count + `)"
                                                >
                                                <span class="form-check-label">
                                                    {{ translate('Multiple') }}
                                                </span>
                                            </label>

                                            <label class="form-check form--check mr-2 mr-md-4">
                                                <input class="form-check-input" type="radio" value="single"
                                                name="options[` + count + `][type]" id="type` + count +
                    `" onchange="hide_min_max(` + count + `)"
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
                                            <label for="">{{ translate('Min') }}</label>
                                            <input id="min_max1_` + count + `" required  name="options[` + count + `][min]" class="form-control" type="number" min="1">
                                        </div>
                                        <div class="col-sm-6 col-md-4">
                                            <label for="">{{ translate('Max') }}</label>
                                            <input id="min_max2_` + count + `"   required name="options[` + count + `][max]" class="form-control" type="number" min="1">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="d-md-block d-none">&nbsp;</label>
                                                <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <input id="options[` + count + `][required]" name="options[` +
                    count + `][required]" type="checkbox">
                                                    <label for="options[` + count + `][required]" class="m-0">{{ translate('Required') }}</label>
                                                </div>
                                                <div>
                                                    <button type="button" class="btn btn-danger btn-sm delete_input_button" onclick="removeOption(this)"
                                                        title="{{ translate('Delete') }}">
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
                                                <input class="form-control" required type="text" name="options[` +
                    count +
                    `][values][0][label]" id="">
                                            </div>
                                            <div class="col-md-4 col-sm-6">
                                                <label for="">{{ translate('Additional_price') }}</label>
                                                <input class="form-control" required type="number" min="0" step="0.01" name="options[` +
                    count + `][values][0][optionPrice]" id="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3 p-3 mr-1 d-flex "  id="add_new_button_` + count +
                    `">
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

        function show_min_max(data) {
            $('#min_max1_' + data).removeAttr("readonly");
            $('#min_max2_' + data).removeAttr("readonly");
            $('#min_max1_' + data).attr("required", "true");
            $('#min_max2_' + data).attr("required", "true");
        }

        function hide_min_max(data) {
            $('#min_max1_' + data).val(null).trigger('change');
            $('#min_max2_' + data).val(null).trigger('change');
            $('#min_max1_' + data).attr("readonly", "true");
            $('#min_max2_' + data).attr("readonly", "true");
            $('#min_max1_' + data).attr("required", "false");
            $('#min_max2_' + data).attr("required", "false");
        }




        function new_option_name(value, data) {
            $("#new_option_name_" + data).empty();
            $("#new_option_name_" + data).text(value)
            console.log(value);
        }

        function removeOption(e) {
            element = $(e);
            element.parents('.view_new_option').remove();
        }

        function deleteRow(e) {
            element = $(e);
            element.parents('.add_new_view_row_class').remove();
        }


        function add_new_row_button(data) {
            count = data;
            countRow = 1 + $('#option_price_view_' + data).children('.add_new_view_row_class').length;
            var add_new_row_view = `
            <div class="row add_new_view_row_class mb-3 position-relative pt-3 pt-sm-0">
                <div class="col-md-4 col-sm-5">
                        <label for="">{{ translate('Option_name') }}</label>
                        <input class="form-control" required type="text" name="options[` + count + `][values][` +
                countRow + `][label]" id="">
                    </div>
                    <div class="col-md-4 col-sm-5">
                        <label for="">{{ translate('Additional_price') }}</label>
                        <input class="form-control"  required type="number" min="0" step="0.01" name="options[` +
                count +
                `][values][` + countRow + `][optionPrice]" id="">
                    </div>
                    <div class="col-sm-2 max-sm-absolute">
                        <label class="d-none d-sm-block">&nbsp;</label>
                        <div class="mt-1">
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this)"
                                title="{{ translate('Delete') }}">
                                <i class="tio-add-to-trash"></i>
                            </button>
                        </div>
                </div>
            </div>`;
            $('#option_price_view_' + data).append(add_new_row_view);

        }
    </script>
    <script src="{{ asset('public/assets/admin') }}/js/tags-input.min.js"></script>
    <script>
        function getRestaurantData(route, store_id, id) {
            $.get({
                url: route + store_id,
                dataType: 'json',
                success: function(data) {
                    $('#' + id).empty().append(data.options);
                },
            });
        }

        function getRequest(route, id) {
            $.get({
                url: route,
                dataType: 'json',
                success: function(data) {
                    $('#' + id).empty().append(data.options);
                },
            });
        }

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function() {
            readURL(this);
            $('#image-viewer-section').show(1000);
        });
    </script>

    <script>
        var module_id = {{Config::get('module.current_module_id')}};
        var parent_category_id = 0;
        var module_data = null;
        var stock = true;

        function modulChange(id) {
            $.get({
                url: "{{ url('/') }}/admin/module/" + id,
                dataType: 'json',
                success: function(data) {
                    module_data = data.data;
                    stock = module_data.stock;
                    module_type = data.type;
                    if (stock) {
                        $('#stock_input').show();
                    } else {
                        $('#stock_input').hide();
                    }
                    if (module_data.add_on) {
                        $('#addon_input').show();
                    } else {
                        $('#addon_input').hide();
                    }

                    if (module_data.item_available_time) {
                        $('#time_input').show();
                    } else {
                        $('#time_input').hide();
                    }

                    if (module_data.veg_non_veg) {
                        $('#veg_input').show();
                    } else {
                        $('#veg_input').hide();
                    }
                    if (module_data.unit) {
                        $('#unit_input').show();
                    } else {
                        $('#unit_input').hide();
                    }
                    combination_update();
                    if (module_type == 'food') {
                        $('#food_variation_section').show();
                        $('#attribute_section').hide();
                    } else {
                        $('#food_variation_section').hide();
                        $('#attribute_section').show();
                    }
                },
            });
            module_id = id;
        }
        modulChange({{Config::get('module.current_module_id')}});
        function categoryChange(id) {
            parent_category_id = id;
            console.log(parent_category_id);
        }

        $(document).on('ready', function() {
            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function() {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
        $('#store_id').select2({
            ajax: {
                url: '{{ url('/') }}/admin/store/get-stores',
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                        module_id:{{Config::get('module.current_module_id')}},
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                __port: function(params, success, failure) {
                    var $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });

        $('#category_id').select2({
            ajax: {
                url: '{{ url('/') }}/admin/item/get-categories?parent_id=0',
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                        module_id:{{Config::get('module.current_module_id')}},
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                __port: function(params, success, failure) {
                    var $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });

        $('#sub-categories').select2({
            ajax: {
                url: '{{ url('/') }}/admin/item/get-categories',
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                        module_id:{{Config::get('module.current_module_id')}},
                        parent_id: parent_category_id,
                        sub_category: true
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                __port: function(params, success, failure) {
                    var $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });

        $('#choice_attributes').on('change', function() {
            if (module_id == 0) {
                toastr.error('{{ translate('messages.select_a_module') }}', {
                    CloseButton: true,
                    ProgressBar: true
                });
                $(this).val("");
                return false;
            }
            $('#customer_choice_options').html(null);
            $.each($("#choice_attributes option:selected"), function() {
                if ($(this).val().length > 50) {
                    toastr.error(
                        '{{ translate('validation.max.string', ['attribute' => translate('messages.variation'), 'max' => '50']) }}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    return false;
                }
                add_more_customer_choice_option($(this).val(), $(this).text());
            });
        });

        function add_more_customer_choice_option(i, name) {
            let n = name;
            $('#customer_choice_options').append(
                '<div class="row gy-1"><div class="col-sm-3"><input type="hidden" name="choice_no[]" value="' + i +
                '"><input type="text" class="form-control" name="choice[]" value="' + n +
                '" placeholder="{{ translate('messages.choice_title') }}" readonly></div><div class="col-sm-9"><input type="text" class="form-control" name="choice_options_' +
                i +
                '[]" placeholder="{{ translate('messages.enter_choice_values') }}" data-role="tagsinput" onchange="combination_update()"></div></div>'
            );
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
                url: "{{ route('admin.item.variant-combination') }}",
                data: $('#item_form').serialize() + '&stock=' + stock,
                beforeSend: function() {
                    $('#loading').show();
                },
                success: function(data) {
                    $('#loading').hide();
                    $('#variant_combination').html(data.view);
                    if (data.length < 1) {
                        $('input[name="current_stock"]').attr("readonly", false);
                    }
                }
            });
        }
    </script>

    <script>
        $('#item_form').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{ route('admin.item.store') }}',
                data: $('#item_form').serialize(),
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#loading').show();
                },
                success: function(data) {
                    $('#loading').hide();
                    if (data.errors) {
                        for (var i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        toastr.success("{{ translate('messages.product_added_successfully') }}", {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function() {
                            location.href =
                                "{{ \Request::server('HTTP_REFERER') ?? route('admin.item.list') }}";
                        }, 2000);
                    }
                }
            });
        });
    </script>
    <script>
        $(".lang_link").click(function(e) {
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.substring(0, form_id.length - 5);
            console.log(lang);
            $("#" + lang + "-form").removeClass('d-none');
            if (lang == '{{ $default_lang }}') {
                $("#from_part_2").removeClass('d-none');
            } else {
                $("#from_part_2").addClass('d-none');
            }
        })
    </script>
    <script src="{{ asset('public/assets/admin/js/spartan-multi-image-picker.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'item_images[]',
                maxCount: 6,
                rowHeight: '120px',
                groupClassName: 'col-6 spartan_item_wrapper',
                maxFileSize: '',
                placeholderImage: {
                    image: "{{ asset('public/assets/admin/img/400x400/img2.jpg') }}",
                    width: '100%'
                },
                dropFileLabel: "Drop Here",
                onAddRow: function(index, file) {

                },
                onRenderedPreview: function(index) {

                },
                onRemoveRow: function(index) {

                },
                onExtensionErr: function(index, file) {
                    toastr.error(
                        "{{ translate('messages.please_only_input_png_or_jpg_type_file') }}", {
                            CloseButton: true,
                            ProgressBar: true
                        });
                },
                onSizeErr: function(index, file) {
                    toastr.error("{{ translate('messages.file_size_too_big') }}", {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        });
    </script>
    <script>
        $('#reset_btn').click(function() {
            $('#module_id').val(null).trigger('change');
            $('#store_id').val(null).trigger('change');
            $('#category_id').val(null).trigger('change');
            $('#sub-categories').val(null).trigger('change');
            $('#unit').val(null).trigger('change');
            $('#veg').val(0).trigger('change');
            $('#add_on').val(null).trigger('change');
            $('#discount_type').val(null).trigger('change');
            $('#choice_attributes').val(null).trigger('change');
            $('#customer_choice_options').empty().trigger('change');
            $('#variant_combination').empty().trigger('change');
            $('#time_input').show();
            $('#viewer').hide();
            $("#coba").empty().spartanMultiImagePicker({
                fieldName: 'item_images[]',
                maxCount: 6,
                rowHeight: '120px',
                groupClassName: 'col-6 spartan_item_wrapper',
                maxFileSize: '',
                placeholderImage: {
                    image: "{{ asset('public/assets/admin/img/400x400/img2.jpg') }}",
                    width: '100%'
                },
                dropFileLabel: "Drop Here",
                onAddRow: function(index, file) {

                },
                onRenderedPreview: function(index) {

                },
                onRemoveRow: function(index) {

                },
                onExtensionErr: function(index, file) {
                    toastr.error(
                        "{{ translate('messages.please_only_input_png_or_jpg_type_file') }}", {
                            CloseButton: true,
                            ProgressBar: true
                        });
                },
                onSizeErr: function(index, file) {
                    toastr.error("{{ translate('messages.file_size_too_big') }}", {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        })
    </script>
@endpush
