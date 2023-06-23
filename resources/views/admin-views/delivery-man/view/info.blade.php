@extends('layouts.admin.app')

@section('title',translate('Delivery Man Preview'))

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
                <span>{{translate('messages.deliveryman_preview')}}<span class="badge badge-soft-dark ml-2" id="itemCount">{{$reviews->total()}}</span></span>
            </h1>
            <div class="row">
                @if($dm->application_status == 'approved')
                <div class="col-md-12">
                    <div class="js-nav-scroller hs-nav-scroller-horizontal mt-2">
                        <!-- Nav -->
                        <ul class="nav nav-tabs mb-3 border-0 nav--tabs">
                            <li class="nav-item">
                                <a class="nav-link active" href="{{route('admin.users.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'info'])}}"  aria-disabled="true">{{translate('messages.info')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('admin.users.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'transaction'])}}"  aria-disabled="true">{{translate('messages.transaction')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('admin.users.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'conversation'])}}"  aria-disabled="true">{{translate('messages.conversations')}}</a>
                            </li>
                        </ul>
                        <!-- End Nav -->
                    </div>
                </div>
                @else
                <div class="col-md-12">
                    <div class="btn--container justify-content-end">
                        <a class="btn btn--primary text-capitalize font-weight-bold"
                        onclick="request_alert('{{route('admin.users.delivery-man.application',[$dm['id'],'approved'])}}','{{translate('messages.you_want_to_approve_this_application')}}')"
                            href="javascript:"><i class="tio-checkmark-circle-outlined font-weight-bold pr-1"></i> {{translate('messages.approve')}}</a>
                        @if($dm->application_status !='denied')
                        <a class="btn btn--danger text-capitalize font-weight-bold"
                        onclick="request_alert('{{route('admin.users.delivery-man.application',[$dm['id'],'denied'])}}','{{translate('messages.you_want_to_deny_this_application')}}')"
                            href="javascript:"><i class="tio-clear-circle-outlined font-weight-bold pr-1"></i> {{translate('messages.deny')}}</a>
                        @endif
                    </div>
                </div>

                @endif
            </div>
        </div>
        <!-- End Page Header -->

        <div class="row mb-3 row-3">
            <!-- Earnings (Monthly) Card Example -->
            <div class="col-sm-6 col-md-4">
                <div class="resturant-card card--bg-1">
                    <h2 class="title">
                        {{$dm->total_delivered_orders()->count()}}
                    </h2>
                    <h5 class="subtitle">
                        {{translate('messages.total_delivered_orders')}}
                    </h5>
                    <img class="resturant-icon w--30" src="{{asset('public/assets/admin/img/tick.png')}}" alt="img">
                </div>
            </div>

            <!-- Collected Cash Card Example -->
            <div class="col-sm-6 col-md-4">
                <div class="resturant-card card--bg-2">
                    <h2 class="title">
                        {{\App\CentralLogics\Helpers::format_currency($dm->wallet?$dm->wallet->collected_cash:0.0)}}
                    </h2>
                    <h5 class="subtitle">
                        {{translate('messages.cash_in_hand')}}
                    </h5>
                    <img class="resturant-icon w--30" src="{{asset('public/assets/admin/img/withdraw-amount.png')}}" alt="img">
                </div>
            </div>

            <!-- Total Earning Card Example -->
            <div class="col-sm-6 col-md-4">
                <div class="resturant-card card--bg-3">
                    <h2 class="title">
                        {{\App\CentralLogics\Helpers::format_currency($dm->wallet?$dm->wallet->total_earning:0.00)}}
                    </h2>
                    <h5 class="subtitle">
                        {{translate('messages.total_earning')}}
                    </h5>
                    <img class="resturant-icon w--30" src="{{asset('public/assets/admin/img/pending.png')}}" alt="img">
                </div>
            </div>

        </div>

        <!-- Card -->
        <div class="card mb-3">
            <div class="card-header py-2">
                <div class="search--button-wrapper">
                    <h4 class="card-title">
                        {{$dm['f_name'].' '.$dm['l_name']}}

                        (@if($dm->zone)
                            {{$dm->zone->name}}
                        @else {{translate('messages.zone').' '.translate('messages.deleted')}}
                        @endif )
                        @if($dm->application_status=='approved')
                            @if($dm['status'])
                                @if($dm['active'])
                                    <label class="badge badge-soft-primary">{{translate('messages.online')}}</label>
                                @else
                                    <label class="badge badge-soft-danger">{{translate('messages.offline')}}</label>
                                @endif
                            @else
                            <span class="badge badge-danger">{{translate('messages.suspended')}}</span>
                            @endif

                        @else
                        <label class="badge badge-soft-{{$dm->application_status=='pending'?'info':'danger'}}">{{translate('messages.'.$dm->application_status)}}</label>
                        @endif
                    </h4>
                    @if($dm->application_status=='approved')
                    <a  href="javascript:"  onclick="request_alert('{{route('admin.users.delivery-man.status',[$dm['id'],$dm->status?0:1])}}','{{$dm->status?translate('messages.you_want_to_suspend_this_deliveryman'):translate('messages.you_want_to_unsuspend_this_deliveryman')}}')" class="btn font-medium {{$dm->status?'btn--danger':'btn-success'}}">
                            {{$dm->status?translate('messages.suspend_this_delivery_man'):translate('messages.unsuspend_this_delivery_man')}}
                    </a>
                    @endif
                    <div class="hs-unfold">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                {{translate('messages.type')}} ({{$dm->earning?translate('messages.freelancer'):translate('messages.salary_based')}})
                            </button>
                            <div class="dropdown-menu text-capitalize" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item {{$dm->earning?'active':''}}"
                                onclick="request_alert('{{route('admin.users.delivery-man.earning',[$dm['id'],1])}}','{{translate('messages.want_to_enable_earnings')}}')"
                                    href="javascript:">{{translate('messages.freelancer')}}</a>
                                <a class="dropdown-item {{$dm->earning?'':'active'}}"
                                onclick="request_alert('{{route('admin.users.delivery-man.earning',[$dm['id'],0])}}','{{translate('messages.want_to_disable_earnings')}}')"
                                    href="javascript:">{{translate('messages.salary_based')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Body -->
            <div class="card-body">
                <div class="row gy-3 align-items-center">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center justify-content-center">
                            <img class="avatar avatar-xxl avatar-4by3 mr-4 img--120"
                                 onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'"
                                 src="{{asset('storage/app/public/delivery-man')}}/{{$dm['image']}}"
                                 alt="Image Description">
                            <div class="d-block">
                                <div class="rating--review">
                                    <h1 class="title">{{count($dm->rating)>0?number_format($dm->rating[0]->average, 1):0}}<span class="out-of">/5</span></h1>
                                    @if (count($dm->rating)>0)
                                    @if ($dm->rating[0]->average == 5)
                                    <div class="rating">
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star"></i></span>
                                    </div>
                                    @elseif ($dm->rating[0]->average < 5 && $dm->rating[0]->average > 4.5)
                                    <div class="rating">
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star-half"></i></span>
                                    </div>
                                    @elseif ($dm->rating[0]->average < 4.5 && $dm->rating[0]->average > 4)
                                    <div class="rating">
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                    </div>
                                    @elseif ($dm->rating[0]->average < 4 && $dm->rating[0]->average > 3)
                                    <div class="rating">
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                    </div>
                                    @elseif ($dm->rating[0]->average < 3 && $dm->rating[0]->average > 2)
                                    <div class="rating">
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                    </div>
                                    @elseif ($dm->rating[0]->average < 2 && $dm->rating[0]->average > 1)
                                    <div class="rating">
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                    </div>
                                    @elseif ($dm->rating[0]->average < 1 && $dm->rating[0]->average > 0)
                                    <div class="rating">
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                    </div>
                                    @elseif ($dm->rating[0]->average == 1)
                                    <div class="rating">
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                    </div>
                                    @elseif ($dm->rating[0]->average == 0)
                                    <div class="rating">
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                    </div>
                                    @endif
                                    @endif
                                    <div class="info">
                                        {{-- <span class="mr-3">{{$dm->rating->count()}} {{translate('messages.rating')}}</span> --}}
                                        <span>{{$dm->reviews->count()}} {{translate('messages.reviews')}}</span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <ul class="list-unstyled list-unstyled-py-2 mb-0 rating--review-right py-3">

                        @php($total=$dm->reviews->count())
                        <!-- Review Ratings -->
                            <li class="d-flex align-items-center font-size-sm">
                                @php($five=\App\CentralLogics\Helpers::dm_rating_count($dm['id'],5))
                                <span
                                    class="progress-name mr-3">{{translate('excellent')}}</span>
                                <div class="progress flex-grow-1">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{$total==0?0:($five/$total)*100}}%;"
                                         aria-valuenow="{{$total==0?0:($five/$total)*100}}"
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="ml-3">{{$five}}</span>
                            </li>
                            <!-- End Review Ratings -->

                            <!-- Review Ratings -->
                            <li class="d-flex align-items-center font-size-sm">
                                @php($four=\App\CentralLogics\Helpers::dm_rating_count($dm['id'],4))
                                <span class="progress-name mr-3">{{translate('good')}}</span>
                                <div class="progress flex-grow-1">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{$total==0?0:($four/$total)*100}}%;"
                                         aria-valuenow="{{$total==0?0:($four/$total)*100}}"
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="ml-3">{{$four}}</span>
                            </li>
                            <!-- End Review Ratings -->

                            <!-- Review Ratings -->
                            <li class="d-flex align-items-center font-size-sm">
                                @php($three=\App\CentralLogics\Helpers::dm_rating_count($dm['id'],3))
                                <span class="progress-name mr-3">{{translate('average')}}</span>
                                <div class="progress flex-grow-1">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{$total==0?0:($three/$total)*100}}%;"
                                         aria-valuenow="{{$total==0?0:($three/$total)*100}}"
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="ml-3">{{$three}}</span>
                            </li>
                            <!-- End Review Ratings -->

                            <!-- Review Ratings -->
                            <li class="d-flex align-items-center font-size-sm">
                                @php($two=\App\CentralLogics\Helpers::dm_rating_count($dm['id'],2))
                                <span class="progress-name mr-3">{{translate('below_average')}}</span>
                                <div class="progress flex-grow-1">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{$total==0?0:($two/$total)*100}}%;"
                                         aria-valuenow="{{$total==0?0:($two/$total)*100}}"
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="ml-3">{{$two}}</span>
                            </li>
                            <!-- End Review Ratings -->

                            <!-- Review Ratings -->
                            <li class="d-flex align-items-center font-size-sm">
                                @php($one=\App\CentralLogics\Helpers::dm_rating_count($dm['id'],1))
                                <span class="progress-name mr-3">{{translate('poor')}}</span>
                                <div class="progress flex-grow-1">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{$total==0?0:($one/$total)*100}}%;"
                                         aria-valuenow="{{$total==0?0:($one/$total)*100}}"
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="ml-3">{{$one}}</span>
                            </li>
                            <!-- End Review Ratings -->
                        </ul>
                    </div>
                </div>
            </div>
            <!-- End Body -->
        </div>
        <!-- End Card -->

        <!-- Card -->
        <div class="card">
            <!-- Table -->
            <div class="card-body p-0">
                <div class="table-responsive datatable-custom">
                    <table id="datatable" class="table table-borderless table-thead-bordered table-nowrap card-table"
                        data-hs-datatables-options='{
                        "columnDefs": [{
                            "targets": [0, 3, 6],
                            "orderable": false
                        }],
                        "order": [],
                        "info": {
                        "totalQty": "#datatableWithPaginationInfoTotalQty"
                        },
                        "search": "#datatableSearch",
                        "entries": "#datatableEntries",
                        "pageLength": 25,
                        "isResponsive": false,
                        "isShowPaging": false,
                        "pagination": "datatablePagination"
                    }'>
                        <thead class="thead-light">
                        <tr>
                            <th class="border-0">{{translate('messages.reviewer')}}</th>
                            <th class="border-0">{{translate('messages.order_id')}}</th>
                            <th class="border-0">{{translate('messages.reviews')}}</th>
                            <th class="border-0">{{translate('messages.date')}}</th>
                        </tr>
                        </thead>

                        <tbody>

                        @foreach($reviews as $review)
                            <tr>
                                <td>
                                    @if ($review->customer)
                                        <a class="d-flex align-items-center"
                                        href="{{route('admin.customer.view',[$review['user_id']])}}">
                                            <div class="avatar avatar-circle">
                                                <img class="avatar-img" width="75" height="75"
                                                    {{-- onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'" --}}
                                                    src="{{asset('storage/app/public/profile/')}}/{{$review->customer?$review->customer->image:''}}"
                                                    alt="Image Description">
                                            </div>
                                            <div class="ml-3">
                                                <span class="d-block h5 text-hover-primary mb-0">{{$review->customer?$review->customer['f_name']." ".$review->customer['l_name']:''}} <i
                                                    class="tio-verified text-primary" data-toggle="tooltip" data-placement="top"
                                                    title="Verified Customer"></i></span>
                                                <span class="d-block font-size-sm text-body">{{$review->customer?$review->customer->email:''}}</span>
                                            </div>
                                        </a>
                                    @else
                                        {{translate('messages.customer_not_found')}}
                                    @endif
                                </td>
                                <td>
                                    <a href="{{route('admin.order.all-details',['id'=>$review->order_id])}}">{{$review->order_id}}</a>
                                </td>
                                <td>
                                    <div class="text-wrap w-18rem">
                                        <div class="d-flex">
                                            <label class="badge badge-soft-info">
                                                {{$review->rating}} <i class="tio-star"></i>
                                            </label>
                                        </div>

                                        <p>
                                            {{$review['comment']}}
                                        </p>
                                    </div>
                                </td>
                                <td>
                                    {{date('d M Y '.config('timeformat'),strtotime($review['created_at']))}}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- End Table -->
                @if(count($reviews) !== 0)
                <hr>
                @endif
                <div class="page-area">
                    {!! $reviews->links() !!}
                </div>
                @if(count($reviews) === 0)
                <div class="empty--data">
                    <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                    <h5>
                        {{translate('no_data_found')}}
                    </h5>
                </div>
                @endif
            </div>
        </div>
        <!-- End Card -->
    </div>
@endsection

@push('script_2')
<script>
    function request_alert(url, message) {
        Swal.fire({
            title: '{{translate('messages.are_you_sure')}}',
            text: message,
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#FC6A57',
            cancelButtonText: '{{translate('messages.no')}}',
            confirmButtonText: '{{translate('messages.yes')}}',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                location.href = url;
            }
        })
    }
</script>
@endpush
