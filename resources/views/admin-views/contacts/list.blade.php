@extends('layouts.admin.app')

@section('title',translate('messages.Contact Messages'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
            <!-- Page Title -->
            <div class="mb-3">
                <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                    <img width="20" src="{{asset('/public/assets/back-end/img/message.png')}}" alt="">
                    {{translate('messages.all_message_lists')}}
                </h2>
            </div>
            <!-- End Page Title -->
        <!-- End Page Header -->
        <div class="row g-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header py-2 border-0">
                        <div class="search--button-wrapper">
                            <h5 class="card-title">
                                {{translate('messages.message_lists')}} <span class="badge badge-soft-dark ml-2" id="itemCount">{{$contacts->total()}}</span>
                            </h5>
                            <form action="javascript:" id="search-form" class="search-form">
                                <!-- Search -->
                                @csrf
                                <div class="input-group input--group">
                                    <input id="datatableSearch_" type="search" name="search" class="form-control"
                                            placeholder="{{translate('messages.ex_:_message_name')}}" aria-label="Search" required>
                                    <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                                </div>
                                <!-- End Search -->
                            </form>
                        </div>
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
                            <tr class="text-center">
                                <th class="border-0">{{translate('messages.sl')}}</th>
                                <th class="border-0">{{translate('messages.name')}}</th>
                                <th class="border-0">{{translate('messages.email')}}</th>
                                <th class="border-0">{{translate('messages.subject')}}</th>
                                <th class="border-0">{{translate('messages.Seen/Unseen')}}</th>
                                <th class="border-0">{{translate('messages.action')}}</th>
                            </tr>

                            </thead>

                            <tbody id="set-rows">
                            @foreach($contacts as $key=>$contact)
                                <tr>
                                    <td class="text-center">
                                        <span class="mr-3">
                                            {{$key+1}}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="font-size-sm text-body mr-3">
                                            {{Str::limit($contact['name'],20,'...')}}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="font-size-sm text-body mr-3">
                                            {{$contact['email']}}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="font-size-sm text-body mr-3 white--space-initial max-w-180px mx-auto">
                                            {{Str::limit($contact['subject'],40,'...')}}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="font-size-sm text-body mr-3">
                                            @if($contact->seen==1)
                                            <label class="badge badge-soft-success mb-0">{{translate('messages.Seen')}}</label>
                                        @else
                                            <label class="badge badge-soft-info mb-0">{{translate('messages.Not_Seen_Yet')}}</label>
                                        @endif
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="btn action-btn btn--primary btn-outline-primary" href="{{route('admin.users.contact.contact-view',[$contact['id']])}}" title="{{translate('messages.edit')}}"><i class="tio-invisible"></i>
                                            </a>
                                            <a class="btn action-btn btn--danger btn-outline-danger" href="javascript:" onclick="form_alert('contact-{{$contact['id']}}','{{ translate('messages.Want to delete this message?') }}')" title="{{translate('messages.delete')}}"><i class="tio-delete-outlined"></i>
                                            </a>
                                            <form action="{{route('admin.users.contact.contact-delete',[$contact['id']])}}"
                                                    method="post" id="contact-{{$contact['id']}}">
                                                @csrf @method('delete')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @if(count($contacts) !== 0)
                        <hr>
                        @endif
                        <div class="page-area">
                            {!! $contacts->links() !!}
                        </div>
                        @if(count($contacts) === 0)
                        <div class="empty--data">
                            <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                            <h5>
                                {{translate('messages.no_data_found')}}
                            </h5>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <!-- End Table -->
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
                    .columns(1)
                    .search(this.value)
                    .draw();
            });


            $('#column3_search').on('change', function () {
                datatable
                    .columns(2)
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
        $('#search-form').on('submit', function () {
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.users.contact.contact-search')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    console.log(data.view)
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
