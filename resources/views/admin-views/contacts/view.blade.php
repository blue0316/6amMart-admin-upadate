@extends('layouts.admin.app')
@section('title', translate('Message View'))
@push('css_or_js')
    <link href="{{asset('public/assets/back-end')}}/css/select2.min.css" rel="stylesheet"/>
    <link href="{{asset('public/assets/back-end/css/croppie.css')}}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Heading -->
        <div class="container">
            <!-- Page Title -->
            <div class="mb-3">
                <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                    <img width="20" src="{{asset('/public/assets/back-end/img/message.png')}}" alt="">
                    {{translate('messages.Message_View')}}
                </h2>
            </div>
            <!-- End Page Title -->

            <!-- Content Row -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0 text-capitalize d-flex">
                                <i class="tio-user-big"></i>
                                 {{translate('messages.User_details')}}
                            </h5>
                            <form action="{{route('admin.users.contact.contact-update',$contact->id)}}" method="post">
                                @csrf
                                <div class="form-group d-none">
                                    <div class="row">
                                        <div class="col-md-10">
                                            <h4>{{translate('messages.Feedback')}}</h4>
                                            <textarea class="form-control" name="feedback" placeholder="{{translate('messages.Please_send_a_Feedback')}}">
                                                {{$contact->feedback}}
                                            </textarea>
                                        </div>
                                    </div>
                                </div>


                                <div class="d-flex justify-content-end">
                                    @if($contact->seen==0)
                                        <button type="submit" class="btn btn-success">
                                            <i class="tio-checkmark-circle"></i> {{translate('messages.check')}}
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-info" disabled>
                                            <i class="tio-checkmark-circle"></i> {{translate('messages.already_check')}}
                                        </button>
                                    @endif
                                </div>
                            </form>
                        </div>
                        <div class="card-body">

                            <div class="pl-2 d-flex gap-2 align-items-center mb-3">
                                <strong class="">{{$contact->subject}}</strong>
                                {{-- @if($contact->seen==1)
                                    <label class="badge badge-soft-info mb-0">{{translate('messages.Seen')}}</label>
                                @else
                                    <label class="badge badge-soft-info mb-0">{{translate('messages.Not_Seen_Yet')}}</label>
                                @endif --}}
                            </div>
                            <table class="table table-user-information table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td>{{translate('messages.name')}}:</td>
                                        <td><strong>{{$contact->name}}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>{{translate('messages.Email')}}:</td>
                                        <td><strong>{{$contact->email}}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="card-header justify-content-center">
                            <h5 class="mb-0 text-capitalize">
                                {{translate('messages.Message_Log')}}
                            </h5>
                        </div>
                        <div class="card-body d-flex flex-column gap-2">
                            {{-- <div class="mb-3">
                                <h5 class="px-2 py-1 badge-soft-info rounded mb-3 d-flex">{{translate($contact->name)}}</h5>
                                <div class="flex-start mb-1">
                                    <strong class="">{{translate('Subject')}}: </strong>
                                    <div><strong>{{$contact->subject}}</strong></div>
                                </div>
                                <div class="flex-start">
                                    <strong class="">{{translate('Message')}}: </strong>
                                    <div>{{$contact->message}}</div>
                                </div>
                            </div> --}}
                            <ul class="list-group mb-3">
                                <h5 class="px-2 py-1 badge-soft-info rounded mb-3 d-flex">{{$contact->name}}</h5>
                                <li class="list-group-item"><strong>{{translate('messages.Subject')}}: </strong> <strong>{{$contact->subject}}</strong></li>
                                <li class="list-group-item"><strong>{{translate('messages.Message')}}: </strong><strong>{{$contact->message}}</strong></li>
                              </ul>
                              <h5 class="px-2 py-1 badge-soft-warning rounded mb-3 d-flex">{{translate('messages.admin')}}</h5>
                              @if($contact['reply']!=null)
                              @php($data=json_decode($contact['reply'],true))
                              <ul class="list-group mb-3">
                                <li class="list-group-item"><strong>{{translate('messages.Subject')}}: </strong> <strong>{{$data['subject']}}</strong></li>
                                <li class="list-group-item"><strong>{{translate('messages.Message')}}: </strong><strong>{{$data['body']}}</strong></li>
                              </ul>
                                @else
                                    <label class="badge badge-danger">{{translate('messages.No_reply')}}.</label>
                                @endif
                                <div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body mt-3 mx-lg-4">
                            <div class="row">
                                <div class="col-12">
                                    <center>
                                        <h3>{{translate('Send_Mail')}}</h3>
                                        <label class="badge-soft-danger px-1">{{translate('messages.Configure_your_mail_setup_first')}}.</label>
                                    </center>


                                    <form action="{{route('admin.users.contact.contact-send-mail',$contact->id)}}" method="post">
                                        @csrf
                                        <div class="form-group mt-2">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label class="title-color">{{translate('messages.Subject')}}</label>
                                                    <input class="form-control" name="subject" required>
                                                </div>
                                                <div class="col-md-12 mt-3">
                                                    <label class="title-color">{{translate('messages.Mail_Body')}}</label>
                                                    <textarea class="form-control h-100" name="mail_body"
                                                              placeholder="{{translate('messages.Please_send_a_Feedback')}}" required></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end pt-3 mt-5">
                                            <button type="submit" class="btn btn--primary px-4">
                                            {{translate('messages.send')}}<i class="tio-send ml-2"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')

@endpush
