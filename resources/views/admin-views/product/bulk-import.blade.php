@extends('layouts.admin.app')

@section('title',translate('Item Bulk Import'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/items.png')}}" class="w--22" alt="">
                </span>
                <span>
                    {{translate('messages.items')}} {{translate('messages.bulk_import')}}
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
                    <p>{{ translate('1. Download the format file and fill it with proper data.') }}</p>

                    <p>{{ translate('2. You can download the example file to understand how the data must be filled.') }}</p>

                    <p>{{ translate('3. Once you have downloaded and filled the format file, upload it in the form below and
                        submit.') }}</p>
                    <p>{{ translate('4. You can get store id, module id and unit id from their list, please input the right ids.') }}</p>

                    <p>{{ translate('5. For ecommerce item avaliable time start and end will be 00:00:00 and 23:59:59') }}</p>

                    <p>{{ translate('6. You can upload your product images in product folder from gallery, and copy image`s path.') }}</p>

                </div>
                <div class="text-center pb-4">
                    <h3 class="mb-3 export--template-title">{{translate('download_spreadsheet_template')}}</h3>
                    <div class="btn--container justify-content-center export--template-btns">
                        <a href="{{asset('public/assets/items_bulk_format.xlsx')}}" download="" class="btn btn-dark">{{translate('template_with_existing_data')}}</a>
                        <a href="{{asset('public/assets/items_bulk_format_nodata.xlsx')}}" download="" class="btn btn-dark">{{translate('template_without_data')}}</a>
                    </div>
                </div>
            </div>
        </div>

        <form class="product-form" action="{{route('admin.item.bulk-import')}}" method="POST"
                enctype="multipart/form-data">
            @csrf
            <div class="card mt-2 rest-part">
                <div class="card-body">
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
