@extends('layouts.admin.app')
@section('title', translate('messages.social_media'))
@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{ asset('public/assets/admin/css/croppie.css') }}" rel="stylesheet">
@endpush
@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title mr-3">
                <span class="page-header-icon">
                    <img src="{{asset('/public/assets/admin/img/social.png')}}" class="w--26" alt="">
                </span>
                <span>
                     {{translate('social_media')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="card mb-3">
            <div class="card-body">
                <form class="text-left" action="javascript:">
                    @csrf
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="name" class="form-label">{{ translate('messages.name') }}</label>
                                <select class="form-control w-100" name="name" id="name">
                                    <option>---{{ translate('messages.select') }}---</option>
                                    <option value="instagram">{{ translate('messages.Instagram') }}</option>
                                    <option value="facebook">{{ translate('messages.Facebook') }}</option>
                                    <option value="twitter">{{ translate('messages.Twitter') }}</option>
                                    <option value="linkedin">{{ translate('messages.LinkedIn') }}</option>
                                    <option value="pinterest">{{ translate('messages.Pinterest') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="hidden" id="id">
                                <label for="link"
                                    class="form-label {{ Session::get('direction') === 'rtl' ? 'mr-1' : '' }}">{{ translate('messages.social_media_link') }}</label>
                                <input type="text" name="link" class="form-control" id="link"
                                    placeholder="{{ translate('messages.social_media_link') }}" required>
                            </div>
                            <div class="col-md-12">
                                <input type="hidden" id="id">
                            </div>

                        </div>
                    </div>
                    <div class="btn--container justify-content-end">
                        <button type="reset" class="btn btn--reset">{{ translate('messages.reset') }}</button>
                        <button id="add" class="btn btn--primary">{{ translate('messages.save') }}</button>
                        <a href="javascript:" id="update" class="initial-hidden btn btn--primary">{{ translate('messages.update') }}</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light">
                            <tr>
                                <th class="border-0" scope="col">{{ translate('messages.sl') }}</th>
                                <th class="border-0" scope="col">{{ translate('messages.name') }}</th>
                                <th class="border-0" scope="col">{{ translate('messages.link') }}</th>
                                <th class="border-0" scope="col">{{ translate('messages.status') }}</th>
                                <th class="border-0" scope="col">{{ translate('messages.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script_2')
    <script>
        // $.ajaxSetup({
        //     headers: {
        //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //     }
        // });

        fetch_social_media();

        function fetch_social_media() {

            $.ajax({
                url: "{{ route('admin.business-settings.social-media.fetch') }}",
                method: 'GET',
                success: function(data) {
                    if (data.length != 0) {
                        var html = '';
                        for (var count = 0; count < data.length; count++) {
                            html += '<tr>';
                            html += '<td class="column_name" data-column_name="sl" data-id="' + data[count].id +
                                '">' + (count + 1) + '</td>';
                            html += '<td class="column_name" data-column_name="name" data-id="' + data[count]
                                .id + '">' + data[count].name + '</td>';
                            html += '<td class="column_name" data-column_name="slug" data-id="' + data[count]
                                .id + '">' + data[count].link + '</td>';
                            html += `<td class="column_name" data-column_name="status" data-id="${data[count].id}">
                            <label class="toggle-switch toggle-switch-sm" for="${data[count].id}">
                                    <input type="checkbox" class="toggle-switch-input status" id="${data[count].id}" ${data[count].status == 1 ? "checked" : ""}>
                                    <span class="toggle-switch-label">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                        </td>`;
                            // html += '<td><a type="button" class="btn btn-primary btn-xs edit" id="' + data[count].id + '"><i class="fa fa-edit text-white"></i></a> <a type="button" class="btn btn-danger btn-xs delete" id="' + data[count].id + '"><i class="fa fa-trash text-white"></i></a></td></tr>';
                            html += '<td><a type="button" class="btn btn--primary btn-outline-primary edit action-btn" id="' + data[
                                count].id + '"><i class="tio-edit"></i></a> </td></tr>';
                        }
                        $('tbody').html(html);
                    }
                }
            });
        }

        $('#add').on('click', function() {
            // $('#add').attr("disabled", true);
            var name = $('#name').val();
            var link = $('#link').val();
            if (name == "") {
                toastr.error('{{ translate('messages.social_media_required') }}.');
                return false;
            }
            if (link == "") {
                toastr.error('{{ translate('messages.social_media_required') }}.');
                return false;
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('admin.business-settings.social-media.store') }}",
                method: 'POST',

                data: {
                    name: name,
                    link: link
                },
                success: function(response) {
                    if (response.error == 1) {
                        toastr.error('{{ translate('messages.social_media_exist') }}');
                    } else {
                        toastr.success('{{ translate('messages.social_media_inserted') }}.');
                    }
                    $('#name').val('');
                    $('#link').val('');
                    fetch_social_media();
                }
            });
        });
        $(document).on('click', '.edit', function() {
            $('#update').show();
            $('#add').hide();
            var id = $(this).attr("id");
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ url('admin/business-settings/social-media') }}/" + id,
                method: 'GET',
                success: function(data) {
                    $(window).scrollTop(0);
                    $('#id').val(data.id);
                    $('#name').val(data.name);
                    $('#link').val(data.link);
                    fetch_social_media()
                }
            });
        });

        $('#update').on('click', function() {
            $('#update').attr("disabled", true);
            var id = $('#id').val();
            var name = $('#name').val();
            var link = $('#link').val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ url('admin/business-settings/social-media') }}/" + id,
                method: 'PUT',
                data: {
                    id: id,
                    name: name,
                    link: link,
                },
                success: function(data) {
                    $('#name').val('');
                    $('#link').val('');

                    toastr.success('{{ translate('messages.social_media_updated') }}');
                    $('#update').hide();
                    $('#add').show();
                    fetch_social_media();

                }
            });
            $('#save').hide();
        });
        $(document).on('click', '.delete', function() {
            var id = $(this).attr("id");
            if (confirm("{{ translate('messages.are_u_sure_want_to_delete') }}?")) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ url('admin/business-settings/social-media/destroy') }}/" + id,
                    method: 'POST',
                    data: {
                        id: id
                    },
                    success: function(data) {
                        fetch_social_media();
                        toastr.success('{{ translate('messages.social_media_deleted') }}.');
                    }
                });
            }
        });

        $(document).on('change', '.status', function() {
            var id = $(this).attr("id");
            if ($(this).prop("checked") == true) {
                var status = 1;
            } else if ($(this).prop("checked") == false) {
                var status = 0;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('admin.business-settings.social-media.status-update') }}",
                method: 'get',
                data: {
                    id: id,
                    status: status
                },
                success: function() {
                    toastr.success('{{ translate('messages.status_updated') }}');
                }
            });
        });
    </script>
@endpush
