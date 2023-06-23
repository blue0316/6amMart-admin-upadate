@extends('layouts.vendor.app')

@section('title',translate('messages.Campaign List'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-notice"></i> {{translate('messages.item')}} {{translate('messages.campaign')}} <span class="badge badge-soft-dark ml-2" id="itemCount">{{$campaigns->total()}}</span></h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <!-- Card -->
                <div class="card">
                    <div class="card-header py-2 border-0">
                        <h5 class="card-title"></h5>
                        <form id="search-form">
                            @csrf
                            <!-- Search -->
                            <div class="input--group input-group input-group-merge input-group-flush">
                                <input id="datatableSearch" type="search" name="search" class="form-control" placeholder=" {{translate('messages.Search by title')}}" aria-label="{{translate('messages.search_here')}}">
                                <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                            </div>
                            <!-- End Search -->
                        </form>
                    </div>
                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                               class="font-size-sm table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                               data-hs-datatables-options='{
                                 "order": [],
                                 "orderCellsTop": true,
                                 "paging":false
                               }'>
                               <thead class="thead-light">
                            <tr>
                                <th>{{ translate('messages.sl') }}</th>
                                <th >{{translate('messages.title')}}</th>
                                <th >{{translate('messages.date')}}</th>
                                <th >{{translate('messages.time')}}</th>
                                <th >{{translate('messages.price')}}</th>
                                {{-- <th>{{translate('messages.status')}}</th>
                                <th class="text-center">{{translate('messages.action')}}</th> --}}
                            </tr>

                            </thead>

                            <tbody id="set-rows">
                            @foreach($campaigns as $key=>$campaign)
                                <tr>
                                    <td>{{$key+$campaigns->firstItem()}}</td>
                                    <td>
                                        <span class="d-block text-body">{{Str::limit($campaign['title'],25,'...')}}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="bg-gradient-light text-dark">{{$campaign->start_date?$campaign->start_date->format('d M, Y'). ' - ' .$campaign->end_date->format('d M, Y'): 'N/A'}}</span>
                                    </td>
                                    <td>
                                        <span class="bg-gradient-light text-dark">{{$campaign->start_time?$campaign->start_time->format(config('timeformat')). ' - ' .$campaign->end_time->format(config('timeformat')): 'N/A'}}</span>
                                    </td>
                                    <td>{{\App\CentralLogics\Helpers::format_currency($campaign->price)}}</td>
                                    {{-- <td>
                                        <label class="toggle-switch toggle-switch-sm" for="campaignCheckbox{{$campaign->id}}">
                                            <input type="checkbox" onclick="location.href='{{route('admin.campaign.status',['item',$campaign['id'],$campaign->status?0:1])}}'"class="toggle-switch-input" id="campaignCheckbox{{$campaign->id}}" {{$campaign->status?'checked':''}}>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="btn btn-sm btn--primary btn-outline-primary action-btn"
                                                href="{{route('admin.campaign.edit',['item',$campaign['id']])}}" title="{{translate('messages.edit')}} {{translate('messages.campaign')}}"><i class="tio-edit"></i>
                                            </a>
                                            <a class="btn btn-sm btn--danger btn-outline-danger action-btn" href="javascript:"
                                                onclick="form_alert('campaign-{{$campaign['id']}}','Want to delete this item ?')" title="{{translate('messages.delete')}} {{translate('messages.campaign')}}"><i class="tio-delete-outlined"></i>
                                            </a>
                                        </div>
                                        <form action="{{route('admin.campaign.delete-item',[$campaign['id']])}}"
                                                      method="post" id="campaign-{{$campaign['id']}}">
                                            @csrf @method('delete')
                                        </form>
                                    </td> --}}
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="page-area px-4 pb-3">
                            <div class="d-flex align-items-center justify-content-end">
                                <div>
                                    {!! $campaigns->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Table -->
                </div>
                <!-- End Card -->
            </div>
        </div>
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
                url: '{{route('vendor.campaign.searchItem')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#set-rows').html(data.view);
                    $('#itemCount').html(data.count);
                    $('.page-area').hide();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });
    </script>
@endpush
