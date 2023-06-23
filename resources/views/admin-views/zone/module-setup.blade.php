@extends('layouts.admin.app')

@section('title', translate('Zone Wise Module Setup'))

@push('css_or_js')
@endpush

@section('content')

    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/edit.png') }}" class="w--26" alt="">
                </span>
                <span>
                   {{ $zone->name }} {{ translate('Module Setup') }}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <form action="{{ route('admin.zone.module-update', $zone->id) }}" method="post" id="zone_form" class="shadow--card">
            @csrf
            <div class="row g-2">
                <div class="col-12">
                    <div class="d-flex flex-wrap select--all-checkes">
                        <h5 class="input-label m-0 text-capitalize">{{translate('messages.Payment Method')}} </h5>
                    </div>
                    <div class="check--item-wrapper mb-1">
                        <div class="check-item">
                            <div class="form-group form-check form--check">
                                <input type="checkbox" name="cash_on_delivery" value="cash_on_delivery" class="form-check-input"
                                       id="cash_on_delivery" {{$zone->cash_on_delivery == 1 ?'checked':''}}>
                                <label class="form-check-label qcont text-dark" for="cash_on_delivery">{{translate('messages.Cash On Delivery')}}</label>
                            </div>
                        </div>
                        <div class="check-item">
                            <div class="form-group form-check form--check">
                                <input type="checkbox" name="digital_payment" value="digital_payment" class="form-check-input"
                                       id="digital_payment" {{$zone->digital_payment == 1 ?'checked':''}}>
                                <label class="form-check-label qcont text-dark" for="digital_payment">{{translate('messages.digital payment')}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="input-label" for="exampleFormControlSelect1">{{ translate('messages.module') }}<span
                                class="input-label-secondary"></span></label>
                        <select name="module_id[]" id="choice_modules" class="form-control js-select2-custom"
                            multiple="multiple">
                            @php($modules_array = \App\Models\Module::get()->toArray())
                            @php($modules = \App\Models\Module::get())
                            @php($selected_modules = count($zone->modules) > 0 ? $zone->modules->pluck('id')->toArray() : [])
                            @foreach ($modules as $module)
                                <option value="{{ $module['id'] }}"
                                    {{ in_array($module['id'], $selected_modules) ? 'selected' : '' }}>
                                    {{ $module['module_name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="delivery_charge_options" id="delivery_charge_options">
                        <div class="row gy-1" id="mod-label">
                            <div class="col-sm-3">
                                <label for="">{{ translate('messages.Module Name') }}</label>
                            </div>
                            <div class="col-sm-3">
                                <label for="">{{ translate('messages.per_km_delivery_charge') }} ({{ \App\CentralLogics\Helpers::currency_symbol() }})</label>
                            </div>
                            <div class="col-sm-3">
                                <label for="">{{ translate('messages.Minimum delivery charge') }} ({{ \App\CentralLogics\Helpers::currency_symbol() }})</label>
                            </div>
                            <div class="col-sm-3">
                                <label for="">{{ translate('maximum_cod_order_amount') }} ({{ \App\CentralLogics\Helpers::currency_symbol() }})</label>
                            </div>
                        </div>
                        @if (count($zone->modules) > 0)
                            @foreach ($zone->modules as $module)
                            @if ($module->module_type == 'parcel')
                            <div class="row gy-1 module-row" id="module_{{ $module->id }}">
                                <div class="col-sm-3"><input type="text" class="form-control"
                                        value="{{ $module->module_name }}"
                                        placeholder="{{ translate('messages.choice_title') }}" readonly></div>
                                <div class="col-sm-3"><input type="number" class="form-control"
                                        name="module_data[{{ $module->id }}][per_km_shipping_charge]" step=".01"
                                        min="0" placeholder="{{ translate('Set charge from parcel category') }}"
                                        data-toggle="tooltip" data-placement="right"
                                        data-original-title="{{ translate('messages.You have to set category wise charge from parcel category') }}" readonly></div>
                                <div class="col-sm-3"><input type="number" step=".01" min="0"
                                        class="form-control"
                                        name="module_data[{{ $module->id }}][minimum_shipping_charge]"
                                        placeholder="{{ translate('Set charge from parcel category') }}"
                                        data-toggle="tooltip" data-placement="right"
                                        data-original-title="{{ translate('messages.You have to set category wise charge from parcel category') }}" readonly></div>
                                <div class="col-sm-3"><input type="number" step=".01" min="0"
                                    class="form-control"
                                    name="module_data[{{ $module->id }}][maximum_cod_order_amount]"
                                    placeholder="{{ translate('set_maximum_cod_order_amount') }}"
                                    title="{{ translate('set_maximum_cod_order_amount') }}"
                                    value="{{ $module->pivot->maximum_cod_order_amount }}" required></div>
                            </div>
                            @else
                            <div class="row gy-1 module-row" id="module_{{ $module->id }}">
                                <div class="col-sm-3"><input type="text" class="form-control"
                                        value="{{ $module->module_name }}"
                                        placeholder="{{ translate('messages.choice_title') }}" readonly></div>
                                <div class="col-sm-3"><input type="number" class="form-control"
                                        name="module_data[{{ $module->id }}][per_km_shipping_charge]" step=".01"
                                        min="0" placeholder="{{ translate('messages.per_km_delivery_charge') }}"
                                        title="{{ translate('messages.per_km_delivery_charge') }}"
                                        value="{{ $module->pivot->per_km_shipping_charge }}" required></div>
                                <div class="col-sm-3"><input type="number" step=".01" min="0"
                                        class="form-control"
                                        name="module_data[{{ $module->id }}][minimum_shipping_charge]"
                                        placeholder="{{ translate('messages.Minimum delivery charge') }}"
                                        title="{{ translate('messages.Minimum delivery charge') }}"
                                        value="{{ $module->pivot->minimum_shipping_charge }}" required></div>
                                <div class="col-sm-3"><input type="number" step=".01" min="0"
                                        class="form-control"
                                        name="module_data[{{ $module->id }}][maximum_cod_order_amount]"
                                        placeholder="{{ translate('set_maximum_cod_order_amount') }}"
                                        title="{{ translate('set_maximum_cod_order_amount') }}"
                                        value="{{ $module->pivot->maximum_cod_order_amount }}" required></div>
                            </div>
                            @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="btn--container mt-3 justify-content-end">
                <button type="submit" class="btn btn--primary">{{ translate('messages.update') }}</button>
            </div>
        </form>
    </div>

@endsection

@push('script_2')
    <script src="{{ asset('public/assets/admin') }}/js/tags-input.min.js"></script>
    <script>
        let modules = <?php echo json_encode($modules_array); ?>;
        let mod = {{ count($zone->modules) }};
        if(mod>0){
            $('#mod-label').show();
        }else{
            $('#mod-label').hide();
        }
        $('#choice_modules').on('change', function() {
            $('#mod-label').show();
            var ids = $('.module-row').map(function() {
                return $(this).attr('id').split('_')[1];
            }).get();

            $.each($("#choice_modules option:selected"), function(index, element) {
                console.log($(this).val());
                if (ids.includes($(this).val())) {
                    ids = ids.filter(id => id !== $(this).val());
                } else {
                    let name = $('#choice_modules option[value="' + $(this).val() + '"]').html();
                    var found = modules.find(modul=> modul.id == $(this).val());
                    if (found.module_type == 'parcel'){

                        add_parcel_module($(this).val(), name.trim());
                    }else{

                        add_more_delivery_charge_option($(this).val(), name.trim());
                    }
                }
            });
            console.log(ids)
            if (ids.length > 0) {
                ids.forEach(element => {
                    console.log("module_", 3)
                    $("#module_" + element.trim()).remove();
                });
            }
        });

        function add_more_delivery_charge_option(i, name) {
            let n = name;
            $('#delivery_charge_options').append(
                '<div class="row gy-1 module-row" id="module_' + i +
                '"><div class="col-sm-3"><input type="text" class="form-control" value="' + n +
                '" placeholder="{{ translate('messages.choice_title') }}" readonly></div><div class="col-sm-3"><input type="number" class="form-control" name="module_data[' +
                i +
                '][per_km_shipping_charge]" step=".01" min="0" placeholder="{{ translate('messages.per_km_delivery_charge') }}" title="{{ translate('messages.per_km_delivery_charge') }}" required></div><div class="col-sm-3"><input type="number" step=".01" min="0" class="form-control" name="module_data[' +
                i +
                '][minimum_shipping_charge]" placeholder="{{ translate('messages.Minimum delivery charge') }}" title="{{ translate('messages.Minimum delivery charge') }}" required></div><div class="col-sm-3"><input type="number" step=".01" min="0" class="form-control" name="module_data[' +
                i +
                '][maximum_cod_order_amount]" placeholder="{{ translate('set_maximum_cod_order_amount') }}" title="{{ translate('set_maximum_cod_order_amount') }}" required></div></div>'
            );
        }
        function add_parcel_module(i, name) {
            let n = name;
            $('#delivery_charge_options').append(
                '<div class="row gy-1 module-row" id="module_' + i +
                '"><div class="col-sm-3"><input type="text" class="form-control" value="' + n +
                '" placeholder="{{ translate('messages.choice_title') }}" readonly></div><div class="col-sm-3"><input type="number" name="module_data[' +
                i +
                '][per_km_shipping_charge]" class="form-control" step=".01" min="0" placeholder="{{ translate('Set charge from parcel category') }}" value="" title="{{ translate('messages.per_km_delivery_charge') }}" readonly></div><div class="col-sm-3"><input type="number" name="module_data[' +
                i +
                '][minimum_shipping_charge]" step=".01" min="0" class="form-control" placeholder="{{ translate('Set charge from parcel category') }}" value="" title="{{ translate('messages.Minimum delivery charge') }}" readonly></div><div class="col-sm-3"><input type="number" step=".01" min="0" class="form-control" name="module_data[' +
                i +
                '][maximum_cod_order_amount]" placeholder="{{ translate('set_maximum_cod_order_amount') }}" title="{{ translate('set_maximum_cod_order_amount') }}" required></div></div>'
            );
        }
    </script>
@endpush
