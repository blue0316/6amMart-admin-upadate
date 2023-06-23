@extends('layouts.admin.app')

@section('title','Attribute Bulk Import')

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <h1 class="text-capitalize">{{translate('messages.attributes')}} {{translate('messages.bulk_import')}}</h1>
        <!-- Content Row -->
        <div class="row">
            <div class="col-12">
                <div class="jumbotron pt-1 bg-white">
                    <h3>Instructions : </h3>
                    <p> 1. Download the format file and fill it with proper data.</p>

                    <p>2. You can download the example file to understand how the data must be filled.</p>

                    <p>3. Once you have downloaded and filled the format file, upload it in the form below and
                        submit.</p>

                </div>
            </div>

            <div class="col-md-12">
                <form class="product-form" action="{{route('admin.attribute.bulk-import')}}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="card mt-2 rest-part">
                        <div class="card-header">
                            <h4>Import Attributes File</h4>
                            <a href="{{asset('public/assets/attributes_bulk_format.xlsx')}}" download=""
                               class="btn btn-secondary">Download Format</a>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="file" name="products_file">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-footer">
                        <div class="pt-3">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')

@endpush
