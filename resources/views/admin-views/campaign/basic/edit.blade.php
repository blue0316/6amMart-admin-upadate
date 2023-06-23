@extends('layouts.admin.app')

@section('title',translate('messages.Update campaign'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/campaign.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.update')}} {{translate('messages.campaign')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.campaign.update-basic',[$campaign['id']])}}" method="post" id=campaign-form
                      enctype="multipart/form-data">
                      @csrf
                      @php($language=\App\Models\BusinessSetting::where('key','language')->first())
                    @php($language = $language->value ?? null)
                    @php($default_lang = 'bn')
                    @if($language)
                        @php($default_lang = json_decode($language)[0])
                        <ul class="nav nav-tabs mb-4">
                            @foreach(json_decode($language) as $lang)
                                <li class="nav-item">
                                    <a class="nav-link lang_link {{$lang == $default_lang? 'active':''}}" href="#" id="{{$lang}}-link">{{\App\CentralLogics\Helpers::get_language_name($lang).'('.strtoupper($lang).')'}}</a>
                                </li>
                            @endforeach
                        </ul>
                        @foreach(json_decode($language) as $lang)
                            <?php
                                if(count($campaign['translations'])){
                                    $translate = [];
                                    foreach($campaign['translations'] as $t)
                                    {
                                        if($t->locale == $lang && $t->key=="title"){
                                            $translate[$lang]['title'] = $t->value;
                                        }
                                        if($t->locale == $lang && $t->key=="description"){
                                            $translate[$lang]['description'] = $t->value;
                                        }
                                    }
                                }
                            ?>
                            <div class="{{$lang != $default_lang ? 'd-none':''}} lang_form" id="{{$lang}}-form">
                                <div class="form-group">
                                    <label class="input-label" for="{{$lang}}_title">{{translate('messages.title')}} ({{strtoupper($lang)}})</label>
                                    <input type="text" {{$lang == $default_lang? 'required':''}} name="title[]" id="{{$lang}}_title" class="form-control" placeholder="{{translate('messages.new_campaign')}}" value="{{$translate[$lang]['title']??$campaign['title']}}" oninvalid="document.getElementById('en-link').click()">
                                </div>
                                <input type="hidden" name="lang[]" value="{{$lang}}">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('messages.short')}} {{translate('messages.description')}} ({{strtoupper($lang)}})</label>
                                    <textarea type="text" name="description[]" class="form-control ckeditor">{!! $translate[$lang]['description']??$campaign['description'] !!}</textarea>
                                </div>
                            </div>
                        @endforeach
                    @else
                    <div id="{{$default_lang}}-form">
                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlInput1">{{translate('messages.title')}} (EN)</label>
                            <input type="text" name="title[]" class="form-control" placeholder="{{translate('messages.new_campaign')}}" value="{{$campaign['title']}}" maxlength="100" required>
                        </div>
                        <input type="hidden" name="lang[]" value="en">
                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlInput1">{{translate('messages.short')}} {{translate('messages.description')}}</label>
                            <textarea type="text" name="description[]" class="form-control ckeditor" maxlength="255">{!! $campaign['description'] !!}</textarea>
                        </div>
                    </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="row g-3">
                                <div class="col-sm-12">
                                    <div>
                                        <label class="input-label">{{translate('messages.module')}}</label>
                                        <select name="module_id" class="form-control js-select2-custom"  data-placeholder="{{translate('messages.select')}} {{translate('messages.module')}}" id="module_select" disabled>
                                            @foreach(\App\Models\Module::notParcel()->get() as $module)
                                                <option value="{{$module->id}}" {{$module->id == $campaign->module_id?'selected':''}} >{{$module->module_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div>
                                        <label class="input-label" for="title">{{translate('messages.start')}} {{translate('messages.date')}}</label>
                                        <input type="date" id="date_from" class="form-control" required name="start_date" value="{{$campaign->start_date->format('Y-m-d')}}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="input-label" for="title">{{translate('messages.end')}} {{translate('messages.date')}}</label>
                                    <input type="date" id="date_to" class="form-control" required="" name="end_date" value="{{$campaign->end_date->format('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div>
                                        <label class="input-label text-capitalize" for="title">{{translate('messages.daily')}} {{translate('messages.start')}} {{translate('messages.time')}}</label>
                                        <input type="time" id="start_time" class="form-control" name="start_time" value="{{$campaign->start_time}}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="input-label text-capitalize" for="title">{{translate('messages.daily')}} {{translate('messages.end')}} {{translate('messages.time')}}</label>
                                    <input type="time" id="end_time" class="form-control" name="end_time" value="{{$campaign->end_time}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-0 h-100 d-flex flex-column">
                                <label>
                                    {{translate('messages.campaign')}} {{translate('messages.image')}}
                                    <small class="text-danger">* ( {{translate('messages.ratio')}} 900x300 )</small>
                                </label>
                                <center class="py-3 my-auto">
                                    <img class="initial--4" id="viewer"
                                         src="{{asset('storage/app/public/campaign')}}/{{$campaign->image}}"
                                         onerror='this.src="{{ asset('public/assets/admin/img/900x400/img1.jpg') }}"' alt="campaign image"/>
                                </center>
                                <div class="custom-file">
                                    <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                           accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                    <label class="custom-file-label" for="customFileEg1">{{translate('messages.choose')}} {{translate('messages.file')}}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn--container justify-content-end mt-3">
                        <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('messages.reset')}}</button>
                        <button type="submit" class="btn btn--primary">{{translate('messages.update')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        $("#date_from").on("change", function () {
            $('#date_to').attr('min',$(this).val());
        });

        $("#date_to").on("change", function () {
            $('#date_from').attr('max',$(this).val());
        });
        $(document).ready(function(){
            $('#date_from').attr('max','{{$campaign->end_date->format("Y-m-d")}}');
            $('#date_to').attr('min','{{$campaign->start_date->format("Y-m-d")}}');
        });
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this);
        });

        function show_item(type) {
            if (type === 'product') {
                $("#type-product").show();
                $("#type-category").hide();
            } else {
                $("#type-product").hide();
                $("#type-category").show();
            }
        }

        $('#campaign-form').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.campaign.update-basic',[$campaign['id']])}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data.errors) {
                        for (var i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        toastr.success('Campaign updated successfully!', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function () {
                            location.href = '{{route('admin.campaign.list', 'basic')}}';
                        }, 2000);
                    }
                }
            });
        });
    </script>
    <script>
        $(".lang_link").click(function(e){
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.substring(0, form_id.length - 5);
            console.log(lang);
            $("#"+lang+"-form").removeClass('d-none');
            if(lang == 'en')
            {
                $("#from_part_2").removeClass('d-none');
            }
            else
            {
                $("#from_part_2").addClass('d-none');
            }
        })
    </script>
        <script>
            $('#reset_btn').click(function(){
                $('#viewer').attr('src','{{asset('storage/app/public/campaign')}}/{{$campaign->image}}');
            })
        </script>
@endpush
