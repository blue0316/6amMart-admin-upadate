@extends('layouts.vendor.app')

@section('title',translate('messages.settings'))

@push('css_or_js')
<link href="{{asset('public/assets/admin/css/croppie.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid config-inline-remove-class">
        <!-- Page Heading -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/config.png')}}" class="w--30" alt="">
                </span>
                <span>
                    {{translate('messages.store')}} {{translate('messages.setup')}}
                </span>
            </h1>
        </div>
        <!-- Page Heading -->
        <div class="card mb-3">
            <div class="card-body py-3">
                <div class="d-flex flex-row justify-content-between align-items-center">
                    <h4 class="card-title align-items-center d-flex">
                        <img src="{{asset('public/assets/admin/img/store.png')}}" class="w--20 mr-1" alt="">
                        <span>{{translate('messages.store_temporarily_closed_title')}}</span>
                    </h4>
                    <label class="switch toggle-switch-lg m-0">
                        <input type="checkbox" class="toggle-switch-input" onclick="restaurant_open_status(this)"
                            {{$store->active ?'':'checked'}}>
                        <span class="toggle-switch-label">
                            <span class="toggle-switch-indicator"></span>
                        </span>
                    </label>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title">
                    <span class="card-header-icon">
                        <i class="tio-settings-outlined"></i>
                    </span>
                    <span>
                        {{translate('messages.store')}} {{translate('messages.settings')}}
                    </span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="form-group mb-0">
                            <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="schedule_order">
                                <span class="pr-2">{{translate('messages.scheduled')}} {{translate('messages.order')}}:</span>
                                <input type="checkbox" class="toggle-switch-input" onclick="location.href='{{route('vendor.business-settings.toggle-settings',[$store->id,$store->schedule_order?0:1, 'schedule_order'])}}'" id="schedule_order" {{$store->schedule_order?'checked':''}}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="form-group mb-0">
                            <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="delivery">
                                <span class="pr-2">{{translate('messages.delivery')}}:</span>
                                <input type="checkbox" name="delivery" class="toggle-switch-input" onclick="location.href='{{route('vendor.business-settings.toggle-settings',[$store->id,$store->delivery?0:1, 'delivery'])}}'" id="delivery" {{$store->delivery?'checked':''}}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="form-group mb-0">
                            <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="take_away">
                                <span class="pr-2 text-capitalize">{{translate('messages.take_away')}}:</span>
                                <input type="checkbox" class="toggle-switch-input" onclick="location.href='{{route('vendor.business-settings.toggle-settings',[$store->id,$store->take_away?0:1, 'take_away'])}}'" id="take_away" {{$store->take_away?'checked':''}}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </div>
                    </div>
                    
                    @if ($store->module->module_type == 'pharmacy')
                    @php($prescription_order_status = \App\Models\BusinessSetting::where('key', 'prescription_order_status')->first())
                    @php($prescription_order_status = $prescription_order_status ? $prescription_order_status->value : 0)
                        @if ($prescription_order_status)
                            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                                <div class="form-group mb-0">
                                    <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="prescription_order">
                                        <span class="pr-2 text-capitalize">{{translate('messages.prescription_order')}}:</span>
                                        <input type="checkbox" class="toggle-switch-input" onclick="location.href='{{route('vendor.business-settings.toggle-settings',[$store->id,$store->prescription_order?0:1, 'prescription_order'])}}'" id="prescription_order" {{$store->prescription_order?'checked':''}}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        @endif
                    @endif
                    @if ($store->self_delivery_system == 1)
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group m-0">
                            <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border rounded px-3 form-control" for="free_delivery">
                                <span class="pr-2">
                                    {{translate('messages.free_delivery')}}:
                                    <span data-toggle="tooltip" data-placement="right" data-original-title="{{translate('If this option is on, customers will get free delivery')}}" class="input-label-secondary"><img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="i"></span>
                                </span>
                                <input type="checkbox" name="free_delivery" class="toggle-switch-input" onclick="location.href='{{route('vendor.business-settings.toggle-settings',[$store->id,$store->free_delivery?0:1, 'free_delivery'])}}'" id="free_delivery" {{$store->free_delivery?'checked':''}}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </div>
                    </div>
                    @endif
                    @if ($toggle_veg_non_veg && config('module.'.$store->module->module_type)['veg_non_veg'])
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="form-group mb-0">
                            <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="veg">
                                <span class="pr-2 text-capitalize">{{translate('messages.veg')}}:</span>
                                <input type="checkbox" class="toggle-switch-input" onclick="location.href='{{route('vendor.business-settings.toggle-settings',[$store->id,$store->veg?0:1, 'veg'])}}'" id="veg" {{$store->veg?'checked':''}}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="form-group mb-0">
                            <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="non_veg">
                                <span class="pr-2 text-capitalize">{{translate('messages.non_veg')}}:</span>
                                <input type="checkbox" class="toggle-switch-input" onclick="location.href='{{route('vendor.business-settings.toggle-settings',[$store->id,$store->non_veg?0:1, 'non_veg'])}}'" id="non_veg" {{$store->non_veg?'checked':''}}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title">
                    <span class="card-header-icon">
                        <i class="tio-settings-outlined"></i>
                    </span>
                    <span>
                        {{translate('messages.basic')}} {{translate('messages.settings')}}
                    </span>
                </h5>
            </div>
            <div class="card-body">
                <form action="{{route('vendor.business-settings.update-setup',[$store['id']])}}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group mb-0 col-md-4">
                            <label class="input-label text-capitalize" for="title">{{translate('messages.minimum')}} {{translate('messages.order')}} {{translate('messages.amount')}}</label>
                            <input type="number" name="minimum_order" step="0.01" min="0" max="100000" class="form-control" placeholder="100" value="{{$store->minimum_order??'0'}}">
                        </div>
                        @if (config('module.'.$store->module->module_type)['order_place_to_schedule_interval'])
                        <div class="form-group mb-0 col-md-4">
                            <label class="input-label text-capitalize" for="maximum_delivery_time">{{translate('messages.minimum_processing_time')}}<span class="input-label-secondary" title="{{translate('messages.minimum_processing_time_warning')}}"><img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{translate('messages.minimum_processing_time_warning')}}"></span></label>
                            <input type="text" name="order_place_to_schedule_interval" class="form-control" value="{{$store->order_place_to_schedule_interval}}">
                        </div>
                        @endif
                        <div class="form-group mb-0 col-md-4">
                            <label class="input-label text-capitalize" for="maximum_delivery_time">{{translate('messages.approx_delivery_time')}}</label>
                            <div class="input-group">
                                <input type="number" name="minimum_delivery_time" class="form-control" placeholder="Min: 10" value="{{explode('-',$store->delivery_time)[0]}}" title="{{translate('messages.minimum_delivery_time')}}">
                                <input type="number" name="maximum_delivery_time" class="form-control" placeholder="Max: 20" value="{{explode(' ',explode('-',$store->delivery_time)[1])[0]}}" title="{{translate('messages.maximum_delivery_time')}}">
                                <select name="delivery_time_type" class="form-control text-capitalize" id="" required>
                                    <option value="min" {{explode(' ',explode('-',$store->delivery_time)[1])[1]=='min'?'selected':''}}>{{translate('messages.minutes')}}</option>
                                    <option value="hours" {{explode(' ',explode('-',$store->delivery_time)[1])[1]=='hours'?'selected':''}}>{{translate('messages.hours')}}</option>
                                    <option value="days" {{explode(' ',explode('-',$store->delivery_time)[1])[1]=='days'?'selected':''}}>{{translate('messages.days')}}</option>
                                </select>
                            </div>
                        </div>
                        {{-- @if($store->self_delivery_system)
                        <div class="col-sm-{{$store->self_delivery_system?'4':'6'}}">
                            <div class="form-group mb-0">
                                <label class="input-label text-capitalize" for="title">{{translate('messages.delivery_charge')}}</label>
                                <input type="number" name="delivery_charge" step="0.01" min="0" max="100000" class="form-control" placeholder="100" value="{{$store->delivery_charge??'0'}}">
                            </div>
                        </div>
                        @endif --}}
                        @if($store->self_delivery_system)
                        <div class="col-sm-{{$store->self_delivery_system?'4':'6'}} col-12">
                            <div class="form-group mt-3">
                                <label class="input-label text-capitalize" for="minimum_shipping_charge">{{translate('messages.minimum_shipping_charge')}} ({{\App\CentralLogics\Helpers::currency_symbol()}})
                                </label>
                                <input type="number" id="minimum_shipping_charge" min="0" max="99999999.99" step="0.01" name="minimum_delivery_charge" class="form-control shipping_input" value="{{isset($store->minimum_shipping_charge) ? $store->minimum_shipping_charge : ''}}">
                            </div>
                        </div>

                        <div class="col-sm-{{$store->self_delivery_system?'4':'6'}} col-12">
                            <div class="form-group mt-3">
                                <label class="input-label text-capitalize" for="title">{{translate('messages.delivery_charge_per_km')}} ({{\App\CentralLogics\Helpers::currency_symbol()}})</label>
                                <input type="number" name="per_km_delivery_charge" step="0.01" min="0" max="100000" class="form-control" placeholder="100" value="{{$store->per_km_shipping_charge??'0'}}">
                            </div>
                        </div>
                        @endif

                        {{-- <div class="col-sm-{{$store->self_delivery_system?'4':'6'}}">
                            <div class="form-group mb-0 p-2 border">
                                <label class="d-flex justify-content-between switch toggle-switch-sm text-dark" for="gst_status">
                                    <span>{{translate('messages.gst')}} <span class="input-label-secondary" title="{{translate('messages.gst_status_warning')}}"><img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{translate('messages.gst_status_warning')}}"></span></span>
                                    <input type="checkbox" class="toggle-switch-input" name="gst_status" id="gst_status" value="1" {{$store->gst_status?'checked':''}}>
                                    <span class="toggle-switch-label">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                                <input type="text" id="gst" name="gst" class="form-control" value="{{$store->gst_code}}" {{isset($store->gst_status)?'':'readonly'}}>
                            </div>
                        </div> --}}
                        <div class="col-12">
                            <div class="btn--container mt-3 justify-content-end">
                                <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                                <button type="submit" class="btn btn--primary">{{translate('messages.update')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @if (!config('module.'.$store->module->module_type)['always_open'])
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title">
                    <span class="card-header-icon">
                        <i class="tio-date-range"></i>
                    </span>
                    <span>
                        {{translate('messages.Daily time schedule')}}
                    </span>
                </h5>
            </div>
            <div class="card-body" id="schedule">
                @include('vendor-views.business-settings.partials._schedule', $store)
            </div>
        </div>
        @endif
    </div>

    <!-- Create schedule modal -->

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{translate('messages.Create Schedule For ')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="javascript:" method="post" id="add-schedule">
                        @csrf
                        <input type="hidden" name="day" id="day_id_input">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">{{translate('messages.Start time')}}:</label>
                            <input type="time" class="form-control" name="start_time" required>
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="col-form-label">{{translate('messages.End time')}}:</label>
                            <input type="time" class="form-control" name="end_time" required>
                        </div>
                        <div class="btn--container justify-content-end">
                            <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                            <button type="submit" class="btn btn--primary">{{translate('messages.Submit')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        function restaurant_open_status(e) {
            Swal.fire({
                title: '{{translate('messages.are_you_sure')}}',
                text: '{{$store->active ? translate('messages.you_want_to_temporarily_close_this_store') : translate('messages.you_want_to_open_this_store') }}',
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#00868F',
                cancelButtonText: '{{translate('messages.no')}}',
                confirmButtonText: '{{translate('messages.yes')}}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.get({
                        url: '{{route('vendor.business-settings.update-active-status')}}',
                        contentType: false,
                        processData: false,
                        beforeSend: function () {
                            $('#loading').show();
                        },
                        success: function (data) {
                            toastr.success(data.message);
                        },
                        complete: function () {
                            $('#loading').hide();
                            location.reload();
                        },
                    });
                } else {
                    e.checked = !e.checked;
                }
            })
        };

        function delete_schedule(route) {
            Swal.fire({
                title: '{{translate('messages.are_you_sure')}}',
                text: '{{translate('messages.You want to remove this schedule')}}',
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#00868F',
                cancelButtonText: '{{translate('messages.no')}}',
                confirmButtonText: '{{translate('messages.yes')}}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.get({
                        url: route,
                        beforeSend: function () {
                            $('#loading').show();
                        },
                        success: function (data) {
                            if (data.errors) {
                                for (var i = 0; i < data.errors.length; i++) {
                                    toastr.error(data.errors[i].message, {
                                        CloseButton: true,
                                        ProgressBar: true
                                    });
                                }
                            } else {
                                $('#schedule').empty().html(data.view);
                                toastr.success('{{translate('messages.Schedule removed successfully')}}', {
                                    CloseButton: true,
                                    ProgressBar: true
                                });
                            }
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            toastr.error('{{translate('messages.Schedule not found')}}', {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        },
                        complete: function () {
                            $('#loading').hide();
                        },
                    });
                }
            })
        };


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

        $(document).on('ready', function () {
            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });

            $("#gst_status").on('change', function(){
                if($("#gst_status").is(':checked')){
                    $('#gst').removeAttr('readonly');
                } else {
                    $('#gst').attr('readonly', true);
                }
            });
        });

        $('#exampleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var day_name = button.data('day');
            var day_id = button.data('dayid');
            var modal = $(this);
            modal.find('.modal-title').text('{{translate('messages.Create Schedule For ')}} ' + day_name);
            modal.find('.modal-body input[name=day]').val(day_id);
        })

        $('#add-schedule').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('vendor.business-settings.add-schedule')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    if (data.errors) {
                        for (var i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        $('#schedule').empty().html(data.view);
                        $('#exampleModal').modal('hide');
                        toastr.success('{{translate('messages.Schedule added successfully')}}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    toastr.error(XMLHttpRequest.responseText, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });
    </script>
@endpush
