@extends('layouts.admin.app')

@section('title',translate('Add new campaign'))

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
                    {{translate('messages.Add new campaign')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.campaign.store-basic')}}" method="post" enctype="multipart/form-data" id="campaign-form">
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
                            <div class="{{$lang != $default_lang ? 'd-none':''}} lang_form" id="{{$lang}}-form">
                                <div class="form-group">
                                    <label class="input-label" for="{{$lang}}_title">{{translate('messages.title')}} ({{strtoupper($lang)}})</label>
                                    <input type="text" {{$lang == $default_lang? 'required':''}} name="title[]" id="{{$lang}}_title" class="form-control" placeholder="{{translate('messages.new_campaign')}}" oninvalid="document.getElementById('en-link').click()">
                                </div>
                                <input type="hidden" name="lang[]" value="{{$lang}}">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('messages.short')}} {{translate('messages.description')}} ({{strtoupper($lang)}})</label>
                                    <textarea type="text" name="description[]" class="form-control ckeditor"></textarea>
                                </div>
                            </div>
                        @endforeach
                    @else
                    <div id="{{$default_lang}}-form">
                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlInput1">{{translate('messages.title')}} (EN)</label>
                            <input type="text" name="title[]" class="form-control" placeholder="{{translate('messages.new_food')}}" required>
                        </div>
                        <input type="hidden" name="lang[]" value="en">
                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlInput1">{{translate('messages.short')}} {{translate('messages.description')}}</label>
                            <textarea type="text" name="description[]" class="form-control ckeditor"></textarea>
                        </div>
                    </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="row g-3">
                                {{-- <div class="col-sm-12">
                                    <div>
                                        <label class="input-label">{{translate('messages.module')}}</label>
                                        <select name="module_id" id="module_id" required class="form-control js-select2-custom"  data-placeholder="{{translate('messages.select')}} {{translate('messages.module')}}" id="module_select">
                                            <option value="" selected disabled>{{translate('messages.select')}} {{translate('messages.module')}}</option>
                                            @foreach(\App\Models\Module::notParcel()->get() as $module)
                                                <option value="{{$module->id}}">{{$module->module_name}}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-danger">{{translate('messages.module_change_warning')}}</small>
                                    </div>
                                </div> --}}
                                <div class="col-sm-6">
                                    <div>
                                        <label class="input-label" for="title">{{translate('messages.start')}} {{translate('messages.date')}}</label>
                                        <input type="date" id="date_from" class="form-control" required="" name="start_date">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div>
                                        <label class="input-label" for="title">{{translate('messages.end')}} {{translate('messages.date')}}</label>
                                        <input type="date" id="date_to" class="form-control" required="" name="end_date">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div>
                                        <label class="input-label text-capitalize" for="title">{{translate('messages.daily')}} {{translate('messages.start')}} {{translate('messages.time')}}</label>
                                        <input type="time" id="start_time" class="form-control" name="start_time">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div>
                                        <label class="input-label text-capitalize" for="title">{{translate('messages.daily')}} {{translate('messages.end')}} {{translate('messages.time')}}</label>
                                        <input type="time" id="end_time" class="form-control" name="end_time">
                                    </div>
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
                                         src="{{asset('public/assets/admin/img/900x400/img1.jpg')}}" alt="campaign image"/>
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
                        <button type="submit" class="btn btn--primary">{{translate('messages.submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script>
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

        $("#date_from").on("change", function () {
            $('#date_to').attr('min',$(this).val());
        });

        $("#date_to").on("change", function () {
            $('#date_from').attr('max',$(this).val());
        });
        $(document).ready(function(){
            $('#date_from').attr('min',(new Date()).toISOString().split('T')[0]);
            $('#date_to').attr('min',(new Date()).toISOString().split('T')[0]);
        });

        $('#campaign-form').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.campaign.store-basic')}}',
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
                        toastr.success('Campaign created successfully!',{
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
            if(lang == '{{$default_lang}}')
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
            $('#module_id').val(null).trigger('change');
            $('#viewer').attr('src','{{asset('public/assets/admin/img/900x400/img1.jpg')}}');
        })
    </script>
@endpush
