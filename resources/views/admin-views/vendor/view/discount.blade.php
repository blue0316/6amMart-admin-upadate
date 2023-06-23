@extends('layouts.admin.app')

@section('title',$store->name."'s ".translate('messages.discount'))

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
                        <i class="tio-info"></i>
                        <span>{{translate('messages.discount')}} {{translate('messages.info')}}</span>
                    </h5>
                    <div class="btn--container justify-content-end">
                        @if($store->discount)
                        <button type="button" class="btn-sm btn--primary" data-toggle="modal" data-target="#updatesettingsmodal">
                            <i class="tio-open-in-new"></i> {{translate('messages.update')}}
                        </button>
                        <button type="button" onclick="form_alert('discount-{{$store->id}}','{{ translate('Want to remove discount?') }}')" class="btn btn--danger text-white"><i class="tio-delete-outlined"></i>  {{translate('messages.delete')}}</button>
                        @else
                        <button type="button" class="btn-sm btn--primary" data-toggle="modal" data-target="#updatesettingsmodal">
                            <i class="tio-add"></i> {{translate('messages.add').' '.translate('messages.discount')}}
                        </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if($store->discount)
                    <div class="text--info mb-3">
                        {{translate('* This discount is applied on all the items in your store')}}
                    </div>
                    <div class="row gy-3">
                        <div class="col-md-4 align-self-center text-center">

                            <div class="discount-item text-center">
                                <h5 class="subtitle">{{translate('messages.discount')}} {{translate('messages.amount')}}</h5>
                                <h4 class="amount">{{$store->discount?round($store->discount->discount):0}}%</h4>
                            </div>

                        </div>
                        <div class="col-md-4 text-center text-md-left">
                            <div class="discount-item">
                                <h5 class="subtitle">{{translate('messages.duration')}}</h5>
                                <ul class="list-unstyled list-unstyled-py-3 text-dark">
                                    <li class="p-0 pt-1 justify-content-center justify-content-md-start">
                                        <span>{{translate('messages.start')}} {{translate('messages.date')}} :</span>
                                        <strong>{{$store->discount?date('Y-m-d',strtotime($store->discount->start_date)):''}} {{$store->discount?date(config('timeformat'), strtotime($store->discount->start_time)):''}}</strong>
                                    </li>
                                    <li class="p-0 pt-1 justify-content-center justify-content-md-start">
                                        <span>{{translate('messages.end')}} {{translate('messages.date')}} :</span>
                                        <strong>{{$store->discount?date('Y-m-d', strtotime($store->discount->end_date)):''}} {{$store->discount?date(config('timeformat'), strtotime($store->discount->end_time)):''}}</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4 text-center text-md-left">

                            <h5 class="subtitle">{{translate('purchase_conditions')}}</h5>

                            <ul class="list-unstyled list-unstyled-py-3 text-dark">
                                <li class="p-0 pt-1 justify-content-center justify-content-md-start">
                                    <span>{{translate('messages.max')}} {{translate('messages.discount')}} :</span>
                                    <strong>{{\App\CentralLogics\Helpers::format_currency($store->discount?$store->discount->max_discount:0)}}</strong>
                                </li>
                                <li class="p-0 pt-1 justify-content-center justify-content-md-start">
                                    <span>{{translate('messages.min')}} {{translate('messages.purchase')}} :</span>
                                    <strong>{{\App\CentralLogics\Helpers::format_currency($store->discount?$store->discount->min_purchase:0)}}</strong>
                                </li>
                            </ul>

                        </div>
                    </div>
                    @else
                    <div class="text-center">
                        <span class="card-subtitle">{{translate('no_discount_created_yet')}}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="updatesettingsmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header shadow py-3">
        <h5 class="modal-title" id="exampleModalCenterTitle">{{$store->discount?translate('messages.update'):translate('messages.add')}} {{translate('messages.discount')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body pb-4 pt-4">
        <form action="{{route('admin.store.discount',[$store['id']])}}" method="post" id="discount-form">
            @csrf
            <div class="row gx-2">
                <div class="col-md-4 col-6">
                    <div class="form-group">
                        <label class="input-label" for="title">{{translate('messages.discount_amount')}} (%)</label>
                        <input type="number" min="0" max="100" step="0.01" name="discount" class="form-control" required value="{{$store->discount?$store->discount->discount:'0'}}">
                    </div>
                </div>
                <div class="col-md-4 col-6">
                    <div class="form-group">
                        <label class="input-label" for="title">{{translate('messages.min')}} {{translate('messages.purchase')}} ({{\App\CentralLogics\Helpers::currency_symbol()}})</label>
                        <input type="number" name="min_purchase" step="0.01" min="0" max="100000" class="form-control" placeholder="100" value="{{$store->discount?$store->discount->min_purchase:'0'}}">
                    </div>
                </div>
                <div class="col-md-4 col-6">
                    <div class="form-group">
                        <label class="input-label" for="title">{{translate('messages.max')}} {{translate('messages.discount')}} ({{\App\CentralLogics\Helpers::currency_symbol()}})</label>
                        <input type="number" min="0" max="1000000" step="0.01" name="max_discount" class="form-control" value="{{$store->discount?$store->discount->max_discount:'0'}}">
                    </div>
                </div>
            </div>
            <div class="row gx-2">
                <div class="col-md-6 col-6">
                    <div class="form-group">
                        <label class="input-label" for="title">{{translate('messages.start')}} {{translate('messages.date')}}</label>
                        <input type="date" id="date_from" class="form-control" required name="start_date" value="{{$store->discount?date('Y-m-d',strtotime($store->discount->start_date)):''}}">
                    </div>
                </div>
                <div class="col-md-6 col-6">
                    <div class="form-group">
                        <label class="input-label" for="title">{{translate('messages.end')}} {{translate('messages.date')}}</label>
                        <input type="date" id="date_to" class="form-control" required name="end_date" value="{{$store->discount?date('Y-m-d', strtotime($store->discount->end_date)):''}}">
                    </div>

                </div>
                <div class="col-md-6 col-6">
                    <div class="form-group">
                        <label class="input-label" for="title">{{translate('messages.start')}} {{translate('messages.time')}}</label>
                        <input type="time" id="start_time" class="form-control" required name="start_time" value="{{$store->discount?date('H:i',strtotime($store->discount->start_time)):'00:00'}}">
                    </div>
                </div>
                <div class="col-md-6 col-6">
                    <label class="input-label" for="title">{{translate('messages.end')}} {{translate('messages.time')}}</label>
                    <input type="time" id="end_time" class="form-control" required name="end_time" value="{{$store->discount?date('H:i', strtotime($store->discount->end_time)):'23:59'}}">
                </div>
            </div>
            <div class="btn--container justify-content-end">
                <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                @if($store->discount)
                    <button type="submit" class="btn btn--primary"><i class="tio-open-in-new"></i> {{translate('messages.update')}}</button>
                @else
                    <button type="submit" class="btn btn--primary">{{translate('messages.add')}}</button>
                @endif
            </div>
        </form>
        <form action="{{route('admin.store.clear-discount',[$store->id])}}" method="post" id="discount-{{$store->id}}">
            @csrf @method('delete')
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('script_2')
    <script>
        $(document).on('ready', function () {
            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
            $('#date_from').attr('min',(new Date()).toISOString().split('T')[0]);
            $('#date_to').attr('min',(new Date()).toISOString().split('T')[0]);

            $("#date_from").on("change", function () {
                $('#date_to').attr('min',$(this).val());
            });

            $("#date_to").on("change", function () {
                $('#date_from').attr('max',$(this).val());
            });
        });

        $('#discount-form').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.store.discount',[$store['id']])}}',
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
                        toastr.success(data.message, {
                            CloseButton: true,
                            ProgressBar: true
                        });

                        setTimeout(function () {
                            location.href = '{{route('admin.store.view', ['store'=>$store->id, 'tab'=> 'discount'])}}';
                        }, 2000);
                    }
                }
            });
        });
    </script>
@endpush
