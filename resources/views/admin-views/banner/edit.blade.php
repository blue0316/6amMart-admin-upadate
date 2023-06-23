@extends('layouts.admin.app')

@section('title',translate('Update Banner'))

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/edit.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.update')}} {{translate('messages.banner')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.banner.update', [$banner->id])}}" method="post" id="banner_form">
                            @csrf
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.title')}}</label>
                                        <input type="text" name="title" class="form-control" placeholder="{{translate('messages.new_banner')}}" value="{{$banner->title}}" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="input-label">{{translate('messages.module')}}</label>
                                        <select name="module_id" class="form-control js-select2-custom"  title="{{translate('messages.select')}} {{translate('messages.module')}}" id="module_select" disabled>
                                            @foreach(\App\Models\Module::notParcel()->get() as $module)
                                                <option value="{{$module->id}}" {{$module->id==$banner->module_id?'selected':''}}>{{$module->module_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="input-label" for="title">{{translate('messages.zone')}}</label>
                                        <select name="zone_id" id="zone" class="form-control js-select2-custom">
                                            <option  disabled selected>---{{translate('messages.select')}}---</option>
                                            @php($zones=\App\Models\Zone::all())
                                            @foreach($zones as $zone)
                                                @if(isset(auth('admin')->user()->zone_id))
                                                    @if(auth('admin')->user()->zone_id == $zone->id)
                                                        <option value="{{$zone['id']}}" {{$zone->id == $banner->zone_id?'selected':''}}>{{$zone['name']}}</option>
                                                    @endif
                                                @else
                                                <option value="{{$zone['id']}}" {{$zone->id == $banner->zone_id?'selected':''}}>{{$zone['name']}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.banner')}} {{translate('messages.type')}}</label>
                                        <select name="banner_type" id="banner_type" class="form-control" onchange="banner_type_change(this.value)">
                                            <option value="store_wise" {{$banner->type == 'store_wise'? 'selected':'' }}>{{translate('messages.store')}} {{translate('messages.wise')}}</option>
                                            <option value="item_wise" {{$banner->type == 'item_wise'? 'selected':'' }}>{{translate('messages.item')}} {{translate('messages.wise')}}</option>
                                            <option value="default" {{$banner->type == 'default'? 'selected':'' }}>{{translate('messages.default')}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-0" id="store_wise">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.store')}}<span
                                                class="input-label-secondary"></span></label>
                                        <select name="store_id" id="store_id" class="js-data-example-ajax" id="resturant_ids"  title="Select Restaurant">
                                        @if($banner->type=='store_wise')
                                        @php($store = \App\Models\Store::where('id', $banner->data)->first())
                                            @if($store)
                                            <option value="{{$store->id}}" selected>{{$store->name}}</option>
                                            @endif
                                        @endif
                                        </select>
                                    </div>
                                    <div class="form-group mb-0" id="item_wise">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.select')}} {{translate('messages.item')}}</label>
                                        <select name="item_id" id="choice_item" class="form-control js-select2-custom" placeholder="{{translate('messages.select_item')}}">

                                        </select>
                                    </div>
                                    <div class="form-group mb-0" id="default">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.default_link')}}</label>
                                        <input type="text" name="default_link" class="form-control" value="{{ $banner->default_link }}" placeholder="{{translate('messages.default_link')}}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="h-100 d-flex flex-column">
                                        <label class="mt-auto mb-0 d-block text-center">
                                            {{translate('messages.campaign')}} {{translate('messages.image')}}
                                            <small class="text-danger">* ( {{translate('messages.ratio')}} 900x300 )</small>
                                        </label>
                                        <center class="py-3 my-auto">
                                            <img class="img--vertical" id="viewer" onerror="this.src='{{asset('public/assets/admin/img/900x400/img1.jpg')}}'" src="{{asset('storage/app/public/banner')}}/{{$banner['image']}}" alt="campaign image"/>
                                        </center>
                                        <div class="custom-file">
                                            <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label" for="customFileEg1">{{translate('messages.choose')}} {{translate('messages.file')}}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <div class="btn--container justify-content-end">
                                        <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('messages.reset')}}</button>
                                        <button type="submit" class="btn btn--primary">{{translate('messages.update')}}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')
    <script>
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
        });
    </script>
    <script>
        var zone_id = {{$banner->zone_id}};

        var module_id = {{$banner->module_id}};

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


            $('#module_select').on('change', function(){
                if($(this).val())
                {
                    module_id = $(this).val();
                    get_items();
                }
            });
            banner_type_change('{{$banner->type}}');

            $('#zone').on('change', function(){
                if($(this).val())
                {
                    zone_id = $(this).val();
                    get_items();
                }
                else
                {
                    zone_id = true;
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



            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });

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
                $('#item_wise').hide();
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
        @if($banner->type == 'item_wise')
        getRequest('{{url('/')}}/admin/item/get-items?module_id={{$banner->module_id}}&zone_id={{$banner->zone_id}}&data[]={{$banner->data}}','choice_item');
        @endif
        $('#banner_form').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: "{{route('admin.banner.update', [$banner['id']])}}",
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
                        toastr.success("{{translate('messages.banner_updated_successfully')}}", {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function () {
                            location.href = "{{url()->full()}}";
                        }, 2000);
                    }
                }
            });
        });
    </script>
            <script>
                $('#reset_btn').click(function(){
                    // $('#module_select').val("{{$banner->module_id}}");
                    // $('#zone').val("{{$banner->zone_id}}").trigger('change');
                    // $('#viewer').attr('src','{{asset('storage/app/public/banner')}}/{{$banner['image']}}');
                    // $('#banner_type').val("{{$banner->type}}").trigger('change');
                    // $('#store_id').val("{{$banner->zone_id}}").trigger('change');
                    // $('#item_id').val("{{$banner->zone_id}}").trigger('change');
                    location.reload(true);
                })

            </script>
@endpush
