@extends('layouts.admin.app')

@section('title',$store->name."'s ".translate('messages.reviews'))

@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{asset('public/assets/admin/css/croppie.css')}}" rel="stylesheet">

@endpush

@section('content')
<div class="content container-fluid">
    @include('admin-views.vendor.view.partials._header',['store'=>$store])
    <!-- Page Heading -->
    <div class="tab-content">
        <div class="tab-pane fade show active" id="product">
            <div class="resturant-review-top" id="store_details">
                <div class="resturant-review-left mb-3">
                    @php($reviews = $store->reviews()->with('item',function($query){
                        $query->withoutGlobalScope(\App\Scopes\StoreScope::class);
                    })->get())
                    {{-- {{ dd($reviews) }} --}}
                    @php($user_rating = null)
                    @php($total_rating = 0)
                    @php($total_reviews = 0)
                    @foreach ($reviews as $key=>$value)
                        @php($user_rating += $value->rating)
                        @php($total_rating +=1)
                        @php($total_reviews +=1)
                    @endforeach
                    @php($user_rating = isset($user_rating) ? ($user_rating)/count($reviews) : 0)
                    {{-- {{$review[0]->rating}} --}}
                    <h1 class="title">{{ number_format($user_rating, 1)}}<span class="out-of">/5</span></h1>
                    @if ($user_rating == 5)
                    <div class="rating">
                        <span><i class="tio-star"></i></span>
                        <span><i class="tio-star"></i></span>
                        <span><i class="tio-star"></i></span>
                        <span><i class="tio-star"></i></span>
                        <span><i class="tio-star"></i></span>
                    </div>
                    @elseif ($user_rating < 5 && $user_rating >= 4.5)
                    <div class="rating">
                        <span><i class="tio-star"></i></span>
                        <span><i class="tio-star"></i></span>
                        <span><i class="tio-star"></i></span>
                        <span><i class="tio-star"></i></span>
                        <span><i class="tio-star-half"></i></span>
                    </div>
                    @elseif ($user_rating < 4.5 && $user_rating >= 4)
                    <div class="rating">
                        <span><i class="tio-star"></i></span>
                        <span><i class="tio-star"></i></span>
                        <span><i class="tio-star"></i></span>
                        <span><i class="tio-star"></i></span>
                        <span><i class="tio-star-outlined"></i></span>
                    </div>
                    @elseif ($user_rating < 4 && $user_rating >= 3.5)
                    <div class="rating">
                        <span><i class="tio-star"></i></span>
                        <span><i class="tio-star"></i></span>
                        <span><i class="tio-star"></i></span>
                        <span><i class="tio-star-half"></i></span>
                        <span><i class="tio-star-outlined"></i></span>
                    </div>
                    @elseif ($user_rating < 3.5 && $user_rating >= 3)
                    <div class="rating">
                        <span><i class="tio-star"></i></span>
                        <span><i class="tio-star"></i></span>
                        <span><i class="tio-star"></i></span>
                        <span><i class="tio-star-outlined"></i></span>
                        <span><i class="tio-star-outlined"></i></span>
                    </div>
                    @elseif ($user_rating < 3 && $user_rating >= 2.5)
                    <div class="rating">
                        <span><i class="tio-star"></i></span>
                        <span><i class="tio-star"></i></span>
                        <span><i class="tio-star-half"></i></span>
                        <span><i class="tio-star-outlined"></i></span>
                        <span><i class="tio-star-outlined"></i></span>
                    </div>
                    @elseif ($user_rating < 2.5 && $user_rating > 2)
                    <div class="rating">
                        <span><i class="tio-star"></i></span>
                        <span><i class="tio-star"></i></span>
                        <span><i class="tio-star-outlined"></i></span>
                        <span><i class="tio-star-outlined"></i></span>
                        <span><i class="tio-star-outlined"></i></span>
                    </div>
                    @elseif ($user_rating < 2 && $user_rating >= 1.5)
                    <div class="rating">
                        <span><i class="tio-star"></i></span>
                        <span><i class="tio-star-half"></i></span>
                        <span><i class="tio-star-outlined"></i></span>
                        <span><i class="tio-star-outlined"></i></span>
                        <span><i class="tio-star-outlined"></i></span>
                    </div>
                    @elseif ($user_rating < 1.5 && $user_rating > 1)
                    <div class="rating">
                        <span><i class="tio-star"></i></span>
                        <span><i class="tio-star-outlined"></i></span>
                        <span><i class="tio-star-outlined"></i></span>
                        <span><i class="tio-star-outlined"></i></span>
                        <span><i class="tio-star-outlined"></i></span>
                    </div>
                    @elseif ($user_rating < 1 && $user_rating > 0)
                    <div class="rating">
                        <span><i class="tio-star-half"></i></span>
                        <span><i class="tio-star-outlined"></i></span>
                        <span><i class="tio-star-outlined"></i></span>
                        <span><i class="tio-star-outlined"></i></span>
                        <span><i class="tio-star-outlined"></i></span>
                    </div>
                    @elseif ($user_rating == 1)
                    <div class="rating">
                        <span><i class="tio-star"></i></span>
                        <span><i class="tio-star-outlined"></i></span>
                        <span><i class="tio-star-outlined"></i></span>
                        <span><i class="tio-star-outlined"></i></span>
                        <span><i class="tio-star-outlined"></i></span>
                    </div>
                    @elseif ($user_rating == 0)
                    <div class="rating">
                        <span><i class="tio-star-outlined"></i></span>
                        <span><i class="tio-star-outlined"></i></span>
                        <span><i class="tio-star-outlined"></i></span>
                        <span><i class="tio-star-outlined"></i></span>
                        <span><i class="tio-star-outlined"></i></span>
                    </div>
                    @endif
                    <div class="info">
                        {{-- <span class="mr-3">{{$total_rating}} {{translate('messages.ratings')}}</span> --}}
                        <span>{{$total_reviews}} {{translate('messages.reviews')}}</span>
                    </div>
                </div>
                <div class="resturant-review-right">
                    <ul class="list-unstyled list-unstyled-py-2 mb-0">
                    @php($ratings = $store->rating)
                    @php($five = $ratings[0])
                    @php($four = $ratings[1])
                    @php($three = $ratings[2])
                    @php($two = $ratings[3])
                    @php($one = $ratings[4])
                    @php($total_rating = $one+$two+$three+$four+$five)
                    @php($total_rating = $total_rating==0?1:$total_rating)
                    <!-- Review Ratings -->
                        <li class="d-flex align-items-center font-size-sm">
                            <span
                                class="progress-name mr-3">{{translate('messages.excellent')}}</span>
                            <div class="progress flex-grow-1">
                                <div class="progress-bar" role="progressbar"
                                        style="width: {{($five/$total_rating)*100}}%;"
                                        aria-valuenow="{{($five/$total_rating)*100}}"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span class="ml-3">{{$five}}</span>
                        </li>
                        <!-- End Review Ratings -->

                        <!-- Review Ratings -->
                        <li class="d-flex align-items-center font-size-sm">
                            <span class="progress-name mr-3">{{translate('messages.good')}}</span>
                            <div class="progress flex-grow-1">
                                <div class="progress-bar" role="progressbar"
                                        style="width: {{($four/$total_rating)*100}}%;"
                                        aria-valuenow="{{($four/$total_rating)*100}}"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span class="ml-3">{{$four}}</span>
                        </li>
                        <!-- End Review Ratings -->

                        <!-- Review Ratings -->
                        <li class="d-flex align-items-center font-size-sm">
                            <span class="progress-name mr-3">{{translate('messages.average')}}</span>
                            <div class="progress flex-grow-1">
                                <div class="progress-bar" role="progressbar"
                                        style="width: {{($five/$total_rating)*100}}%;"
                                        aria-valuenow="{{($five/$total_rating)*100}}"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span class="ml-3">{{$three}}</span>
                        </li>
                        <!-- End Review Ratings -->

                        <!-- Review Ratings -->
                        <li class="d-flex align-items-center font-size-sm">
                            <span class="progress-name mr-3">{{translate('messages.below_average')}}</span>
                            <div class="progress flex-grow-1">
                                <div class="progress-bar" role="progressbar"
                                        style="width: {{($two/$total_rating)*100}}%;"
                                        aria-valuenow="{{($two/$total_rating)*100}}"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span class="ml-3">{{$two}}</span>
                        </li>
                        <!-- End Review Ratings -->

                        <!-- Review Ratings -->
                        <li class="d-flex align-items-center font-size-sm">

                            <span class="progress-name mr-3">{{translate('messages.poor')}}</span>
                            <div class="progress flex-grow-1">
                                <div class="progress-bar" role="progressbar"
                                        style="width: {{($one/$total_rating)*100}}%;"
                                        aria-valuenow="{{($one/$total_rating)*100}}"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span class="ml-3">{{$one}}</span>
                        </li>
                        <!-- End Review Ratings -->
                    </ul>
                </div>
            </div>
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                                class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                                data-hs-datatables-options='{
                                    "order": [],
                                    "orderCellsTop": true,
                                    "paging":false
                                }'>
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-0">{{translate('sl')}}</th>
                                    <th class="border-0">{{translate('messages.item')}}</th>
                                    <th class="border-0">{{translate('messages.reviewer')}}</th>
                                    <th class="border-0">{{translate('messages.review')}}</th>
                                    <th class="border-0">{{translate('messages.rating')}}</th>
                                    <th class="border-0">{{translate('messages.date')}}</th>
                                </tr>
                            </thead>

                            <tbody id="set-rows">
                            @php($reviews = $store->reviews()->with('item',function($query){
                                $query->withoutGlobalScope(\App\Scopes\StoreScope::class);
                            })->latest()->paginate(25))

                            @foreach($reviews as $key=>$review)
                                <tr>
                                    <td>{{$key+$reviews->firstItem()}}</td>
                                    <td>
                                    @if ($review->item)
                                        <a class="media align-items-center" href="{{route('admin.item.view',[$review->item['id']])}}">
                                            <img class="avatar avatar-lg mr-3" src="{{asset('storage/app/public/product')}}/{{$review->item['image']}}"
                                                onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'" alt="{{$review->item->name}} image">
                                            <div class="media-body">
                                                <h5 class="text-hover-primary mb-0">{{Str::limit($review->item['name'],10)}}</h5>
                                            </div>
                                        </a>
                                        <span class="ml-10"><a href="{{route('admin.order.details',['id'=>$review->order_id])}}">{{ translate('messages.order_id') }}: {{$review->order_id}}</a></span>
                                    @else
                                        {{translate('messages.Item deleted!')}}
                                    @endif
                                    </td>
                                    <td>
                                    @if($review->customer)
                                        <a class="d-flex align-items-center"
                                        href="{{route('admin.customer.view',[$review['user_id']])}}">
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
                                        </a>
                                    @else
                                        {{translate('messages.customer_not_found')}}
                                    @endif
                                    </td>
                                    <td>
                                        <div class="text-wrap">
                                            <p>
                                                {{Str::limit($review['comment'], 80)}}
                                            </p>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-wrap">
                                            <div class="d-flex mb-2">
                                                <label class="badge badge-soft-info">
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
            </div>
        </div>
    </div>
</div>
@endsection

@push('script_2')
    <!-- Page level plugins -->
    <script>
        // Call the dataTables jQuery plugin
        $(document).ready(function () {
            $('#dataTable').DataTable();
        });
    </script>
    <script>
        $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

            $('#column1_search').on('keyup', function () {
                datatable
                    .columns(1)
                    .search(this.value)
                    .draw();
            });

            $('#column2_search').on('keyup', function () {
                datatable
                    .columns(2)
                    .search(this.value)
                    .draw();
            });

            $('#column3_search').on('change', function () {
                datatable
                    .columns(3)
                    .search(this.value)
                    .draw();
            });

            $('#column4_search').on('keyup', function () {
                datatable
                    .columns(4)
                    .search(this.value)
                    .draw();
            });


            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });

        $('#search-form').on('submit', function () {
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.item.search')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#set-rows').html(data.view);
                    $('.page-area').hide();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });
    </script>
@endpush
