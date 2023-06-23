@extends('layouts.admin.app')

@section('title', translate('Social Login Setup'))


@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/captcha.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('Social Login Setup')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->

        <div class="row mt-3">
            @if (isset($socialLoginServices))
            @foreach ($socialLoginServices as $socialLoginService)
                    <div class="col-md-6 mt-4">
                        <div class="card">
                            <div class="card-body text-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}">
                                <div class="flex-between">
                                    <h4 class="text-center">{{translate('messages.'.$socialLoginService['login_medium'])}}</h4>
                                    <div class="btn cursor-pointer btn-dark p-2" data-toggle="modal" data-target="#{{$socialLoginService['login_medium']}}-modal">
                                        <i class="tio-info-outined"></i> {{translate('messages.credentials_setup')}}
                                    </div>
                                </div>
                                <form
                                    action="{{route('admin.social-login.update',[$socialLoginService['login_medium']])}}"
                                    method="post">
                                    @csrf
                                    <div class="form-group mb-2 mt-5">
                                        <input type="radio" name="status"
                                               value="1" {{$socialLoginService['status']==1?'checked':''}}>
                                        <label class="{{Session::get('direction') === "rtl" ? 'pr-1' : 'pl-1'}}">{{translate('messages.active')}}</label>
                                        <br>
                                    </div>
                                    <div class="form-group mb-2">
                                        <input type="radio" name="status"
                                               value="0" {{$socialLoginService['status']==0?'checked':''}}>
                                        <label class="{{Session::get('direction') === "rtl" ? 'pr-1' : 'pl-1'}}">{{translate('messages.inactive')}}</label>
                                        <br>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="{{Session::get('direction') === "rtl" ? 'pr-1' : 'pl-1'}}">{{translate('messages.callback_uri')}}</label>
                                        <span class="btn btn-secondary btn-sm m-2" onclick="copyToClipboard('#id_{{$socialLoginService['login_medium']}}')"><i class="tio-copy"></i> {{translate('messages.copy_uri')}}</span>
                                        <br>
                                        <span class="form-control h-unset" id="id_{{$socialLoginService['login_medium']}}">{{ url('/') }}/customer/auth/login/{{$socialLoginService['login_medium']}}/callback</span>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label
                                            class="{{Session::get('direction') === "rtl" ? 'pr-1' : 'pl-1'}}">{{translate('messages.client_id')}}</label><br>
                                        <input type="text" class="form-control" name="client_id"
                                               value="{{ $socialLoginService['client_id'] }}">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label
                                            class="{{Session::get('direction') === "rtl" ? 'pr-1' : 'pl-1'}}">{{translate('messages.client_secret')}}</label><br>
                                        <input type="text" class="form-control" name="client_secret"
                                               value="{{ $socialLoginService['client_secret'] }}">
                                    </div>
                                    <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                            class="btn btn--primary mb-2">{{translate('messages.save')}}</button>
                                </form>
                            </div>
                        </div>
                    </div>
            @endforeach
            @endif
        </div>
    </div>
            {{-- Modal Starts--}}
        <!-- Google -->
        <div class="modal fade" id="google-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content {{Session::get('direction') === "rtl" ? 'text-right' : 'text-left'}}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">{{translate('messages.google_api_setup_instructions')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <ol>
                            <li>{{translate('messages.go_to_the_credentials_page')}} ({{translate('messages.click')}} <a href="https://console.cloud.google.com/apis/credentials" target="_blank">{{translate('here')}}</a>)</li>
                            <li>{{translate('messages.click')}} <b>{{translate('messages.create_credentials')}}</b> > <b>{{translate('messages.auth_client_id')}}</b>.</li>
                            <li>{{translate('messages.select_the')}} <b>{{translate('messages.web_application')}}</b> {{translate('messages.type')}}.</li>
                            <li>{{translate('messages.name_your_auth_client')}}</li>
                            <li>{{translate('messages.click')}} <b>{{translate('messages.add_uri')}}</b> {{translate('messages.from')}} <b>{{translate('messages.authorized_redirect_uris')}}</b> , {{translate('messages.provide_the')}} <code>{{translate('messages.callback_uri')}}</code> {{translate('messages.from_below_and_click')}} <b>{{translate('messages.created')}}</b></li>
                            <li>{{translate('messages.copy')}} <b>{{translate('messages.client_id')}}</b> {{translate('messages.and')}} <b>{{translate('messages.client_secret')}}</b>, {{translate('messages.past_in_the_input_field_below_and')}} <b>Save</b>.</li>
                        </ol>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--primary" data-dismiss="modal">{{translate('messages.close')}}</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Facebook -->
        <div class="modal fade" id="facebook-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content {{Session::get('direction') === "rtl" ? 'text-right' : 'text-left'}}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">{{translate('messages.facebook_api_set_instruction')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body"><b></b>
                        <ol>
                            <li>{{translate('messages.goto_the_facebook_developer_page')}} (<a href="https://developers.facebook.com/apps/" target="_blank">{{translate('messages.click_here')}}</a>)</li>
                            <li>{{translate('messages.goto')}} <b>{{translate('messages.get_started')}}</b> {{translate('messages.from_navbar')}}</li>
                            <li>{{translate('messages.from_register_tab_press')}} <b>{{translate('messages.continue')}}</b> <small>({{translate('messages.if_needed')}})</small></li>
                            <li>{{translate('messages.provide_primary_email_and_press')}} <b>{{translate('messages.confirm_email')}}</b> <small>({{translate('messages.if_needed')}})</small></li>
                            <li>{{translate('messages.in_about_section_select')}} <b>{{translate('messages.other')}}</b> {{translate('messages.and_press')}} <b>{{translate('messages.complete_registration')}}</b></li>

                            <li><b>{{translate('messages.create_app')}}</b> > {{translate('messages.select_an_app_type_and_press')}} <b>{{translate('messages.next')}}</b></li>
                            <li>{{translate('messages.complete_the_details_form_and_press')}} <b>{{translate('messages.create_app')}}</b></li><br/>

                            <li>{{translate('messages.form')}} <b>{{translate('messages.facebook_login')}}</b> {{translate('messages.press')}} <b>{{translate('messages.set_up')}}</b></li>
                            <li>{{translate('messages.select')}} <b>{{translate('messages.web')}}</b></li>
                            <li>{{translate('messages.provide')}} <b>{{translate('messages.site_url')}}</b> <small>({{translate('messages.base_url_of_the_site')}}: https://example.com)</small> > <b>{{translate('messages.save')}}</b></li><br/>
                            <li>{{translate('messages.now_go_to')}} <b>{{translate('messages.setting')}}</b> {{translate('messages.form')}} <b>{{translate('messages.facebook_login')}}</b> ({{translate('messages.left_sidebar')}})</li>
                            <li>{{translate('messages.make_sure_to_check')}} <b>{{translate('messages.client_auth_login')}}</b> <small>({{translate('messages.must_on')}})</small></li>
                            <li>{{translate('messages.provide')}} <code>{{translate('messages.valid_auth_redirect_uris')}}</code> {{translate('messages.from_below_and_click')}} <b>{{translate('messages.save_changes')}}</b></li>

                            <li>{{translate('messages.now_go_to')}} <b>{{translate('messages.setting')}}</b> ({{translate('messages.from_left_sidebar')}}) > <b>{{translate('messages.basic')}}</b></li>
                            <li>{{translate('messages.fill_the_form_and_press')}} <b>{{translate('messages.save_changes')}}</b></li>
                            <li>{{translate('messages.now_copy')}} <b>{{translate('messages.client_id')}}</b> & <b>{{translate('messages.client_secret')}}</b>, {{translate('messages.past_in_the_input_field_below_and')}} <b>{{translate('messages.save')}}</b>.</li>
                        </ol>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">{{translate('messages.close')}}</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Twitter -->
        <div class="modal fade" id="twitter-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content {{Session::get('direction') === "rtl" ? 'text-right' : 'text-left'}}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">{{translate('messages.twitter_api_set_up_instructions')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body"><b></b>
                        {{translate('messages.instruction_will_be_available_very_soon')}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">{{translate('messages.close')}}</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- Modal Ends--}}
@endsection
@push('script_2')
    <script>
        function copyToClipboard(element) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val($(element).text()).select();
            document.execCommand("copy");
            $temp.remove();

            toastr.success("{{translate('Copied to the clipboard')}}");
        }
    </script>

@endpush
