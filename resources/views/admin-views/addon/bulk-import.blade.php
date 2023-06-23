@extends('layouts.admin.app')

@section('title',translate('AddOn Bulk Import'))

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
                    {{translate('messages.addons')}} {{translate('messages.bulk_import')}}
                </span>
            </h1>
        </div>
        <!-- Content Row -->
        <div class="card">
            <div class="card-body">
                <div class="export-steps style-2">
                    <div class="export-steps-item">
                        <div class="inner">
                            <h5>{{translate('STEP 1')}}</h5>
                            <p>
                                {{translate('Download Excel File')}}
                            </p>
                        </div>
                    </div>
                    <div class="export-steps-item">
                        <div class="inner">
                            <h5>{{translate('STEP 2')}}</h5>
                            <p>
                                {{translate('Match Spread sheet data according to instruction')}}
                            </p>
                        </div>
                    </div>
                    <div class="export-steps-item">
                        <div class="inner">
                            <h5>{{translate('STEP 3')}}</h5>
                            <p>
                                {{translate('Validate data and complete import')}}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="jumbotron pt-1 mb-0 pb-4 bg-white">
                    <h3>{{ translate('messages.Instructions') }} : </h3>
                    <p> {{ translate('1. Download the format file and fill it with proper data.') }}</p>

                    <p>{{ translate('2. You can download the example file to understand how the data must be filled.') }}</p>

                    <p>{{ translate('3. Once you have downloaded and filled the format file, upload it in the form below and
                        submit.') }}</p>

                </div>
                <div class="text-center pb-4">
                    <h3 class="mb-3 export--template-title">{{ translate('messages.Download Spreadsheet Template') }}</h3>
                    <div class="btn--container justify-content-center export--template-btns">
                        <a href="{{asset('public/assets/addons_bulk_format.xlsx')}}" download="" class="btn btn-dark">{{ translate('messages.Template with Existing Data') }}</a>
                        <a href="{{asset('public/assets/addons_bulk_format_nodata.xlsx')}}" download="" class="btn btn-dark">{{ translate('messages.Template without Data') }}</a>
                    </div>
                </div>
            </div>
        </div>
        <form class="product-form" action="{{route('admin.addon.bulk-import')}}" method="POST"
        enctype="multipart/form-data">
        @csrf
        <div class="card mt-3">
            <div class="card-body">
                <div class="mt-2 rest-part">
                    <h4 class="mb-3">{{translate('messages.import_categories_file')}}</h4>
                    <div class="custom-file custom--file">
                        <input type="file" name="products_file" class="form-control" id="products_file">
                        <label class="custom-file-label" for="products_file">{{ translate('messages.Choose File') }}</label>
                    </div>
                    <div class="btn--container justify-content-end mt-3">
                        <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                        <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('script')

@endpush
