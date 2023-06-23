<div>
    <div class="table-responsive">
        <table id="datatable"
            class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
            <thead class="thead-light">
                <tr>
                    <th class="border-0">{{translate('sl')}}</th>
                    <th class="border-0">{{translate('messages.received_at')}}</th>
                    <th class="border-0">{{translate('messages.balance_before_transaction')}}</th>
                    <th class="border-0">{{translate('messages.amount')}}</th>
                    <th class="border-0">{{translate('messages.reference')}}</th>
                    <th class="border-0">{{translate('messages.action')}}</th>
                </tr>
            </thead>
            <tbody>
            @php($account_transaction = \App\Models\AccountTransaction::where('from_type', 'store')->where('from_id', $store->vendor->id)->paginate(25))
            @foreach($account_transaction as $k=>$at)
                <tr>
                    <td scope="row">{{$k+$account_transaction->firstItem()}}</td>
                    <td>{{$at->created_at->format('Y-m-d '.config('timeformat'))}}</td>
                    <td>{{\App\CentralLogics\Helpers::format_currency($at['current_balance'])}}</td>
                    <td>{{\App\CentralLogics\Helpers::format_currency($at['amount'])}}</td>
                    <td>{{$at['ref']}}</td>
                    <td>
                        <a href="{{route('admin.account-transaction.show',[$at['id']])}}"
                        class="btn btn--warning action-btn btn-outline-warning"><i class="tio-visible"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@if(count($account_transaction) !== 0)
<hr>
@endif
<div class="page-area">
    {!! $account_transaction->links() !!}
</div>
@if(count($account_transaction) === 0)
<div class="empty--data">
    <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
    <h5>
        {{translate('no_data_found')}}
    </h5>
</div>
@endif
