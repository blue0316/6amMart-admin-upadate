<div>
    <div class="table-responsive">
        <table id="datatable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
            <thead class="thead-light">
                <tr>
                    <th class="border-0">{{translate('messages.sl#')}}</th>
                    <th class="border-0">{{translate('messages.created_at')}}</th>
                    <th class="border-0">{{translate('messages.amount')}}</th>
                    <th class="border-0">{{translate('messages.status')}}</th>
                    <th class="border-0">{{translate('messages.action')}}</th>
                </tr>
            </thead>
            <tbody>
            @php($withdraw_transaction = \App\Models\WithdrawRequest::where('vendor_id', $store->vendor->id)->paginate(25))
            @foreach($withdraw_transaction as $k=>$wt)
                <tr>
                    <td scope="row">{{$k+$withdraw_transaction->firstItem()}}</td>
                    <td>{{date('Y-m-d '.config('timeformat'), strtotime($wt->created_at))}}</td>
                    <td>{{\App\CentralLogics\Helpers::format_currency($wt->amount)}}</td>
                    <td>
                        @if($wt->approved==0)
                            <label class="badge badge-primary">{{ translate('messages.pending') }}</label>
                        @elseif($wt->approved==1)
                            <label class="badge badge-success">{{ translate('messages.approved') }}</label>
                        @else
                            <label class="badge badge-danger">{{ translate('messages.denied') }}</label>
                        @endif
                    </td>
                    <td>
                        <a href="{{route('admin.store.withdraw_view',[$wt['id'],$store->vendor['id']])}}"
                            class="btn btn--warning action-btn btn-outline-warning"><i class="tio-visible"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
</div>
@if(count($withdraw_transaction) !== 0)
<hr>
@endif
<div class="page-area">
    {!! $withdraw_transaction->links() !!}
</div>
@if(count($withdraw_transaction) === 0)
<div class="empty--data">
    <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
    <h5>
        {{translate('no_data_found')}}
    </h5>
</div>
@endif
