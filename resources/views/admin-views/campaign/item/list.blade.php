@extends('layouts.admin.app')

@section('title',translate('Campaign List'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <h1 class="page-header-title">
                    <span class="page-header-icon">
                        <img src="{{asset('public/assets/admin/img/campaign.png')}}" class="w--26" alt="">
                    </span>
                    <span>
                        {{translate('messages.campaign')}}
                    </span>
                </h1>
                    <a class="btn btn--primary" href="{{route('admin.campaign.add-new', 'item')}}">
                        <i class="tio-add-circle"></i> {{translate('messages.add')}} {{translate('messages.new')}} {{translate('messages.campaign')}}
                    </a>
            </div>
        </div>
        <!-- End Page Header -->
        <!-- Card -->
        <div class="card">
            <div class="card-header border-0 py-2">
                <div class="search--button-wrapper">
                    <h5 class="card-title">{{translate('messages.campaign')}} {{translate('messages.list')}}<span class="badge badge-soft-dark ml-2" id="itemCount">{{$campaigns->total()}}</span></h5>
                    <form id="search-form" class="search-form min--270">
                        @csrf
                        <!-- Search -->
                        <div class="input-group input--group">
                            <input id="datatableSearch" type="search" name="search" class="form-control" placeholder="{{ translate('messages.Ex:') }} {{ translate('Campaign title...') }}" aria-label="{{translate('messages.search_here')}}">
                            <button type="submit" class="btn btn--secondary">
                                <i class="tio-search"></i>
                            </button>
                        </div>
                        <!-- End Search -->
                    </form>
                </div>
            </div>
            <div class="card-body p-0">
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
                            <th class="border-0">{{translate('sl')}}</th>
                            <th class="border-0" >{{translate('messages.title')}}</th>
                            <th class="border-0" >{{translate('messages.date')}}</th>
                            <th class="border-0" >{{translate('messages.time')}}</th>
                            <th class="border-0" >{{translate('messages.price')}}</th>
                            <th class="border-0 text-center">{{translate('messages.status')}}</th>
                            <th class="border-0 text-center">{{translate('messages.action')}}</th>
                        </tr>

                        </thead>

                        <tbody id="set-rows">
                        @foreach($campaigns as $key=>$campaign)
                            <tr>
                                <td>{{$key+$campaigns->firstItem()}}</td>
                                <td>
                                    <a href="{{route('admin.campaign.view',['item',$campaign->id])}}" class="d-block text-body">{{Str::limit($campaign['title'],25,'...')}}</a>
                                </td>
                                <td>
                                    <span class="bg-gradient-light text-dark">{{$campaign->start_date?$campaign->start_date->format('d/M/Y'). ' - ' .$campaign->end_date->format('d/M/Y'): 'N/A'}}</span>
                                </td>
                                <td>
                                    <span class="bg-gradient-light text-dark">{{$campaign->start_time?$campaign->start_time->format(config('timeformat')). ' - ' .$campaign->end_time->format(config('timeformat')): 'N/A'}}</span>
                                </td>
                                <td>{{\App\CentralLogics\Helpers::format_currency($campaign->price)}}</td>
                                <td>
                                    <div class="d-flex flex-wrap justify-content-center">
                                        <label class="toggle-switch toggle-switch-sm" for="campaignCheckbox{{$campaign->id}}">
                                            <input type="checkbox" onclick="location.href='{{route('admin.campaign.status',['item',$campaign['id'],$campaign->status?0:1])}}'"class="toggle-switch-input" id="campaignCheckbox{{$campaign->id}}" {{$campaign->status?'checked':''}}>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn action-btn btn--primary btn-outline-primary"
                                            href="{{route('admin.campaign.edit',['item',$campaign['id']])}}" title="{{translate('messages.edit')}} {{translate('messages.campaign')}}"><i class="tio-edit"></i>
                                        </a>
                                        <a class="btn action-btn btn--danger btn-outline-danger" href="javascript:"
                                            onclick="form_alert('campaign-{{$campaign['id']}}','{{ translate('Want to delete this item ?') }}')" title="{{translate('messages.delete')}} {{translate('messages.campaign')}}"><i class="tio-delete-outlined"></i>
                                        </a>
                                        <form action="{{route('admin.campaign.delete-item',[$campaign['id']])}}"
                                                    method="post" id="campaign-{{$campaign['id']}}">
                                            @csrf @method('delete')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @if(count($campaigns) !== 0)
                    <hr>
                    @endif
                    <div class="page-area">
                        {!! $campaigns->links() !!}
                    </div>
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
                url: '{{route('admin.campaign.searchItem')}}',
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
