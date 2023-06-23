@extends('layouts.admin.app')

@section('title',$store->name."'s ".translate('messages.settings'))

@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{asset('public/assets/admin/css/croppie.css')}}" rel="stylesheet">

@endpush

@section('content')
<div class="content container-fluid">
    @include('admin-views.vendor.view.partials._header',['store'=>$store])
    <!-- Page Heading -->
    <div class="tab-content">
        <div class="tab-pane fade show active" id="vendor">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <span class="card-header-icon">
                            <img class="w--22" src="{{asset('public/assets/admin/img/store.png')}}" alt="">
                        </span>
                        <span>{{translate('messages.store')}} {{translate('messages.settings')}}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group mb-0">
                                <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="item_section">
                                <span class="pr-2">{{translate('messages.manage_item_setup')}}<span class="input-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{translate('By disabling this field, the store can`t manage items, which means the store web panel app won`t get the access for managing items')}}"><img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{translate('messages.show_hide_food_menu')}}"></span></span>
                                    <input type="checkbox" class="toggle-switch-input" onclick="location.href='{{route('admin.store.toggle-settings',[$store->id,$store->item_section?0:1, 'item_section'])}}'" name="item_section" id="item_section" {{$store->item_section?'checked':''}}>
                                    <span class="toggle-switch-label text">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group mb-0">
                                <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="reviews_section">
                                <span class="pr-2">{{translate('messages.Show_Reviews_In_Store_Panel')}}<span class="input-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{translate('If this field is active, the store panel & store app can see the customer`s review')}}"><img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{translate('messages.show_hide_food_menu')}}"></span> </span>
                                    <input type="checkbox" class="toggle-switch-input" onclick="location.href='{{route('admin.store.toggle-settings',[$store->id,$store->reviews_section?0:1, 'reviews_section'])}}'" name="reviews_section" id="reviews_section" {{$store->reviews_section?'checked':''}}>
                                    <span class="toggle-switch-label text">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group mb-0">
                                <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="pos_system">
                                <span class="pr-2 text-capitalize">{{translate('messages.include_POS_in_store_panel')}}<span class="input-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{translate('If this option is turned on, the store panel will get the
                                    Point of Sale (POS) option')}}"><img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{__('messages.pos_system_hint')}}"></span></span>
                                    <input type="checkbox" class="toggle-switch-input" onclick="location.href='{{route('admin.store.toggle-settings',[$store->id,$store->pos_system?0:1, 'pos_system'])}}'" id="pos_system" {{$store->pos_system?'checked':''}}>
                                    <span class="toggle-switch-label">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group mb-0">
                                <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="schedule_order">
                                <span class="pr-2">{{translate('messages.scheduled')}} {{translate('messages.order_option')}}<span class="input-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{translate('If this status is turned on, the customer is able to place a scheduled
                                    order for this store')}}"><img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{__('messages.scheduled_order_hint')}}"></span></span>
                                    <input type="checkbox" class="toggle-switch-input" onclick="location.href='{{route('admin.store.toggle-settings',[$store->id,$store->schedule_order?0:1, 'schedule_order'])}}'" id="schedule_order" {{$store->schedule_order?'checked':''}}>
                                    <span class="toggle-switch-label">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group mb-0">
                                <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="self_delivery_system">
                                <span class="pr-2 text-capitalize">{{translate('messages.self_delivery')}}<span class="input-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{translate('When this option is enabled, stores need to deliver orders by themselves or
                                    by their own delivery man. Stores will also get an option for adding their own delivery man
                                    from the store panel')}}"><img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{__('messages.self_delivery_hint')}}"></span></span>
                                    <input type="checkbox" class="toggle-switch-input" onclick="location.href='{{route('admin.store.toggle-settings',[$store->id,$store->self_delivery_system?0:1, 'self_delivery_system'])}}'" id="self_delivery_system" {{$store->self_delivery_system?'checked':''}}>
                                    <span class="toggle-switch-label">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group mb-0">
                                <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="delivery">
                                    <span class="pr-2">{{translate('messages.home_delivery')}}<span class="input-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{translate('messages.home_delivery_hint')}}"><img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{__('messages.home_delivery_hint')}}"></span></span>
                                    <input type="checkbox" name="delivery" class="toggle-switch-input" onclick="location.href='{{route('admin.store.toggle-settings',[$store->id,$store->delivery?0:1, 'delivery'])}}'" id="delivery" {{$store->delivery?'checked':''}}>
                                    <span class="toggle-switch-label">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group mb-0">
                                <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="take_away">
                                <span class="pr-2 text-capitalize">{{translate('messages.take_away')}}<span class="input-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{__('messages.take_away_hint')}}"><img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{__('messages.take_away_hint')}}"></span></span>
                                    <input type="checkbox" class="toggle-switch-input" onclick="location.href='{{route('admin.store.toggle-settings',[$store->id,$store->take_away?0:1, 'take_away'])}}'" id="take_away" {{$store->take_away?'checked':''}}>
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
                                <div class="col-sm-6 col-lg-4">
                                    <div class="form-group mb-0">
                                        <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="prescription_order">
                                        <span class="pr-2 text-capitalize">{{translate('messages.prescription_order')}}<span class="input-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{__('messages.prescription_order_hint')}}"><img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{__('messages.prescription_order_hint')}}"></span></span>
                                            <input type="checkbox" class="toggle-switch-input" onclick="location.href='{{route('admin.store.toggle-settings',[$store->id,$store->prescription_order?0:1, 'prescription_order'])}}'" id="prescription_order" {{$store->prescription_order?'checked':''}}>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                    <div class="row g-3 mt-3">
                        <form action="{{route('admin.store.update-settings',[$store['id']])}}" method="post"
                            enctype="multipart/form-data" class="col-12">
                            @csrf
                            <div class="row">
                                @if ($toggle_veg_non_veg && config('module.'.$store->module->module_type)['veg_non_veg'])
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label">{{translate('store_type')}}</label>
                                            <div class="resturant-type-group border rounded px-3 d-flex flex-wrap min--h-45px">
                                                <label class="form-check form--check mr-2 mr-md-4">
                                                    <input class="form-check-input" type="radio" name="veg_non_veg" value="veg" {{$store->veg && !$store->non_veg?'checked':''}}>
                                                    <span class="form-check-label">
                                                        {{translate('messages.veg')}}
                                                    </span>
                                                </label>
                                                <label class="form-check form--check mr-2 mr-md-4">
                                                    <input class="form-check-input" type="radio" name="veg_non_veg" value="non_veg" {{!$store->veg && $store->non_veg?'checked':''}}>
                                                    <span class="form-check-label">
                                                        {{translate('messages.non_veg')}}
                                                    </span>
                                                </label>
                                                <label class="form-check form--check">
                                                    <input class="form-check-input" type="radio" name="veg_non_veg" value="both" {{$store->veg && $store->non_veg?'checked':''}}>
                                                    <span class="form-check-label">
                                                        {{translate('messages.both')}}
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="form-group col-sm-6 col-lg-4">
                                    <label class="input-label text-capitalize">{{translate('messages.minimum')}} {{translate('messages.order')}} {{translate('messages.amount')}}</label>
                                    <input type="number" name="minimum_order" step="0.01" min="0" max="100000" class="form-control" placeholder="100" value="{{$store->minimum_order??'0'}}">
                                </div>
                                @if (config('module.'.$store->module->module_type)['order_place_to_schedule_interval'])
                                <div class="form-group col-sm-6 col-lg-4">
                                    <label class="input-label text-capitalize" for="maximum_delivery_time">{{translate('messages.minimum_processing_time')}}<span class="input-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{translate('messages.minimum_processing_time_warning')}}"><img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{translate('messages.minimum_processing_time_warning')}}"></span></label>
                                    <input type="text" name="order_place_to_schedule_interval" class="form-control" value="{{$store->order_place_to_schedule_interval}}">
                                </div>
                                @endif
                                <div class="form-group col-sm-6 col-lg-4">
                                    <label class="input-label text-capitalize" for="maximum_delivery_time">{{translate('messages.approx_delivery_time')}}</label>
                                    <div class="input-group">
                                        <input type="number" name="minimum_delivery_time" class="form-control" placeholder="Min: 10" value="{{explode('-',$store->delivery_time)[0]}}" data-toggle="tooltip" data-placement="top" data-original-title="{{translate('messages.minimum_delivery_time')}}">
                                        <input type="number" name="maximum_delivery_time" class="form-control" placeholder="Max: 20" value="{{explode(' ',explode('-',$store->delivery_time)[1])[0]}}" data-toggle="tooltip" data-placement="top" data-original-title="{{translate('messages.maximum_delivery_time')}}">
                                        <select name="delivery_time_type" class="form-control text-capitalize" id="" required>
                                            <option value="min" {{explode(' ',explode('-',$store->delivery_time)[1])[1]=='min'?'selected':''}}>{{translate('messages.minutes')}}</option>
                                            <option value="hours" {{explode(' ',explode('-',$store->delivery_time)[1])[1]=='hours'?'selected':''}}>{{translate('messages.hours')}}</option>
                                            <option value="days" {{explode(' ',explode('-',$store->delivery_time)[1])[1]=='days'?'selected':''}}>{{translate('messages.days')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6 col-lg-4">
                                    <div class="form-group">
                                        <label class="d-flex justify-content-between switch toggle-switch-sm text-dark" for="tax">
                                            <span>{{translate('messages.vat/tax')}}(%)</span>
                                        </label>
                                        <input type="number" id="tax" min="0" max="100" step="0.01" name="tax" class="form-control" required value="{{$store->tax??'0'}}" {{isset($store->tax)?'':'readonly'}}>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6 col-lg-4">
                                    <div class="form-group">
                                        <label class="d-flex mb-1 justify-content-between switch toggle-switch-sm text-dark text-capitalize" for="comission_status">
                                            <span>{{translate('messages.admin_commission')}}(%) <span class="input-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{translate('messages.if_sales_commission_disabled_system_default_will_be_applicable')}}"><img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{translate('messages.if_sales_commission_disabled_system_default_will_be_applicable')}}"></span></span>
                                            <input type="checkbox" class="toggle-switch-input" name="comission_status" id="comission_status" value="1" {{isset($store->comission)?'checked':''}}>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                        <input type="number" id="comission" min="0" max="10000" step="0.01" name="comission" class="form-control" required value="{{$store->comission??'0'}}" {{isset($store->comission)?'':'readonly'}}>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="justify-content-end btn--container">
                                        <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                                        <button type="submit" class="btn btn--primary">{{translate('save_changes')}}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @if (!config('module.'.$store->module->module_type)['always_open'])
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">
                        <span class="card-header-icon"><i class="tio-clock"></i></span>
                        <span>{{translate('messages.Daily time schedule')}}</span>
                    </h5>
                </div>
                <div class="card-body" id="schedule">
                    @include('admin-views.vendor.view.partials._schedule', $store)
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Create schedule modal -->

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{translate('messages.Create Schedule')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="javascript:" method="post" id="add-schedule">
                    @csrf
                    <input type="hidden" name="day" id="day_id_input">
                    <input type="hidden" name="store_id" value="{{$store->id}}">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">{{translate('messages.Start time')}}:</label>
                        <input type="time" class="form-control" name="start_time" required>
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">{{translate('messages.End time')}}:</label>
                        <input type="time" class="form-control" name="end_time" required>
                    </div>
                    <button type="submit" class="btn btn-primary">{{translate('messages.Submit')}}</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script_2')
    <!-- Page level plugins -->
    <script>
        // Call the dataTables jQuery plugin
        $(document).ready(function () {
            $('#dataTable').DataTable();

            $('#exampleModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var day_name = button.data('day');
                var day_id = button.data('dayid');
                var modal = $(this);
                modal.find('.modal-title').text('{{translate('messages.Create Schedule For ')}} ' + day_name);
                modal.find('.modal-body input[name=day]').val(day_id);
            })
        });
    </script>
    <script>
        $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

            $('#column1_search').on('keyup', function () {
                datatable
                    .columns(1)
                    .search(this.value)
                    .draw();
            });

            $('#column2_search').on('keyup', function () {
                datatable
                    .columns(2)
                    .search(this.value)
                    .draw();
            });

            $('#column3_search').on('change', function () {
                datatable
                    .columns(3)
                    .search(this.value)
                    .draw();
            });

            $('#column4_search').on('keyup', function () {
                datatable
                    .columns(4)
                    .search(this.value)
                    .draw();
            });


            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
            $("#comission_status").on('change', function(){
                if($("#comission_status").is(':checked')){
                    $('#comission').removeAttr('readonly');
                } else {
                    $('#comission').attr('readonly', true);
                    $('#comission').val('0');
                }
            });

        });

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

        $('#add-schedule').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.store.add-schedule')}}',
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
