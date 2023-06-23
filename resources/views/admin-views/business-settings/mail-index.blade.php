@extends('layouts.admin.app')

@section('title', translate('mail_config'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/email.png')}}" class="w--26" alt="">
                </span>
                <span>{{ translate('messages.smtp') }} {{ translate('messages.mail') }}
                        {{ translate('messages.setup') }}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="row pb-2">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-sm-8">
                                <button class="btn btn--reset" type="button" data-toggle="collapse"
                                    data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                    <i class="tio-email-outlined"></i>
                                    {{ translate('test_your_email_integration') }}
                                </button>
                            </div>
                            <div class="col-2 d-none d-sm-block float-right">
                                <i class="tio-telegram float-right"></i>
                            </div>
                        </div>
                        <div class="collapse" id="collapseExample">
                            <div class="pt-4">
                                <form class="" action="javascript:">
                                    <div class="row gx-3 gy-1">
                                        <div class="col-md-8 col-sm-7">
                                            <div>
                                                <label for="inputPassword2" class="sr-only">
                                                    {{ translate('mail') }}</label>
                                                <input type="email" id="test-email" class="form-control"
                                                    placeholder="{{ translate('messages.Ex:') }} jhon@email.com">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-5">
                                            <button type="button" onclick="{{env('APP_MODE') == 'demo' ? 'call_demo()' : 'send_mail()'}}" class="btn btn--primary h--45px btn-block">
                                                <i class="tio-telegram"></i>
                                                {{ translate('send_mail') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php($config = \App\Models\BusinessSetting::where(['key' => 'mail_config'])->first())
        @php($data = $config ? json_decode($config['value'], true) : null)
        <div class="card">
            <form class="card-body"
                action="{{ env('APP_MODE') != 'demo' ? route('admin.business-settings.mail-config') : 'javascript:' }}"
                method="post" enctype="multipart/form-data">

                @csrf
                {{-- Main Status --}}
                <div class="form-group mb-3 text-center d-flex flex-wrap align-items-center">
                    <label class="control-label h3 mb-0 text-capitalize mr-3">{{ translate('mail_configuration_status') }}</label>
                    <!-- Static Switch Box -->
                    <div class="custom--switch">
                        <input type="checkbox" name="status" value="1" id="switch6" switch="primary" {{isset($data['status'])&&$data['status']==1?'checked':''}}>
                        <label for="switch6" data-on-label="{{ translate('messages.on') }}" data-off-label="{{ translate('messages.off') }}"></label>
                    </div>
                    <!-- Static Switch Box -->
                </div>
                {{-- <div class="form-group mb-0">
                    <label class="form-label">{{ translate('status') }}</label>
                </div>
                <div class="form-group mb-0 mt-2">
                    <input type="radio" name="status" value="1"
                        {{ isset($data['status']) && $data['status'] == 1 ? 'checked' : '' }}>
                    <label class="form-label">{{ translate('Active') }}</label>
                    <br>
                </div>
                <div class="form-group mb-0">
                    <input type="radio" name="status" value="0"
                        {{ isset($data['status']) && $data['status'] == 0 ? 'checked' : '' }}>
                    <label class="form-label">{{ translate('Inactive') }}</label>
                    <br>
                </div> --}}

                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="form-group mb-0">
                            <label class="form-label">{{ translate('messages.mailer') }}
                                {{ translate('messages.name') }}</label><br>
                            <input type="text" placeholder="{{ translate('messages.Ex:') }} Alex" class="form-control" name="name"
                                value="{{ env('APP_MODE') != 'demo' ? $data['name'] ?? '' : '' }}" required>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-0">
                            <label class="form-label">{{ translate('messages.host') }}</label><br>
                            <input type="text" class="form-control" name="host" placeholder="{{translate('messages.Ex_:_mail.6am.one')}}"
                                value="{{ env('APP_MODE') != 'demo' ? $data['host'] ?? '' : '' }}" required>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-0">
                            <label class="form-label">{{ translate('messages.driver') }}</label><br>
                            <input type="text" class="form-control" name="driver" placeholder="{{translate('messages.Ex : smtp')}}"
                                value="{{ env('APP_MODE') != 'demo' ? $data['driver'] ?? '' : '' }}" required>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-0">
                            <label class="form-label">{{ translate('messages.port') }}</label><br>
                            <input type="text" class="form-control" name="port" placeholder="{{translate('messages.Ex : 587')}}"
                                value="{{ env('APP_MODE') != 'demo' ? $data['port'] ?? '' : '' }}" required>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-0">
                            <label class="form-label">{{ translate('messages.username') }}</label><br>
                            <input type="text" placeholder="{{ translate('messages.Ex:') }} ex@yahoo.com" class="form-control" name="username"
                                value="{{ env('APP_MODE') != 'demo' ? $data['username'] ?? '' : '' }}" required>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-0">
                            <label class="form-label">{{ translate('messages.email') }}
                                {{ translate('messages.id') }}</label><br>
                            <input type="text" placeholder="{{ translate('messages.Ex:') }} ex@yahoo.com" class="form-control" name="email"
                                value="{{ env('APP_MODE') != 'demo' ? $data['email_id'] ?? '' : '' }}" required>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-0">
                            <label class="form-label">{{ translate('messages.encryption') }}</label><br>
                            <input type="text" placeholder="{{ translate('messages.Ex:') }} tls" class="form-control" name="encryption"
                                value="{{ env('APP_MODE') != 'demo' ? $data['encryption'] ?? '' : '' }}" required>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-0">
                            <label class="form-label">{{ translate('messages.password') }}</label><br>
                            <input type="text" class="form-control" name="password" placeholder="{{translate('messages.Ex : 5+ Characters')}}"
                                value="{{ env('APP_MODE') != 'demo' ? $data['password'] ?? '' : '' }}" required>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="btn--container justify-content-end">
                            <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                            <button type="{{ env('APP_MODE') != 'demo' ? 'submit' : 'button' }}"
                            onclick="{{ env('APP_MODE') != 'demo' ? '' : 'call_demo()' }}"
                            class="btn btn--primary">{{ translate('messages.save') }}</button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection
@push('script_2')
    <script>
        function ValidateEmail(inputText) {
            var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            if (inputText.match(mailformat)) {
                return true;
            } else {
                return false;
            }
        }

        function send_mail() {
            if (ValidateEmail($('#test-email').val())) {
                Swal.fire({
                    title: '{{ translate('Are you sure?') }}?',
                    text: "{{ translate('a_test_mail_will_be_sent_to_your_email') }}!",
                    showCancelButton: true,
                    confirmButtonColor: '#00868F',
                    cancelButtonColor: 'secondary',
                    confirmButtonText: '{{ translate('Yes') }}!'
                }).then((result) => {
                    if (result.value) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                            }
                        });
                        $.ajax({
                            url: "{{ route('admin.business-settings.mail.send') }}",
                            method: 'GET',
                            data: {
                                "email": $('#test-email').val()
                            },
                            beforeSend: function() {
                                $('#loading').show();
                            },
                            success: function(data) {
                                if (data.success === 2) {
                                    toastr.error(
                                        '{{ translate('email_configuration_error') }} !!'
                                    );
                                } else if (data.success === 1) {
                                    toastr.success(
                                        '{{ translate('email_configured_perfectly!') }}!'
                                    );
                                } else {
                                    toastr.info(
                                        '{{ translate('email_status_is_not_active') }}!'
                                    );
                                }
                            },
                            complete: function() {
                                $('#loading').hide();

                            }
                        });
                    }
                })
            } else {
                toastr.error('{{ translate('invalid_email_address') }} !!');
            }
        }
    </script>
@endpush
