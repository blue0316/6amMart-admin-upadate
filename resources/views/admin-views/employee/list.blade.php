@extends('layouts.admin.app')
@section('title',translate('Employee List'))
@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">
    <div class="page-header">
    <!-- Page Heading -->
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <h1 class="page-header-title mb-3 mr-1">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/role.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.Employee')}} {{translate('messages.list')}}
                </span>
            </h1>
            <a href="{{route('admin.users.employee.add-new')}}" class="btn btn--primary mb-3">
                <i class="tio-add-circle"></i>
                <span class="text">{{translate('messages.add')}} {{translate('messages.new')}}</span>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-2 border-0">
                    <div class="search--button-wrapper">
                        <h5 class="card-title">{{translate('messages.Employee')}} {{translate('messages.table')}} <span class="badge badge-soft-dark ml-2" id="itemCount">{{$em->total()}}</span></h5>
                        <form action="javascript:" id="search-form" class="search-form min--200">
                            @csrf
                            <!-- Search -->
                            <div class="input-group input--group">
                                <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="{{translate('messages.ex_:_search_name')}}" aria-label="Search">
                                <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                            </div>
                            <!-- End Search -->
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="datatable"
                               class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100"
                               data-hs-datatables-options='{
                                 "order": [],
                                 "orderCellsTop": true,
                                 "paging":false
                               }'>
                            <thead class="thead-light">
                            <tr>
                                <th class="border-0">{{translate('sl')}}</th>
                                <th class="border-0">{{translate('messages.name')}}</th>
                                <th class="border-0">{{translate('messages.email')}}</th>
                                <th class="border-0">{{translate('messages.phone')}}</th>
                                <th class="border-0">{{translate('messages.Role')}}</th>
                                <th class="border-0 text-center">{{translate('messages.action')}}</th>
                            </tr>
                            </thead>
                            <tbody id="set-rows">
                            @foreach($em as $k=>$e)
                                <tr>
                                    <th scope="row">{{$k+$em->firstItem()}}</th>
                                    <td class="text-capitalize">{{$e['f_name']}} {{$e['l_name']}}</td>
                                    <td >
                                      {{$e['email']}}
                                    </td>
                                    <td>{{$e['phone']}}</td>
                                    <td>{{$e->role?$e->role['name']:translate('messages.role_deleted')}}</td>
                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="btn action-btn btn--primary btn-outline-primary"
                                                href="{{route('admin.users.employee.edit',[$e['id']])}}" title="{{translate('messages.edit')}} {{translate('messages.Employee')}}"><i class="tio-edit"></i>
                                            </a>
                                            <a class="btn action-btn btn--danger btn-outline-danger" href="javascript:"
                                                onclick="form_alert('employee-{{$e['id']}}','{{translate('messages.Want_to_delete_this_role')}}')" title="{{translate('messages.delete')}} {{translate('messages.Employee')}}"><i class="tio-delete-outlined"></i>
                                            </a>
                                        </div>
                                        <form action="{{route('admin.users.employee.delete',[$e['id']])}}"
                                                method="post" id="employee-{{$e['id']}}">
                                            @csrf @method('delete')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(count($em) !== 0)
                <hr>
                @endif
                <div class="page-area">
                    {!! $em->links() !!}
                </div>
                @if(count($em) === 0)
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
@endsection

@push('script_2')
    <script>
        // Call the dataTables jQuery plugin
        $(document).ready(function () {
            $('#dataTable').DataTable();
        });
        $('#search-form').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.users.employee.search')}}',
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
