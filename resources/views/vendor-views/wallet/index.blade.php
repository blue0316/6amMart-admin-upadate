@extends('layouts.vendor.app')

@section('title',translate('messages.store').' '.translate('messages.wallet'))

@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Heading -->
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('public/assets/admin/img/wallet.png')}}" class="w--26" alt="">
            </span>
            <span>
                {{translate('messages.store_wallet')}}
            </span>
        </h1>
    </div>
    <!-- Page Heading -->
        <div class="row g-2">
        <?php
            $wallet = \App\Models\StoreWallet::where('vendor_id',\App\CentralLogics\Helpers::get_vendor_id())->first();
            if(isset($wallet)==false){
                \Illuminate\Support\Facades\DB::table('store_wallets')->insert([
                    'vendor_id'=>\App\CentralLogics\Helpers::get_vendor_id(),
                    'created_at'=>now(),
                    'updated_at'=>now()
                ]);
                $wallet = \App\Models\StoreWallet::where('vendor_id',\App\CentralLogics\Helpers::get_vendor_id())->first();
            }
        ?>

            <div class="col-md-4">
                <div class="card h-100 card--bg-1">
                    <div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
                        <h5 class="cash--subtitle text-white">
                            {{translate('messages.withdraw_able_balance')}}
                        </h5>
                        <div class="d-flex align-items-center justify-content-center mt-3">
                            <div class="cash-icon mr-3">
                            <img src="{{asset('public/assets/admin/img/cash.png')}}" alt="img">
                        </div>
                            <h2 class="cash--title text-white">{{\App\CentralLogics\Helpers::format_currency($wallet->balance)}}</h2>
                        </div>
                    </div>
                    <div class="card-footer pt-0 bg-transparent border-0">
                        @if(\App\CentralLogics\Helpers::get_vendor_data()->account_no==null || \App\CentralLogics\Helpers::get_vendor_data()->bank_name==null)
                        <a tabindex="0" class="btn text--title text-capitalize bg-white h--45px w-100" role="button" data-toggle="popover" data-trigger="focus" title="{{translate('messages.warning_missing_bank_info')}}" data-content="{{translate('messages.warning_add_bank_info')}}">{{translate('messages.request')}} {{translate('messages.withdraw')}}</a>
                        @else
                        <a class="btn text--title text-capitalize bg-white h--45px w-100" href="javascript:" data-toggle="modal" data-target="#balance-modal">{{translate('messages.request')}} {{translate('messages.withdraw')}}</a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="row g-2">
                    <!-- Panding Withdraw Card Example -->
                    <div class="col-sm-6">
                        <div class="resturant-card card--bg-2" >
                            <h4 class="title">{{\App\CentralLogics\Helpers::format_currency($wallet->pending_withdraw)}}</h4>
                            <div class="subtitle">{{translate('messages.pending')}} {{translate('messages.withdraw')}}</div>
                            <img class="resturant-icon w--30" src="{{asset('/public/assets/admin/img/transactions/pending.png')}}" alt="transaction">
                        </div>
                    </div>

                    <!-- Earnings (Monthly) Card Example -->
                    <div class="col-sm-6">
                        <div class="resturant-card card--bg-3">
                            <h4 class="title">{{\App\CentralLogics\Helpers::format_currency($wallet->total_withdrawn)}}</h4>
                            <div class="subtitle">{{translate('messages.withdrawn')}}</div>
                            <img class="resturant-icon w--30" src="{{asset('/public/assets/admin/img/transactions/withdraw-amount.png')}}" alt="transaction">
                        </div>
                    </div>

                    <!-- Collected Cash Card Example -->
                    <div class="col-sm-6">
                        <div class="resturant-card card--bg-4">
                            <h4 class="title">{{\App\CentralLogics\Helpers::format_currency($wallet->collected_cash)}}</h4>
                            <div class="subtitle">{{translate('messages.collected_cash')}}</div>
                            <img class="resturant-icon w--30" src="{{asset('/public/assets/admin/img/transactions/withdraw-amount.png')}}" alt="transaction">
                        </div>
                    </div>

                    <!-- Pending Requests Card Example -->
                    <div class="col-sm-6">
                        <div class="resturant-card card--bg-1">
                            <h4 class="title">{{\App\CentralLogics\Helpers::format_currency($wallet->total_earning)}}</h4>
                            <div class="subtitle">{{translate('messages.total_earning')}}</div>
                            <img class="resturant-icon w--30" src="{{asset('/public/assets/admin/img/transactions/earning.png')}}" alt="transaction">
                        </div>
                    </div>
                </div>

            </div>
        </div>

    <div class="modal fade" id="balance-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header border-bottom pb-3">
                    <h5 class="modal-title" id="exampleModalLabel">{{translate('messages.withdraw')}} {{translate('messages.request')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('vendor.wallet.withdraw-request')}}" method="post">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">{{translate('messages.amount')}}:</label>
                            <input type="number" name="amount" step="0.01"
                                    value="{{$wallet->balance}}"
                                    class="form-control" id="" min="0" max="{{$wallet->balance}}">
                        </div>
                    </div>
                    <div class="modal-footer justify-content-end">
                        <button type="button" class="btn btn--danger" data-dismiss="modal">{{translate('messages.Close')}}</button>
                        <button type="submit" class="btn btn--primary">{{translate('messages.Submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="card mt-3">
        <div class="card-header border-0">
            <h5 class="card-title">{{ translate('messages.withdraw')}} {{ translate('messages.request')}} {{ translate('messages.table')}}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="datatable"
                        class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table" data-hs-datatables-options='{
                            "order": [],
                            "orderCellsTop": true,
                            "paging":false
                        }'>
                    <thead class="thead-light">
                    <tr>
                        <th class="border-0">{{translate('messages.sl')}}</th>
                        <th class="border-0">{{translate('messages.amount')}}</th>
                        {{--<th class="border-0">{{translate('messages.note')}}</th>--}}
                        <th class="border-0">{{translate('messages.request_time')}}</th>
                        <th class="border-0">{{translate('messages.status')}}</th>
                        <th class="border-0 w-100px">Close</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($withdraw_req as $k=>$wr)
                        <tr>
                            <td scope="row">{{$k+$withdraw_req->firstItem()}}</td>
                            <td>{{$wr['amount']}}</td>
                            {{--<td>{{$wr->transaction_note}}</td>--}}
                            <td>{{date('Y-m-d '.config('timeformat'),strtotime($wr->created_at))}}</td>
                            <td>
                                @if($wr->approved==0)
                                    <label class="badge badge-primary">{{translate('messages.pending')}}</label>
                                @elseif($wr->approved==1)
                                    <label class="badge badge-success">{{translate('messages.approved')}}</label>
                                @else
                                    <label class="badge badge-danger">{{translate('messages.denied')}}</label>
                                @endif
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    @if($wr->approved==0)
                                        {{-- <a href="{{route('vendor.withdraw.close',[$wr['id']])}}"
                                            class="btn action-btn btn--danger btn-outline-danger">
                                            {{translate('messages.Delete')}}
                                        </a> --}}
                                        <a class="btn action-btn btn--danger btn-outline-danger" href="javascript:" onclick="form_alert('withdraw-{{$wr['id']}}','{{ translate('Want to delete this') }} ?')" title="{{translate('messages.delete')}}"><i class="tio-delete-outlined"></i>
                                    </a>
                                @else
                                    <span class="badge badge-success">{{translate('messages.complete')}}</span>
                                @endif
                                </div>
                                <form action="{{route('vendor.wallet.close-request',[$wr['id']])}}"
                                        method="post" id="withdraw-{{$wr['id']}}">
                                    @csrf @method('delete')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if(count($withdraw_req) !== 0)
        <div class="card-footer">
            {{$withdraw_req->links()}}
        </div>
        @endif
        @if(count($withdraw_req) === 0)
        <div class="empty--data">
            <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
            <h5>
                {{translate('no_data_found')}}
            </h5>
        </div>
        @endif
    </div>
</div>
@endsection
