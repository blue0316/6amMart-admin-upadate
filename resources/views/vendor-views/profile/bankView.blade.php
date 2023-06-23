@extends('layouts.vendor.app')
@section('title',translate('messages.Bank Info View'))
@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{asset('public/assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('/public/assets/admin/img/bank.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.my_bank_info')}}
                </span>
            </h1>
        </div>
        <!-- Page Heading -->
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header flex-wrap justify-content-end p-2 px-xl-4">
                        @if($data->holder_name)
                            <button class="btn btn--primary m-1 m-sm-2" type="button" data-toggle="modal" data-target="#update-modal"><i class="tio-edit"></i> {{__('messages.update')}}</button>
                            <a class="btn btn--danger m-1 m-sm-2" href="javascript:void(0)" onclick="form_alert('del','Delete Bank Info ?')"><i class="tio-delete-outlined"></i> {{__('messages.delete')}}</a>
                            <form action="javascript:" id="del" method="post">
                                @csrf @method('post')
                            </form>
                        @else
                            <button class="btn btn--primary m-1 m-sm-2" type="button" data-toggle="modal" data-target="#update-modal"><i class="tio-add"></i>{{ translate('Add Bank Info') }}</button>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="bank--card">
                            <div class="bank--card__header border-bottom">
                                <div class="title">{{translate('messages.holder_name')}} : {{$data->holder_name ? $data->holder_name : translate('messages.No Data found')}}</div>
                            </div>
                            <div class="bank--card__body">
                                <img class="bank__icon" src="{{asset('public/assets/admin/img/bank-icon.png')}}" alt="public">
                                <ul>
                                    <li>
                                        <h5>
                                            {{translate('messages.bank_name')}} : 
                                        </h5>
                                        <div class="info">
                                            {{$data->bank_name ? $data->bank_name : translate('messages.No Data found')}}
                                        </div>
                                    </li>
                                    <li>
                                        <h5>
                                            {{translate('messages.branch')}} : 
                                        </h5>
                                        <div class="info">
                                            {{$data->branch ? $data->branch : translate('messages.No Data found')}}
                                        </div>
                                    </li>
                                    <li>
                                        <h5>
                                            {{translate('messages.account_no')}} : 
                                        </h5>
                                        <div class="info">
                                            {{$data->account_no ? $data->account_no : translate('messages.No Data found')}}
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="update-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="h3 mb-0">{{ translate('messages.edit_bank_info') }}</h1>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true" class="btn btn--circle btn-soft-danger text-danger"><ti class="tio-clear"></ti></span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('vendor.profile.bank_update')}}" method="post"
                            enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="name">{{translate('messages.bank_name')}} <span class="text-danger">*</span></label>
                                    <input type="text" name="bank_name" value="{{$data->bank_name}}"
                                            class="form-control" id="name"
                                            required maxlength="191">
                                </div>
                                <div class="col-md-6">
                                    <label for="name">{{translate('messages.branch')}} {{translate('messages.name')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="branch" value="{{$data->branch}}" class="form-control"
                                            id="name"
                                            required maxlength="191">
                                </div>
                            </div>

                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="account_no">{{translate('messages.holder_name')}} <span class="text-danger">*</span></label>
                                    <input type="text" name="holder_name" value="{{$data->holder_name}}"
                                            class="form-control" id="account_no"
                                            required maxlength="191">
                                </div>
                                <div class="col-md-6">
                                    <label for="account_no">{{translate('messages.account_no')}} <span class="text-danger">*</span></label>
                                    <input type="text" name="account_no" value="{{$data->account_no}}"
                                            class="form-control" id="account_no"
                                            required maxlength="191">
                                </div>

                            </div>

                        </div>
                        <div class="btn--container justify-content-end">
                            <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                            <button type="submit" class="btn btn--primary" id="btn_update">{{translate('messages.update')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <!-- Page level plugins -->
@endpush
