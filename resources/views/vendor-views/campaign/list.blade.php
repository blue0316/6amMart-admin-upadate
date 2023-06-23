@extends('layouts.vendor.app')

@section('title',translate('messages.Campaign List'))

@push('css_or_js')

@endpush

@section('content')
@php($store_id = \App\CentralLogics\Helpers::get_store_id())
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/campaign.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.campaign_list')}}<span class="badge badge-soft-dark ml-2" id="itemCount">{{$campaigns->total()}}</span>
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <!-- Card -->
        <div class="card">
            <div class="card-header border-0 justify-content-end ">
                <form action="javascript:" id="search-form" class="min--250">
                    @csrf
                    <!-- Search -->
                    <div class="input-group input--group">
                        <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="{{translate('messages.ex_search_name')}}" aria-label="{{translate('messages.search')}}">
                        <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                    </div>
                    <!-- End Search -->
                </form>
            </div>
            <!-- Table -->
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
                            <th class="border-0">{{translate('messages.#')}}</th>
                            <th class="border-0 w-30p">{{translate('messages.title')}}</th>
                            <th class="border-0 w-25p">{{translate('messages.image')}}</th>
                            <th class="border-0 w-25p">{{translate('messages.date_duration')}}</th>
                            <th class="border-0 w-25p">{{translate('messages.time_duration')}}</th>
                            <th class="border-0 text-center">{{translate('messages.status')}}</th>
                        </tr>
                    </thead>

                    <tbody id="set-rows">
                    @foreach($campaigns as $key=>$campaign)
                        <tr>
                            <td>{{$key+$campaigns->firstItem()}}</td>
                            <td>
                                <span class="d-block font-size-sm text-body">
                                    {{Str::limit($campaign['title'],25,'...')}}
                                </span>
                            </td>
                            <td>
                                <div class="overflow-hidden">
                                    <img class="img--vertical max--200 mw--200" src="{{asset('storage/app/public/campaign')}}/{{$campaign['image']}}"onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'">
                                </div>
                            </td>
                            <td>
                                <span class="bg-gradient-light text-dark">{{$campaign->start_date?$campaign->start_date->format('d M, Y'). ' - ' .$campaign->end_date->format('d M, Y'): 'N/A'}}</span>
                            </td>
                            <td>
                                <span class="bg-gradient-light text-dark">{{$campaign->start_time?date(config('timeformat'),strtotime($campaign->start_time)). ' - ' .date(config('timeformat'),strtotime($campaign->end_time)): 'N/A'}}</span>
                            </td>
                            <td class="text-center">
                            <?php
                                $store_ids = [];
                                foreach($campaign->stores as $store)
                                {
                                    $store_ids[] = $store->id;
                                }
                            ?>
                                @if(in_array($store_id,$store_ids))
                                <!-- <button type="button" onclick="location.href='{{route('vendor.campaign.remove-store',[$campaign['id'],$store_id])}}'" title="You are already joined. Click to out from the campaign." class="btn btn-outline-danger">Out</button> -->
                                <span type="button" onclick="form_alert('campaign-{{$campaign['id']}}','{{translate('messages.alert_store_out_from_campaign')}}')" title="You are already joined. Click to out from the campaign." class="badge btn--danger text-white">{{translate('messages.leave')}}</span>
                                <form action="{{route('vendor.campaign.remove-store',[$campaign['id'],$store_id])}}"
                                        method="GET" id="campaign-{{$campaign['id']}}">
                                    @csrf
                                </form>
                                @else
                                <span type="button" class="badge btn--primary text-white" onclick="form_alert('campaign-{{$campaign['id']}}','{{translate('messages.alert_store_join_campaign')}}')" title="Click to join the campaign">{{translate('messages.join')}}</span>
                                <form action="{{route('vendor.campaign.add-store',[$campaign['id'],$store_id])}}"
                                        method="GET" id="campaign-{{$campaign['id']}}">
                                    @csrf
                                </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @if(count($campaigns) !== 0)
                <hr>
                @endif
                <table class="page-area">
                    <tfoot>
                    {!! $campaigns->links() !!}
                    </tfoot>
                </table>
                @if(count($campaigns) === 0)
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

            $('#column1_search').on('keyup', function () {
                datatable
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
    </script>
    <script>
        $('#search-form').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('vendor.campaign.search')}}',
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
