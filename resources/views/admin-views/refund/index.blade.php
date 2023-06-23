@extends('layouts.admin.app')

@section('title', translate('Refund Settings'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title text-capitalize">
                <div class="card-header-icon d-inline-flex mr-2 img">
                    <img src="{{ asset('/public/assets/admin/img/email.png') }}" alt="public">
                </div>
                <span>
                    {{ translate('messages.refund_settings') }}
                </span>
            </h1>
        </div>

        <!-- End Page Header -->
        <div class="card mb-3">
            <div class="card-body">
                <div
                    class="maintainance-mode-toggle-bar d-flex flex-wrap justify-content-between border border-primary rounded align-items-center p-2">
                    @php($config = $refund_active_status->value ?? null)
                    <h5 class="text-capitalize m-0 text--info text--primary">
                        <i class="tio-settings-outlined"></i>
                        {{ translate('messages.Refund Request') }} {{ translate('Mode') }}
                    </h5>
                    <label class="toggle-switch toggle-switch-sm">
                        <input type="checkbox" class="status toggle-switch-input" onclick="refund_mode()"
                            {{ isset($config) && $config ? 'checked' : '' }}>
                        <span class="toggle-switch-label text mb-0">
                            <span class="toggle-switch-indicator"></span>
                        </span>
                    </label>
                </div>
                <div class="mt-2">
                    {{ translate('*By Turning ON Refund Mode, Customers Can Sent Refund Requests') }}
                </div>
            </div>
        </div>



        <div class="col-lg-12 pt-sm-3">
            <div class="report-card-inner mb-4 pt-3 mw-100">
                <form action="{{ route('admin.refund.refund_reason') }}" method="post">
                    @csrf
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-md-0 mb-3">
                        <div class="mx-1">
                            <h5 class="form-label mb-0">
                                {{ translate('messages.Add a Refund Reason') }}
                            </h5>
                        </div>
                    </div>
                    <div class="row g-2 align-items-end">
                        <div class="col-md-10">
                            <div>
                                <label class="floating-label" for="refund_reason"></label>
                                <input type="text" class="form-control h--45px" name="reason" id="refund_reason"
                                    value="{{ old('reason') }}" placeholder="Ex: Item is Broken" required>
                            </div>
                        </div>

                        <div class="col-md-auto">
                            <button type="submit"
                                class="btn btn--primary h--45px btn-block">{{ translate('messages.Add Now') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body mb-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-md-0 mb-3">
                    <div class="mx-1">
                        <h5 class="form-label mb-0">
                            {{ translate('Refund Reason List') }}
                        </h5>
                    </div>
                </div>




                <!-- Table -->
                <div class="card-body p-0">
                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                            class="table table-borderless table-thead-bordered table-align-middle"
                            data-hs-datatables-options='{
                        "isResponsive": false,
                        "isShowPaging": false,
                        "paging":false,
                    }'>
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-0">{{ translate('messages.SL') }}</th>
                                    <th class="border-0">{{ translate('messages.Reason') }}</th>
                                    <th class="border-0">{{ translate('messages.status') }}</th>
                                    <th class="border-0 text-center">{{ translate('messages.action') }}</th>
                                </tr>
                            </thead>

                            <tbody id="table-div">
                                @foreach ($reasons as $key => $reason)
                                    <tr>
                                        <td>{{ $key + $reasons->firstItem() }}</td>

                                        <td>
                                            <span class="d-block font-size-sm text-body">
                                                {{ Str::limit($reason->reason, 25, '...') }}
                                            </span>
                                        </td>
                                        <td>
                                            <label class="toggle-switch toggle-switch-sm"
                                                for="stocksCheckbox{{ $reason->id }}">
                                                <input type="checkbox"
                                                    onclick="location.href='{{ route('admin.refund.reason_status', [$reason['id'], $reason->status ? 0 : 1]) }}'"class="toggle-switch-input"
                                                    id="stocksCheckbox{{ $reason->id }}"
                                                    {{ $reason->status ? 'checked' : '' }}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </td>

                                        <td>
                                            <div class="btn--container justify-content-center">
                                                <button
                                                    class="btn action-btn btn--primary btn-outline-primary identifyingClass"
                                                    data-id={{ $reason['id'] }}
                                                    title="{{ translate('messages.edit') }} {{ translate('messages.category') }}"
                                                    data-toggle="modal" data-target="#exampleModal{{ $reason['id'] }}">
                                                    <i class="tio-edit"></i>
                                                </button>
                                                <!-- Modal -->
                                                <div class="modal fade" id="exampleModal{{ $reason['id'] }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">{{ translate('messages.Reason') }}
                                                                    {{ translate('messages.Update') }}</label></h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form action="{{ route('admin.refund.reason_edit') }}" method="post">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="reason_id" id="hiddenValue" value="{{ $reason['id'] }}" />
                                                                    <input class="form-control" name='reason' id="reason_title" value="{{ $reason['reason'] }}" required type="text">
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('messages.Close') }}</button>
                                                                <button type="submit" class="btn btn-primary">{{ translate('messages.save_changes') }}</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <a class="btn btn-sm btn--danger btn-outline-danger action-btn"
                                                    href="javascript:"
                                                    onclick="form_alert('refund_reason-{{ $reason['id'] }}','{{ translate('Want to delete this refund reason ?') }}')"
                                                    title="{{ translate('messages.delete') }}">
                                                    <i class="tio-delete-outlined"></i>
                                                </a>
                                                <form action="{{ route('admin.refund.reason_delete', [$reason['id']]) }}"
                                                    method="post" id="refund_reason-{{ $reason['id'] }}">
                                                    @csrf @method('delete')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- End Table -->

            </div>
        </div>
    </div>


@endsection
@push('script_2')
    <script>
        function refund_mode() {

            Swal.fire({
                title: 'Are you sure?',
                text: 'Be careful before you turn on/off Refund Request mode',
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#377dff',
                cancelButtonText: 'No',
                confirmButtonText: 'Yes',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.get({
                        url: '{{ route('admin.refund.refund_mode') }}',
                        contentType: false,
                        processData: false,
                        beforeSend: function() {
                            $('#loading').show();
                        },
                        success: function(data) {
                            toastr.success(data.message);
                        },
                        complete: function() {
                            $('#loading').hide();
                        },
                    });
                } else {
                    location.reload();
                }
            })

        };
    </script>
    {{-- <script type="text/javascript">
        $(function() {
            $(".identifyingClass").click(function() {
                var my_id_value = $(this).data('id');
                $(".modal-body #hiddenValue").val(my_id_value);
                $(".modal-body #hiddenValue").val(my_id_value);
            })
        });
    </script> --}}
@endpush
