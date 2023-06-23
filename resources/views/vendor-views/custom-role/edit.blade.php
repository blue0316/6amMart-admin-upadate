@extends('layouts.vendor.app')
@section('title','Edit Role')
@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">

    <!-- Page Heading -->
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('public/assets/admin/img/edit.png')}}" class="w--26" alt="">
            </span>
            <span>
                {{translate('messages.edit_role')}}
            </span>
        </h1>
    </div>
    <!-- Page Heading -->

    <!-- Content Row -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <span class="card-header-icon">
                    <i class="tio-document-text-outlined"></i>
                </span>
                <span>{{translate('messages.role_form')}}</span>
            </h5>
        </div>
        <div class="card-body">
            <form action="{{route('vendor.custom-role.update',[$role['id']])}}" method="post">
                @csrf
                <div class="form-group">
                    <label for="name">{{translate('messages.role_name')}}</label>
                    <input type="text" name="name" value="{{$role['name']}}" class="form-control" id="name" aria-describedby="emailHelp"
                            placeholder="{{translate('role_name_example')}}">
                </div>

                <h5>{{translate('messages.module_permission')}} : </h5>
                <hr>
                <div class="check--item-wrapper mx-0">
                    <div class="check-item">
                        <div class="form-group form-check form--check">
                            <input type="checkbox" name="modules[]" value="item" class="form-check-input"
                                    id="item" {{in_array('item',(array)json_decode($role['modules']))?'checked':''}}>
                            <label class="form-check-label qcont" for="item">{{translate('messages.item')}}</label>
                        </div>
                    </div>
                    <div class="check-item">
                        <div class="form-group form-check form--check">
                            <input type="checkbox" name="modules[]" value="order" class="form-check-input"
                                    id="order" {{in_array('order',(array)json_decode($role['modules']))?'checked':''}}>
                            <label class="form-check-label qcont" for="order">{{translate('messages.order')}}</label>
                        </div>
                    </div>
                    <div class="check-item">
                        <div class="form-group form-check form--check">
                            <input type="checkbox" name="modules[]" value="store_setup" class="form-check-input"
                                    id="store_setup" {{in_array('store_setup',(array)json_decode($role['modules']))?'checked':''}}>
                            <label class="form-check-label qcont" for="store_setup">{{translate('messages.business')}} {{translate('messages.setup')}}</label>
                        </div>
                    </div>
                    @if (config('module.'.\App\CentralLogics\Helpers::get_store_data()->module->module_type)['add_on'])
                    <div class="check-item">
                        <div class="form-group form-check form--check">
                            <input type="checkbox" name="modules[]" value="addon" class="form-check-input"
                                    id="addon" {{in_array('addon',(array)json_decode($role['modules']))?'checked':''}}>
                            <label class="form-check-label qcont" for="addon">{{translate('messages.addon')}}</label>
                        </div>
                    </div>
                    @endif
                    <div class="check-item">
                        <div class="form-group form-check form--check">
                            <input type="checkbox" name="modules[]" value="wallet" class="form-check-input"
                                    id="wallet" {{in_array('wallet',(array)json_decode($role['modules']))?'checked':''}}>
                            <label class="form-check-label qcont" for="wallet">{{translate('messages.wallet')}}</label>
                        </div>
                    </div>
                    <div class="check-item">
                        <div class="form-group form-check form--check">
                            <input type="checkbox" name="modules[]" value="bank_info" class="form-check-input"
                                    id="bank_info" {{in_array('bank_info',(array)json_decode($role['modules']))?'checked':''}}>
                            <label class="form-check-label qcont" for="bank_info">{{translate('messages.bank_info')}}</label>
                        </div>
                    </div>
                    <div class="check-item">
                        <div class="form-group form-check form--check">
                            <input type="checkbox" name="modules[]" value="employee" class="form-check-input"
                                    id="employee" {{in_array('employee',(array)json_decode($role['modules']))?'checked':''}}>
                            <label class="form-check-label qcont" for="employee">{{translate('messages.Employee')}}</label>
                        </div>
                    </div>
                    <div class="check-item">
                        <div class="form-group form-check form--check">
                            <input type="checkbox" name="modules[]" value="my_shop" class="form-check-input"
                                    id="my_shop" {{in_array('my_shop',(array)json_decode($role['modules']))?'checked':''}}>
                            <label class="form-check-label qcont" for="my_shop">{{translate('messages.my_shop')}}</label>
                        </div>
                    </div>
                    <div class="check-item">
                        <div class="form-group form-check form--check">
                            <input type="checkbox" name="modules[]" value="custom_role" class="form-check-input"
                                    id="custom_role" {{in_array('custom_role',(array)json_decode($role['modules']))?'checked':''}}>
                            <label class="form-check-label qcont" for="custom_role">{{translate('messages.custom_role')}}</label>
                        </div>
                    </div>
                    
                    <div class="check-item">
                        <div class="form-group form-check form--check">
                            <input type="checkbox" name="modules[]" value="campaign" class="form-check-input"
                                    id="campaign" {{in_array('campaign',(array)json_decode($role['modules']))?'checked':''}}>
                            <label class="form-check-label qcont" for="campaign">{{translate('messages.campaign')}}</label>
                        </div>
                    </div>

                    <div class="check-item">
                        <div class="form-group form-check form--check">
                            <input type="checkbox" name="modules[]" value="reviews" class="form-check-input"
                                    id="reviews" {{in_array('reviews',(array)json_decode($role['modules']))?'checked':''}}>
                            <label class="form-check-label qcont" for="reviews">{{translate('messages.reviews')}}</label>
                        </div>
                    </div>

                    <div class="check-item">
                        <div class="form-group form-check form--check">
                            <input type="checkbox" name="modules[]" value="pos" class="form-check-input"
                                    id="pos" {{in_array('pos',(array)json_decode($role['modules']))?'checked':''}}>
                            <label class="form-check-label qcont" for="pos">{{translate('messages.pos')}}</label>
                        </div>
                    </div>
                    <div class="check-item">
                        <div class="form-group form-check form--check">
                            <input type="checkbox" name="modules[]" value="chat" class="form-check-input"
                                    id="chat" {{in_array('chat',(array)json_decode($role['modules']))?'checked':''}}>
                            <label class="form-check-label qcont" for="chat">{{translate('messages.chat')}}</label>
                        </div>
                    </div>
                </div>
                <div class="btn--container justify-content-end mt-4">
                    <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                    <button type="submit" class="btn btn--primary">{{translate('messages.update')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')

@endpush
