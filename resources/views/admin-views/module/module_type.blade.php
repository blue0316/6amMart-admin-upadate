@extends('layouts.admin.app')

@section('title',translate('messages.modules'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">{{translate('messages.module')}} {{translate('messages.type')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <div class="card">
            <div class="card-header"><h5>{{translate('messages.add').' '.translate('messages.new')}} {{translate('messages.module')}}</h5></div>
            <div class="card-body">
                <form action="{{route('admin.module.create')}}" method="get" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.type')}}</label>
                        <input type="text" name="module_type" class="form-control" placeholder="{{translate('messages.new_category')}}" value="{{old('name')}}" required maxlength="191">
                    </div>

                    <div class="form-group">
                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.description')}}</label>
                        <textarea class="ckeditor form-control" name="module_description"></textarea>
                    </div>

                    <div class="form-group pt-2">
                        <button type="submit" class="btn btn-primary">{{translate('messages.add')}}</button>
                    </div>
                    
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header pb-0">
                <h5>{{translate('messages.module_type')}} {{translate('messages.list')}}</h5>
                {{--<form id="dataSearch">
                    @csrf
                    <!-- Search -->
                    <div class="input-group input-group-merge input-group-flush">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="tio-search"></i>
                            </div>
                        </div>
                        <input type="search" name="search" class="form-control" placeholder="{{translate('messages.search_categories')}}" aria-label="{{translate('messages.search_categories')}}">
                        <button type="submit" class="btn btn-light">{{translate('messages.search')}}</button>
                    </div>
                    <!-- End Search -->
                </form>--}}
            </div>
            <div class="card-body">
                <div class="table-responsive datatable-custom">
                    <table id="columnSearchDatatable"
                        class="table table-borderless table-thead-bordered table-align-middle" data-hs-datatables-options='{
                            "isResponsive": false,
                            "isShowPaging": false,
                            "paging":false,
                        }'>
                        <thead class="thead-light">
                            <tr>
                                <th>{{translate('messages.module_type')}}</th>
                                <th>{{translate('messages.description')}}</th>
                            </tr>
                        </thead>

                        <tbody id="table-div">
                        @foreach($module_type as $key=>$module)
                            <tr>
                                <td>
                                    <span class="d-block font-size-sm text-body">
                                        {{Str::limit($module['module_type'], 20,'...')}}
                                    </span>
                                </td>
                                <td>
                                    {!! $module->description !!}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('script_2')
    <script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.ckeditor').ckeditor();
        });
    </script>
@endpush
