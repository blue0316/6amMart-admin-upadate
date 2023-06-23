@extends('layouts.admin.app')

@section('title',translate('messages.edit').' '.translate('messages.account_transaction'))

@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-2">
        <!-- <h4 class=" mb-0 text-black-50">{{translate('messages.account_transaction')}}</h4> -->
    </div>
    <div class="card">
        <div class="card-header">
            <h4 class="text-capitalize">{{translate('messages.add')}} {{translate('messages.account_transaction')}}</h4>
        </div>
        <div class="card-body">
            <form action="{{route('admin.account-transaction.store')}}" method='post' id="add_transaction">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                        <label class="input-label" for="type">{{translate('messages.type')}}<span class="input-label-secondary"></span></label>
                            <select name="type" id="type" class="form-control">
                                <option value="deliveryman" {{$account_transaction->from_type=='deliveryman'?'selected':''}}>{{translate('messages.deliveryman')}}</option>
                                <option value="store" {{$account_transaction->from_type=='deliveryman'?'selected':''}}>{{translate('messages.store')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="input-label" for="store">{{translate('messages.store')}}<span class="input-label-secondary"></span></label>
                            <select id="store" name="store_id" data-placeholder="{{translate('messages.select')}} {{translate('messages.store')}}" class="form-control" title="Select Restaurant" {{$account_transaction->deliveryman?'disabled':''}}>

                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="input-label" for="deliveryman">{{translate('messages.deliveryman')}}<span class="input-label-secondary"></span></label>
                            <select id="deliveryman" name="deliveryman_id" data-placeholder="{{translate('messages.select')}} {{translate('messages.deliveryman')}}" class="form-control" title="Select deliveryman" {{$account_transaction->store?'disabled':''}}>

                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="input-label" for="method">{{translate('messages.method')}}<span class="input-label-secondary"></span></label>
                            <input class="form-control" type="text" name="method" id="method" value="{{$account_transaction->method}}" maxlength="191">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="input-label" for="ref">{{translate('messages.reference')}}<span class="input-label-secondary"></span></label>
                            <input  class="form-control" type="text" name="ref" id="ref" value="{{$account_transaction->ref}}" maxlength="191">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="input-label" for="amount">{{translate('messages.amount')}}<span class="input-label-secondary"></span></label>
                            <input class="form-control" type="number" min=".01" step="0.01" name="amount" id="amount" value="{{$account_transaction->amount}}" max="999999999999.99">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <input class="btn btn-primary" type="submit" value="{{translate('messages.save')}}" >
                </div>
            </form>
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

        $('#type').on('change', function() {
            if($('#type').val() == 'store')
            {
                $('#store').removeAttr("disabled");
                $('#deliveryman').val("").trigger( "change" );
                $('#deliveryman').attr("disabled","true");
            }
            else if($('#type').val() == 'deliveryman')
            {
                $('#deliveryman').removeAttr("disabled");
                $('#store').val("").trigger( "change" );
                $('#store').attr("disabled","true");
            }
        });
    });
    $('#store').select2({
        ajax: {
            url: '{{url('/')}}/admin/store/get-stores',
            data: function (params) {
                return {
                    q: params.term, // search term
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

    $('#deliveryman').select2({
        ajax: {
            url: '{{url('/')}}/admin/delivery-man/get-deliverymen',
            data: function (params) {
                return {
                    q: params.term, // search term
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
</script>
<script>
    $('#add_transaction').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.post({
            url: '{{route('admin.account-transaction.update')}}',
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
                    toastr.success('{{translate('messages.transaction_updated')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                    setTimeout(function () {
                        location.href = '{{route('admin.account-transaction.index')}}';
                    }, 2000);
                }
            }
        });
    });
</script>
@endpush
