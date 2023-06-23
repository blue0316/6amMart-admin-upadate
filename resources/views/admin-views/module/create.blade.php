@extends('layouts.admin.app')

@section('title',translate('messages.modules'))

@push('css_or_js')
<link rel="stylesheet" href="{{asset('public/assets/admin/css/radio-image.css')}}">

@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('/public/assets/admin/img/module.png')}}" alt="">
            </span>
            <span>
                {{translate('Add New System Module')}}
            </span>
        </h1>
    </div>
    <!-- End Page Header -->

    <div class="card">
        <div class="card-body">
            <form action="{{route('admin.business-settings.module.store')}}" method="post" enctype="multipart/form-data">
                @csrf
                @php($language=\App\Models\BusinessSetting::where('key','language')->first())
                @php($language = $language->value ?? null)
                @php($default_lang = 'en')
                @if($language)
                @php($default_lang = json_decode($language)[0])
                <ul class="nav nav-tabs mb-4 border-0">
                    @foreach(json_decode($language) as $lang)
                    <li class="nav-item">
                        <a class="nav-link lang_link {{$lang == $default_lang? 'active':''}}" href="#" id="{{$lang}}-link">{{\App\CentralLogics\Helpers::get_language_name($lang).'('.strtoupper($lang).')'}}</a>
                    </li>
                    @endforeach
                </ul>
                @endif
                @if ($language)
                @foreach(json_decode($language) as $lang)
                <div class="{{$lang != $default_lang ? 'd-none':''}} lang_form p-1 mb-2" id="{{$lang}}-form">
                    <div class="form-group">
                        <label class="input-label text-capitalize" for="exampleFormControlInput1">{{translate('messages.module')}} {{translate('messages.name')}} ({{strtoupper($lang)}})</label>
                        <input type="text" name="module_name[]" class="form-control" maxlength="191" {{$lang == $default_lang? 'required':''}} oninvalid="document.getElementById('en-link').click()" placeholder="{{ translate('messages.Ex:') }} {{ translate('Module Name') }}">
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="module_type">{{translate('messages.description')}} ({{strtoupper($lang)}})</label>
                        <textarea class="ckeditor form-control" name="description[]"></textarea>
                    </div>
                </div>

                <input type="hidden" name="lang[]" value="{{$lang}}">
                @endforeach
                @else
                <div class="form-group">
                    <label class="input-label" for="exampleFormControlInput1">{{translate('messages.module')}} {{translate('messages.name')}}</label>
                    <input type="text" name="module_name" class="form-control" placeholder="{{translate('messages.new_category')}}" value="{{old('name')}}" required maxlength="191"  placeholder="{{ translate('messages.Ex:') }} {{ translate('messages.Module Name') }}">
                </div>
                <div class="form-group">
                    <label class="input-label" for="module_type">{{translate('messages.description')}}</label>
                    <textarea class="ckeditor form-control" name="description"></textarea>
                </div>
                <input type="hidden" name="lang[]" value="{{$lang}}">
                @endif
                <!-- <div class="form-group">
                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.name')}}</label>
                        <input type="text" name="name" value="{{isset($module)?$module['name']:''}}" class="form-control" placeholder="New Category" required>
                    </div> -->
                <div class="row mt-2">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="input-label" for="module_type">{{translate('messages.module_type')}}</label>
                            <select name="module_type" id="module_type" class="form-control text-capitalize" onchange="modulChange(this.value)">
                                <option disabled selected>{{translate('messages.select')}} {{translate('messages.module_type')}}</option>
                                @foreach (config('module.module_type') as $key)
                                <option class="" value="{{$key}}">{{$key}}</option>
                                @endforeach
                            </select>
                            <small class="text-danger">{{translate('messages.module_type_change_warning')}}</small>
                            <div class="card mt-1 initial-hidden" id="module_des_card">
                                <div class="card-body" id="module_description"></div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-sm-6">
                        <div class="form-group" id="zone_check">
                            <label class="input-label">{{ translate('Store can serve in') }} <small class="text-danger"><span class="input-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('messages.module_all_zone_hint') }}">
                                        <img src="{{ asset('/public/assets/admin/img/info-circle.svg') }}"
                                            alt="{{ translate('messages.module_all_zone_hint') }}" class="initial--14">
                                </span> *</small></label>
                            <div class="input-group input-group-md-down-break">
                                <!-- Custom Radio -->
                                <div class="form-control">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" value="1"
                                            name="customer_verification" id="all_zone_service1">
                                        <label class="custom-control-label" for="all_zone_service1">{{ translate('messages.All Zones') }}</label>
                                    </div>
                                </div>
                                <!-- End Custom Radio -->

                                <!-- Custom Radio -->
                                <div class="form-control">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" value="0"
                                            name="customer_verification" id="all_zone_service2" >
                                        <label class="custom-control-label"
                                            for="all_zone_service2">{{ translate('One Zone') }}</label>
                                    </div>
                                </div>
                                <!-- End Custom Radio -->
                            </div>
                        </div>
                    </div> --}}
                </div>

                <div class="form-group" id="module_theme">
                    <label class="input-label" for="module_type">{{translate('messages.select_theme')}}</label>
                    <div class="row g-4">
                        <div class='col-lg-3 col-sm-6 col-12 text-center'>
                            <input type="radio" name="theme" require id="img1" class="d-none imgbgchk" value="1">
                            <label for="img1">
                                <img class="img-thumbnail rounded" src="{{asset('public/assets/admin/img/Theme-1.png')}}" alt="Image 1">
                            </label>
                        </div>
                        <div class='col-lg-3 col-sm-6 col-12 text-center'>
                            <input type="radio" name="theme" require id="img2" class="d-none imgbgchk" value="2">
                            <label for="img2">
                                <img class="img-thumbnail rounded" src="{{asset('public/assets/admin/img/Theme-2.png')}}" alt="Image 2">
                            </label>
                        </div>
                        <div class="col-lg-6">
                            {{-- <div class="card h-100 module-logo-card">
                                <div class="card-body">
                                    <div class="row h-100">
                                        <div class="col-sm-6 mb-4 mb-sm-0">
                                            <div class="form-group m-0 h-100 d-flex flex-column justify-content-center">
                                                <label class="form-label mb-0">
                                                    {{translate('messages.icon')}}
                                                    <small class="text-danger">* ( {{translate('messages.ratio')}} 1:1)</small>
                                                </label>
                                                <center class="my-auto py-3">
                                                    <img class="initial--15" id="viewer" src="{{asset('public/assets/admin/img/400x400/img2.jpg')}}" alt="image" />
                                                </center>
                                                <div class="custom-file">
                                                    <input type="file" name="icon" id="customFileEg1" class="custom-file-input" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                                                    <label class="custom-file-label" for="customFileEg1">{{translate('messages.choose')}} {{translate('messages.file')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group m-0 h-100 d-flex flex-column justify-content-center">
                                                <label class="form-label mb-0">
                                                    {{translate('messages.thumbnail')}}
                                                    <small class="text-danger">* ( {{translate('messages.ratio')}} 1:1)</small>
                                                </label>
                                                <center class="my-auto py-3">
                                                    <img class="initial--15" id="viewer2" src="{{asset('public/assets/admin/img/400x400/img2.jpg')}}" alt="image" />
                                                </center>
                                                <div class="custom-file">
                                                    <input type="file" name="thumbnail" id="customFileEg2" class="custom-file-input" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                                                    <label class="custom-file-label" for="customFileEg2">{{translate('messages.choose')}} {{translate('messages.file')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>
                <div class="card h-100 module-logo-card mb-3">
                    <div class="card-body">
                        <div class="row h-100">
                            <div class="col-sm-6 mb-4 mb-sm-0">
                                <div class="form-group m-0 h-100 d-flex flex-column justify-content-center">
                                    <label class="form-label mb-0">
                                        {{translate('messages.icon')}}
                                        <small class="text-danger">* ( {{translate('messages.ratio')}} 1:1)</small>
                                    </label>
                                    <center class="my-auto py-3">
                                        <img class="initial--15" id="viewer" src="{{asset('public/assets/admin/img/400x400/img2.jpg')}}" alt="image" />
                                    </center>
                                    <div class="custom-file">
                                        <input type="file" name="icon" id="customFileEg1" class="custom-file-input" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                                        <label class="custom-file-label" for="customFileEg1">{{translate('messages.choose')}} {{translate('messages.file')}}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group m-0 h-100 d-flex flex-column justify-content-center">
                                    <label class="form-label mb-0">
                                        {{translate('messages.thumbnail')}}
                                        <small class="text-danger">* ( {{translate('messages.ratio')}} 1:1)</small>
                                    </label>
                                    <center class="my-auto py-3">
                                        <img class="initial--15" id="viewer2" src="{{asset('public/assets/admin/img/400x400/img2.jpg')}}" alt="image" />
                                    </center>
                                    <div class="custom-file">
                                        <input type="file" name="thumbnail" id="customFileEg2" class="custom-file-input" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                                        <label class="custom-file-label" for="customFileEg2">{{translate('messages.choose')}} {{translate('messages.file')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btn--container justify-content-end">
                    <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('messages.reset')}}</button>
                    <button type="submit" class="btn btn--primary">{{translate('messages.add')}}</button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection

@push('script_2')
<script>
    function modulChange(id) {
        $.get({
            url: "{{url('/')}}/admin/module/type/?module_type=" + id,
            dataType: 'json',
            success: function(data) {
                if(data.data.description.length)
                {
                    $('#module_des_card').show();
                    $('#module_description').html(data.data.description);
                }
                else
                {
                    $('#module_des_card').hide();
                }
                if(id=='parcel')
                {
                    $('#module_theme').hide();
                    $('#zone_check').hide();
                }
                else{
                    $('#module_theme').show();
                    $('#zone_check').show();
                }
            },
        });
    }

    function readURL(input, id) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#' + id).attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#customFileEg1").change(function() {
        readURL(this, 'viewer');
    });

    $("#customFileEg2").change(function() {
        readURL(this, 'viewer2');
    });
</script>
<script>
    $(".lang_link").click(function(e) {
        e.preventDefault();
        $(".lang_link").removeClass('active');
        $(".lang_form").addClass('d-none');
        $(this).addClass('active');

        let form_id = this.id;
        let lang = form_id.substring(0, form_id.length - 5);
        console.log(lang);
        $("#" + lang + "-form").removeClass('d-none');
        if (lang == '{{$default_lang}}') {
            $(".from_part_2").removeClass('d-none');
        } else {
            $(".from_part_2").addClass('d-none');
        }
    });
</script>
<script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.ckeditor').ckeditor();
    });
</script>
<script>
        $('#reset_btn').click(function(){
            $('#viewer').attr('src','{{asset('public/assets/admin/img/400x400/img2.jpg')}}');
            $('#viewer2').attr('src','{{asset('public/assets/admin/img/400x400/img2.jpg')}}');
        })
</script>
@endpush
