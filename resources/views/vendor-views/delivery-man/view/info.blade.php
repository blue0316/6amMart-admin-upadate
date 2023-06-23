@extends('layouts.vendor.app')

@section('title',translate('messages.Delivery Man Preview'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/deliveryman.png')}}" class="w--30" alt="">
                </span>
                <span>
                    {{translate('messages.delivery_man_details')}}
                </span>
            </h1>
            <div class="js-nav-scroller hs-nav-scroller-horizontal">
                <ul class="nav nav-tabs mb-3 border-0 nav--tabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{route('vendor.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'info'])}}"  aria-disabled="true">{{translate('messages.info')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('vendor.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'transaction'])}}"  aria-disabled="true">{{translate('messages.transaction')}}</a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- End Page Header -->

        <div class="row mb-3 g-3 justify-content-center">
            <!-- Earnings (Monthly) Card Example -->
            <div class="col-sm-6 col-md-4">
                <div class="resturant-card card--bg-1">
                    <h2 class="title">
                        {{$dm->orders->count()}}
                    </h2>
                    <h5 class="subtitle">
                        {{translate('messages.total')}} {{translate('messages.delivered')}} {{translate('messages.orders')}}
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
        <div class="card mb-3 mb-lg-5">
            <div class="card-header py-2">
                <div class="search--button-wrapper">
                    <h4 class="card-title mb-md-0">{{$dm['f_name'].' '.$dm['l_name']}}@if($dm['status']) @if($dm['active']) <label class="badge badge-soft-primary m-0 ml-2">{{translate('messages.online')}}</label> @else <label class="badge badge-soft-danger m-0 ml-2">{{translate('messages.offline')}}</label> @endif  @else <span class="badge badge-danger">{{translate('messages.suspended')}}</span> @endif</h4>

                    <a  href="javascript:"  onclick="request_alert('{{route('vendor.delivery-man.status',[$dm['id'],$dm->status?0:1])}}','{{$dm->status?'Want to suspend this deliveryman ?':'Want to unsuspend this deliveryman'}}')" class="btn {{$dm->status?'btn-danger':'btn-success'}}">
                            {{$dm->status?translate('messages.suspend_this_delivery_man'):translate('messages.unsuspend_this_delivery_man')}}
                    </a>
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
                                        <h1 class="title">{{count($dm->rating)>0?number_format($dm->rating[0]->average, 1, '.', ' '):0}}<span class="out-of">/5</span></h1>
                                        @if (count($dm->rating)>0)
                                        @if ($dm->rating[0]->average == 5)
                                        <div class="rating">
                                            <span><i class="tio-star"></i></span>
                                            <span><i class="tio-star"></i></span>
                                            <span><i class="tio-star"></i></span>
                                            <span><i class="tio-star"></i></span>
                                            <span><i class="tio-star"></i></span>
                                        </div>
                                        @elseif ($dm->rating[0]->average < 5 && $dm->rating[0]->average >= 4.5)
                                        <div class="rating">
                                            <span><i class="tio-star"></i></span>
                                            <span><i class="tio-star"></i></span>
                                            <span><i class="tio-star"></i></span>
                                            <span><i class="tio-star"></i></span>
                                            <span><i class="tio-star-half"></i></span>
                                        </div>
                                        @elseif ($dm->rating[0]->average < 4.5 && $dm->rating[0]->average >= 4)
                                        <div class="rating">
                                            <span><i class="tio-star"></i></span>
                                            <span><i class="tio-star"></i></span>
                                            <span><i class="tio-star"></i></span>
                                            <span><i class="tio-star"></i></span>
                                            <span><i class="tio-star-outlined"></i></span>
                                        </div>
                                        @elseif ($dm->rating[0]->average < 4 && $dm->rating[0]->average >= 3.5)
                                        <div class="rating">
                                            <span><i class="tio-star"></i></span>
                                            <span><i class="tio-star"></i></span>
                                            <span><i class="tio-star"></i></span>
                                            <span><i class="tio-star-half"></i></span>
                                            <span><i class="tio-star-outlined"></i></span>
                                        </div>
                                        @elseif ($dm->rating[0]->average < 3.5 && $dm->rating[0]->average >= 3)
                                        <div class="rating">
                                            <span><i class="tio-star"></i></span>
                                            <span><i class="tio-star"></i></span>
                                            <span><i class="tio-star"></i></span>
                                            <span><i class="tio-star-outlined"></i></span>
                                            <span><i class="tio-star-outlined"></i></span>
                                        </div>
                                        @elseif ($dm->rating[0]->average < 3 && $dm->rating[0]->average >= 2.5)
                                        <div class="rating">
                                            <span><i class="tio-star"></i></span>
                                            <span><i class="tio-star"></i></span>
                                            <span><i class="tio-star-half"></i></span>
                                            <span><i class="tio-star-outlined"></i></span>
                                            <span><i class="tio-star-outlined"></i></span>
                                        </div>
                                        @elseif ($dm->rating[0]->average < 2.5 && $dm->rating[0]->average > 2)
                                        <div class="rating">
                                            <span><i class="tio-star"></i></span>
                                            <span><i class="tio-star"></i></span>
                                            <span><i class="tio-star-outlined"></i></span>
                                            <span><i class="tio-star-outlined"></i></span>
                                            <span><i class="tio-star-outlined"></i></span>
                                        </div>
                                        @elseif ($dm->rating[0]->average < 2 && $dm->rating[0]->average >= 1.5)
                                        <div class="rating">
                                            <span><i class="tio-star"></i></span>
                                            <span><i class="tio-star-half"></i></span>
                                            <span><i class="tio-star-outlined"></i></span>
                                            <span><i class="tio-star-outlined"></i></span>
                                            <span><i class="tio-star-outlined"></i></span>
                                        </div>
                                        @elseif ($dm->rating[0]->average < 1.5 && $dm->rating[0]->average > 1)
                                        <div class="rating">
                                            <span><i class="tio-star"></i></span>
                                            <span><i class="tio-star-outlined"></i></span>
                                            <span><i class="tio-star-outlined"></i></span>
                                            <span><i class="tio-star-outlined"></i></span>
                                            <span><i class="tio-star-outlined"></i></span>
                                        </div>
                                        @elseif ($dm->rating[0]->average < 1 && $dm->rating[0]->average > 0)
                                        <div class="rating">
                                            <span><i class="tio-star-half"></i></span>
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
            {{-- <div class="card-header py-2 justify-content-end border-0">
                <div class="search--button-wrapper justify-content-end">
                    <h5 class="card-title">{{translate('messages.reviewer_list')}} <span class="badge badge-soft-secondary">{{count($reviews)}}</span></h5>
                    <form action="javascript:" id="search-form" class="search-form">
                        @csrf
                        <!-- Search -->
                        <div class="input-group input--group">
                            <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="{{translate('messages.ex_:_search_here')}}" aria-label="Search">
                            <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                        </div>
                        <!-- End Search -->
                    </form>
                    <!-- Unfold -->
                    <div class="hs-unfold mr-2">
                        <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle h--40px" href="javascript:;"
                            data-hs-unfold-options='{
                                "target": "#usersExportDropdown",
                                "type": "css-animation"
                            }'>
                            <i class="tio-download-to mr-1"></i> {{translate('messages.export')}}
                        </a>

                        <div id="usersExportDropdown"
                                class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                            <span class="dropdown-header">{{translate('messages.options')}}</span>
                            <a id="export-copy" class="dropdown-item" href="javascript:;">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{asset('public/assets/admin')}}/svg/illustrations/copy.svg"
                                        alt="Image Description">
                                {{translate('messages.copy')}}
                            </a>
                            <a id="export-print" class="dropdown-item" href="javascript:;">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{asset('public/assets/admin')}}/svg/illustrations/print.svg"
                                        alt="Image Description">
                                {{translate('messages.print')}}
                            </a>
                            <div class="dropdown-divider"></div>
                            <span
                                class="dropdown-header">{{translate('messages.download')}} {{translate('messages.options')}}</span>
                            <a id="export-excel" class="dropdown-item" href="javascript:;">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{asset('public/assets/admin')}}/svg/components/excel.svg"
                                        alt="Image Description">
                                {{translate('messages.excel')}}
                            </a>
                            <a id="export-csv" class="dropdown-item" href="javascript:;">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{asset('public/assets/admin')}}/svg/components/placeholder-csv-format.svg"
                                        alt="Image Description">
                                .{{translate('messages.csv')}}
                            </a>
                            <a id="export-pdf" class="dropdown-item" href="javascript:;">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{asset('public/assets/admin')}}/svg/components/pdf.svg"
                                        alt="Image Description">
                                {{translate('messages.pdf')}}
                            </a>
                        </div>
                    </div>
                    <!-- End Unfold -->
                </div>
            </div> --}}
            <!-- Table -->
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
                        <th class="border-0">{{translate('messages.review')}}</th>
                        <th class="border-0">{{translate('messages.attachment')}}</th>
                        <th class="border-0">{{translate('messages.date')}}</th>
                    </tr>
                    </thead>

                    <tbody>

                    @foreach($reviews as $review)
                        <tr>
                            <td>
                                @if ($review->customer)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-circle">
                                            <img class="avatar-img" width="75" height="75"
                                                onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'"
                                                src="{{asset('storage/app/public/profile/'.$review->customer->image)}}"
                                                alt="Image Description">
                                        </div>
                                        <div class="ml-3">
                                        <span class="d-block h5 text-hover-primary mb-0">{{$review->customer['f_name']." ".$review->customer['l_name']}} <i
                                                class="tio-verified text-primary" data-toggle="tooltip" data-placement="top"
                                                title="Verified Customer"></i></span>
                                            <span class="d-block font-size-sm text-body">{{$review->customer->email}}</span>
                                        </div>
                                    </div>
                                @else
                                    {{translate('messages.customer_not_found')}}
                                @endif
                            </td>
                            <td>
                                <div class="text-wrap" style="width: 18rem;">
                                    <div class="d-flex mb-2">
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
                                @foreach(json_decode($review['attachment'],true) as $attachment)
                                    <img width="100" onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'" src="{{asset('storage/app/public')}}/{{$attachment}}">
                                @endforeach
                            </td>
                            <td>
                                {{date('d M Y '. config('timeformat'),strtotime($review['created_at']))}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <!-- End Table -->

            <!-- Footer -->
            <div class="card-footer">
                <!-- Pagination -->
                <div class="row justify-content-center justify-content-sm-between align-items-sm-center">
                    <div class="col-12">
                        {!! $reviews->links() !!}
                    </div>
                </div>
                <!-- End Pagination -->
            </div>
            <!-- End Footer -->
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
