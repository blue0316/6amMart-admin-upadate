@extends('layouts.admin.app')

@section('title',translate('messages.Delivery Man Preview'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title text-break">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/delivery-man.png')}}" class="w--26" alt="">
                </span>
                <span>{{$dm['f_name'].' '.$dm['l_name']}}</span>
            </h1>
            <div class="row">
                <div class="js-nav-scroller hs-nav-scroller-horizontal mt-2">
                    <!-- Nav -->
                    <ul class="nav nav-tabs nav--tabs border-0">
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('admin.users.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'info'])}}"  aria-disabled="true">{{translate('messages.info')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="{{route('admin.users.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'transaction'])}}"  aria-disabled="true">{{translate('messages.transaction')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('admin.users.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'conversation'])}}"  aria-disabled="true">{{translate('messages.conversations')}}</a>
                        </li>
                    </ul>
                    <!-- End Nav -->
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <!-- Card -->
        <div class="card mb-3 mb-lg-5 mt-2">
            <div class="card-header py-2 border-0">
                <div class="search--button-wrapper">
                    <h4 class="card-title">{{ translate('messages.order')}} {{ translate('messages.transactions')}}</h4>
                    <div class="min--260">
                        <input type="date" class="form-control" onchange="set_filter('{{route('admin.delivery-man.preview',['id'=>$dm->id, 'tab'=> 'transaction'])}}',this.value, 'date')" value="{{$date}}">
                    </div>
                </div>
            </div>
            <!-- Body -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="datatable"
                        class="table table-borderless table-thead-bordered table-nowrap justify-content-between table-align-middle card-table">
                        <thead class="thead-light">
                            <tr>
                                <th class="border-0">{{translate('sl')}}</th>
                                <th class="border-0">{{translate('messages.order')}} {{translate('messages.id')}}</th>
                                <th class="border-0">{{translate('messages.delivery_fee')}} {{translate('messages.earned')}}</th>
                                <th class="border-0">{{translate('messages.delivery_tips')}}</th>
                                <th class="border-0">{{translate('messages.date')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php($digital_transaction = \App\Models\OrderTransaction::where('delivery_man_id', $dm->id)
                        ->when($date, function($query)use($date){
                            return $query->whereDate('created_at', $date);
                        })->paginate(25))
                        @foreach($digital_transaction as $k=>$dt)
                            <tr>
                                <td scope="row">{{$k+$digital_transaction->firstItem()}}</td>
                                <td><a href="{{route((isset($dt->order) && $dt->order->order_type=='parcel')?'admin.parcel.order.details':'admin.order.details',[$dt->order_id,'module_id'=>$dt->order->module_id])}}">{{$dt->order_id}}</a></td>
                                <td>{{$dt->original_delivery_charge}}</td>
                                <td>{{$dt->dm_tips}}</td>
                                <td>{{$dt->created_at->format('Y-m-d')}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- End Body -->
            <div class="card-footer">
                {!!$digital_transaction->links()!!}
            </div>
        </div>
        <!-- End Card -->
    </div>
@endsection

@push('script_2')
<script>
    function request_alert(url, message) {
        Swal.fire({
            title: 'Are you sure?',
            text: message,
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#FC6A57',
            cancelButtonText: 'No',
            confirmButtonText: 'Yes',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                location.href = url;
            }
        })
    }
</script>
@endpush
