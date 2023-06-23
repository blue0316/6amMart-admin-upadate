@extends('layouts.admin.app')

@section('title',translate('Addon Bulk Export'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/addon.png')}}" class="w--20" alt="">
                </span>
                <span>
                    {{translate('messages.addons')}} {{translate('messages.bulk_export')}}
                </span>
            </h1>
        </div>
        <div class="card mt-2 rest-part">
            <div class="card-body">
                <div class="export-steps">
                    <div class="export-steps-item">
                        <div class="inner">
                            <h5>{{translate('STEP 1')}}</h5>
                            <p>
                                {{translate('Select Data Type')}}
                            </p>
                        </div>
                    </div>
                    <div class="export-steps-item">
                        <div class="inner">
                            <h5>{{translate('STEP 2')}}</h5>
                            <p>
                                {{translate('Select Data Range by Date or ID and Export')}}
                            </p>
                        </div>
                    </div>
                </div>
                <form class="product-form" action="{{route('admin.addon.bulk-export')}}" method="POST"
                        enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.type')}}<span
                                        class="input-label-secondary"></span></label>
                                <select name="type" id="type" data-placeholder="{{translate('messages.select')}} {{translate('messages.type')}}" class="form-control" required title="Select Type">
                                    <option value="all">{{translate('messages.all')}} {{translate('messages.data')}}</option>
                                    <option value="date_wise">{{translate('messages.date')}} {{translate('messages.wise')}}</option>
                                    <option value="id_wise">{{translate('messages.id')}} {{translate('messages.wise')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group id_wise">
                                <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.start')}} {{translate('messages.id')}}<span
                                        class="input-label-secondary"></span></label>
                                <input type="number" name="start_id" class="form-control">
                            </div>
                            <div class="form-group date_wise">
                                <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.from')}} {{translate('messages.date')}}<span
                                        class="input-label-secondary"></span></label>
                                <input type="date" name="from_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group id_wise">
                                <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.end')}} {{translate('messages.id')}}<span
                                        class="input-label-secondary"></span></label>
                                <input type="number" name="end_id" class="form-control">
                            </div>
                            <div class="form-group date_wise">
                                <label class="input-label text-capitalize" for="exampleFormControlSelect1">{{translate('messages.to')}} {{translate('messages.date')}}<span
                                        class="input-label-secondary"></span></label>
                                <input type="date" name="to_date" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="btn--container justify-content-end mt-3">
                        <button type="reset" class="btn btn--reset">{{translate('clear')}}</button>
                        <button type="submit" class="btn btn--primary">{{translate('export')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
<script>
    $(document).on('ready', function (){
        $('.id_wise').hide();
        $('.date_wise').hide();
        $('#type').on('change', function()
        {
            $('.id_wise').hide();
            $('.date_wise').hide();
            $('.'+$(this).val()).show();
        })
    });
</script>
@endpush
