@extends('layouts.vendor.app')

@section('title',translate('messages.Review List'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Heading -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/star.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.customers')}} {{translate('messages.reviews')}}
                </span>
            </h1>
        </div>
        <!-- Page Heading -->
        <!-- Card -->
        <div class="card">
            <!-- Table -->
            <div class="table-responsive datatable-custom">
                <table id="columnSearchDatatable"
                        class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                        data-hs-datatables-options='{
                            "order": [],
                            "orderCellsTop": true,
                            "paging": false
                        }'>
                    <thead class="thead-light">
                    <tr>
                        <th class="border-0">{{translate('messages.#')}}</th>
                        <th class="border-0">{{translate('messages.item')}}</th>
                        <th class="border-0">{{translate('messages.reviewer')}}</th>
                        <th class="border-0">{{translate('messages.review')}}</th>
                        <th class="border-0">{{translate('messages.rating')}}</th>
                        <th class="border-0">{{translate('messages.date')}}</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($reviews as $key=>$review)
                        <tr>
                            <td>{{$key+$reviews->firstItem()}}</td>
                            <td>
                                @if ($review->item)
                                <a class="media align-items-center" href="{{route('vendor.item.view',[$review->item['id']])}}">
                                    <img class="avatar avatar-lg mr-3" src="{{asset('storage/app/public/product')}}/{{$review->item['image']}}"
                                        onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'" alt="{{$review->item->name}} image">
                                    <div class="media-body">
                                        <h5 class="text-hover-primary mb-0">{{Str::limit($review->item['name'],10)}}</h5>
                                    </div>
                                </a>
                                <span class="ml-10"><a href="{{route('vendor.order.details',['id'=>$review->order_id])}}">{{ translate('messages.order_id') }}: {{$review->order_id}}</a></span>
                                @else
                                    {{translate('messages.Item deleted!')}}
                                @endif
                            </td>
                            <td>
                                @if($review->customer)
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-circle">
                                        <img class="avatar-img" width="75" height="75"
                                            onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'"
                                            src="{{asset('storage/app/public/profile/'.$review->customer->image)}}"
                                            alt="Image Description">
                                    </div>
                                    <div class="ml-3">
                                    <span class="d-block h5 text-hover-primary mb-0">{{Str::limit($review->customer['f_name']." ".$review->customer['l_name'], 15)}} <i
                                            class="tio-verified text-primary" data-toggle="tooltip" data-placement="top"
                                            title="Verified Customer"></i></span>
                                        <span class="d-block font-size-sm text-body">{{Str::limit($review->customer->email, 20)}}</span>
                                    </div>
                                </div>
                                @else
                                {{translate('messages.customer_not_found')}}
                                @endif
                            </td>
                            <td>
                                <div class="text-wrap w-18rem">
                                    <p>
                                        {{Str::limit($review['comment'], 80)}}
                                    </p>
                                </div>
                            </td>
                            <td>
                                <div class="text-wrap">
                                    <div class="d-flex mb-2">
                                        <label class="badge badge-soft-warning">
                                            {{$review->rating}} <i class="tio-star"></i>
                                        </label>
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{date('d M Y '.config('timeformat'),strtotime($review['created_at']))}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @if(count($reviews) !== 0)
                <hr>
                @endif
                <table>
                    <tfoot>
                    {!! $reviews->links() !!}
                    </tfoot>
                </table>
                @if(count($reviews) === 0)
                <div class="empty--data">
                    <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                    <h5>
                        {{translate('no_data_found')}}
                    </h5>
                </div>
                @endif
            </div>
            <!-- End Table -->
        </div>
        <!-- End Card -->
    </div>

@endsection

@push('script_2')
    <script>
        $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

        });
    </script>
@endpush
