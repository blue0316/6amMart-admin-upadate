@extends('layouts.admin.app')

@section('title',translate('edit_coupon'))

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/edit.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.coupon')}} {{translate('messages.update')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.coupon.update',[$coupon['id']])}}" method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4 col-lg-3 col-sm-6">
                            <div class="form-group m-0">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.title')}}</label>
                                <input type="text" name="title" value="{{$coupon['title']}}" class="form-control"
                                       placeholder="{{translate('messages.new_coupon')}}" required maxlength="191">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3 col-sm-6">
                            <div class="form-group m-0">
                                <label class="input-label">{{translate('messages.module')}}</label>
                                <select name="module_id" required class="form-control js-select2-custom"  data-placeholder="{{translate('messages.select')}} {{translate('messages.module')}}" id="module_select" disabled>
                                    @foreach(\App\Models\Module::notParcel()->get() as $module)
                                        <option value="{{$module->id}}" {{$module->id==$coupon->module_id?'selected':''}}>{{$module->module_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3 col-sm-6">
                            <div class="form-group m-0">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.coupon')}} {{translate('messages.type')}}</label>
                                <select name="coupon_type" class="form-control" onchange="coupon_type_change(this.value)">
                                    <option value="store_wise" {{$coupon['coupon_type']=='store_wise'?'selected':''}}>{{translate('messages.store')}} {{translate('messages.wise')}}</option>
                                    <option value="zone_wise" {{$coupon['coupon_type']=='zone_wise'?'selected':''}}>{{translate('messages.zone')}} {{translate('messages.wise')}}</option>
                                    <option value="free_delivery" {{$coupon['coupon_type']=='free_delivery'?'selected':''}}>{{translate('messages.free_delivery')}}</option>
                                    <option value="first_order" {{$coupon['coupon_type']=='first_order'?'selected':''}}>{{translate('messages.first')}} {{translate('messages.order')}}</option>
                                    <option value="default" {{$coupon['coupon_type']=='default'?'selected':''}}>{{translate('messages.default')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3 col-sm-6" id="store_wise">
                            <div class="form-group m-0 ">
                                    <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.store')}}<span
                                            class="input-label-secondary"></span></label>
                                    <select name="store_ids[]" class="js-data-example-ajax form-control"  title="Select Restaurant">
                                    @if($coupon->coupon_type == 'store_wise')
                                    @php($store=\App\Models\Store::find(json_decode($coupon->data)[0]))
                                        @if($store)
                                        <option value="{{$store->id}}">{{$store->name}}</option>
                                        @endif
                                    @else
                                    <option selected>{{ translate('Select Store') }}</option>
                                    @endif
                                    </select>
                                </div>
                        </div>
                        <div class="col-md-4 col-lg-3 col-sm-6"  id="zone_wise">
                            <div class="form-group m-0 ">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.select')}} {{translate('messages.zone')}}</label>
                                <select name="zone_ids[]" id="choice_zones"
                                    class="form-control js-select2-custom"
                                    multiple="multiple" placeholder="{{translate('messages.select_zone')}}">
                                @foreach(\App\Models\Zone::all() as $zone)
                                    <option value="{{$zone->id}}" {{($coupon->coupon_type=='zone_wise'&&json_decode($coupon->data))?(in_array($zone->id, json_decode($coupon->data))?'selected':''):''}}>{{$zone->name}}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3 col-sm-6">
                            <div class="form-group m-0">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.code')}}</label>
                                <input type="text" name="code" class="form-control" value="{{$coupon['code']}}"
                                       placeholder="{{\Illuminate\Support\Str::random(8)}}" required maxlength="100">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3 col-sm-6">
                            <div class="form-group m-0">
                                <label class="input-label" for="limit">{{translate('messages.limit')}} {{translate('messages.for')}} {{translate('messages.same')}} {{translate('messages.user')}}</label>
                                <input type="number" name="limit" id="coupon_limit" value="{{$coupon['limit']}}" class="form-control" max="100"
                                       placeholder="EX: 10">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3 col-sm-6">
                            <div class="form-group m-0">
                                <label class="input-label" for="">{{translate('messages.start')}} {{translate('messages.date')}}</label>
                                <input type="date" name="start_date" class="form-control" id="date_from" placeholder="{{translate('messages.select_date')}}" value="{{date('Y-m-d',strtotime($coupon['start_date']))}}">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3 col-sm-6">
                            <div class="form-group m-0">
                                <label class="input-label" for="date_to">{{translate('messages.expire')}} {{translate('messages.date')}}</label>
                                <input type="date" name="expire_date" class="form-control" placeholder="{{translate('messages.select_date')}}" id="date_to" value="{{date('Y-m-d',strtotime($coupon['expire_date']))}}"
                                       data-hs-flatpickr-options='{
                                     "dateFormat": "Y-m-d"
                                   }'>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3 col-sm-6">
                            <div class="form-group m-0">
                                <label class="input-label" for="discount_type">{{translate('messages.discount')}} {{translate('messages.type')}}</label>
                                <select name="discount_type" id="discount_type" class="form-control">
                                    <option value="amount" {{$coupon['discount_type']=='amount'?'selected':''}}>{{translate('messages.amount')}}
                                    </option>
                                    <option value="percent" {{$coupon['discount_type']=='percent'?'selected':''}}>
                                        {{translate('messages.percent')}}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3 col-sm-6">
                            <div class="form-group m-0">
                                <label class="input-label" for="discount">{{translate('messages.discount')}}
                                    <span class="input-label-secondary text--title" data-toggle="tooltip"
                                        data-placement="right"
                                        data-original-title="{{ translate('Currently you need to manage discount with the Restaurant.') }}">
                                        <i class="tio-info-outined"></i>
                                    </span>
                                </label>
                                <input type="number" id="discount" min="1" max="999999999999.99" step="0.01" value="{{$coupon['discount']}}"
                                       name="discount" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3 col-sm-6">
                            <div class="form-group m-0">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.max')}} {{translate('messages.discount')}}</label>
                                <input type="number" min="0" max="999999999999.99" step="0.01" value="{{$coupon['max_discount']}}" name="max_discount" id="max_discount" class="form-control" {{$coupon['discount_type']=='amount'?'readonly="readonly"':''}}>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3 col-sm-6">
                            <div class="form-group m-0">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.min')}} {{translate('messages.purchase')}}</label>
                                <input type="number" name="min_purchase" step="0.01" value="{{$coupon['min_purchase']}}"
                                       min="0" max="999999999999.99" class="form-control"
                                       placeholder="100">
                            </div>
                        </div>
                    </div>
                    <div class="btn--container justify-content-end mt-4">
                        <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('messages.reset')}}</button>
                        <button type="submit" class="btn btn--primary">{{translate('messages.update')}}</button>
                    </div>
                </form>
            </div>
            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        coupon_type_change('{{$coupon->coupon_type}}');
        $("#date_from").on("change", function () {
            $('#date_to').attr('min',$(this).val());
        });

        $("#date_to").on("change", function () {
            $('#date_from').attr('max',$(this).val());
        });
        $(document).on('ready', function () {
            $('#discount_type').on('change', function() {
                if($('#discount_type').val() == 'amount')
                {
                    $('#max_discount').attr("readonly","true");
                    $('#max_discount').val(0);
                }
                else
                {
                    $('#max_discount').removeAttr("readonly");
                }
            });
            $('#date_from').attr('max','{{date("Y-m-d",strtotime($coupon["expire_date"]))}}');
            $('#date_to').attr('min','{{date("Y-m-d",strtotime($coupon["start_date"]))}}');

            var module_id = 0;
            $('#module_select').on('change', function(){
                if($(this).val())
                {
                    module_id = $(this).val();
                }
            });

            $('.js-data-example-ajax').select2({
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
            // INITIALIZATION OF FLATPICKR
            // =======================================================
            $('.js-flatpickr').each(function () {
                $.HSCore.components.HSFlatpickr.init($(this));
            });
        });

        function coupon_type_change(coupon_type) {
           if(coupon_type=='zone_wise')
            {
                $('#store_wise').hide();
                $('#zone_wise').show();
            }
            else if(coupon_type=='store_wise')
            {
                $('#store_wise').show();
                $('#zone_wise').hide();
            }
            else if(coupon_type=='first_order')
            {
                $('#zone_wise').hide();
                $('#store_wise').hide();
                $('#coupon_limit').val(1);
                $('#coupon_limit').attr("readonly","true");
            }
            else{
                $('#zone_wise').hide();
                $('#store_wise').hide();
                $('#coupon_limit').val('');
                $('#coupon_limit').removeAttr("readonly");
            }

            if(coupon_type=='free_delivery')
            {
                $('#discount_type').attr("disabled","true");
                $('#discount_type').val("").trigger( "change" );
                $('#max_discount').val(0);
                $('#max_discount').attr("readonly","true");
                $('#discount').val(0);
                $('#discount').attr("readonly","true");
            }
            else{
                $('#max_discount').removeAttr("readonly");
                $('#discount_type').removeAttr("disabled");
                $('#discount').removeAttr("readonly");
            }
        }
    </script>
        <script>
            $('#reset_btn').click(function(){
                location.reload(true);
            })

        </script>
@endpush
