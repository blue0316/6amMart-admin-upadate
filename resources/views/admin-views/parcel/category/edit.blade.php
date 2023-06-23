@extends('layouts.admin.app')

@section('title',translate('messages.update_parcel_category'))


@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/edit.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.update_parcel_category')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.parcel.category.update',[$parcel_category['id']])}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        @method('PUT')
                        @php($language=\App\Models\BusinessSetting::where('key','language')->first())
                        @php($language = $language->value ?? null)
                        @php($default_lang = 'en')
                        <div class="col-lg-12">
                            @if($language)
                                @php($default_lang = json_decode($language)[0])
                                <ul class="nav nav-tabs mb-4">
                                    @foreach(json_decode($language) as $lang)
                                        <li class="nav-item">
                                            <a class="nav-link lang_link {{$lang == $default_lang? 'active':''}}" href="#" id="{{$lang}}-link">{{\App\CentralLogics\Helpers::get_language_name($lang).'('.strtoupper($lang).')'}}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        <div class="col-lg-6">
                            @if($language)
                                @foreach(json_decode($language) as $lang)
                                    <?php
                                        if(count($parcel_category['translations'])){
                                            $translate = [];
                                            foreach($parcel_category['translations'] as $t)
                                            {
                                                if($t->locale == $lang && $t->key=="name"){
                                                    $translate[$lang]['name'] = $t->value;
                                                }
                                                if($t->locale == $lang && $t->key=="description"){
                                                    $translate[$lang]['description'] = $t->value;
                                                }
                                            }
                                        }
                                    ?>
                                    <div class="{{$lang != $default_lang ? 'd-none':''}} lang_form" id="{{$lang}}-form">
                                        <div class="form-group">
                                            <label class="input-label" for="{{$lang}}_name">{{translate('messages.name')}} ({{strtoupper($lang)}})</label>
                                            <input type="text" name="name[]" id="{{$lang}}_name" class="form-control" placeholder="{{translate('messages.new_food')}}" value="{{$translate[$lang]['name']??$parcel_category['name']}}" {{$lang == $default_lang? 'required':''}} oninvalid="document.getElementById('en-link').click()">
                                        </div>
                                        <input type="hidden" name="lang[]" value="{{$lang}}">
                                        <div class="form-group">
                                            <label class="input-label" for="exampleFormControlInput1">{{translate('messages.short')}} {{translate('messages.description')}} ({{strtoupper($lang)}})</label>
                                            <textarea type="text" name="description[]" class="form-control ckeditor" {{$lang == $default_lang? 'required':''}} oninvalid="document.getElementById('en-link').click()">{!! $translate[$lang]['description']??$parcel_category['description'] !!}</textarea>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div id="{{$default_lang}}-form">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.name')}} (EN)</label>
                                        <input type="text" name="name[]" class="form-control" placeholder="{{translate('messages.new_food')}}" value="{{$parcel_category['name']}}" required>
                                    </div>
                                    <input type="hidden" name="lang[]" value="en">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.short')}} {{translate('messages.description')}}</label>
                                        <textarea type="text" name="description[]" class="form-control ckeditor">{!! $parcel_category['description'] !!}</textarea>
                                    </div>
                                </div>
                            @endif
                            @if($parcel_category->position == 0)
                            {{-- <div class="form-group mb-0">
                                <label class="input-label">{{translate('messages.module')}}</label>
                                <select name="module_id" id="module_id" required class="form-control js-select2-custom"  data-placeholder="{{translate('messages.select')}} {{translate('messages.module')}}">
                                        <option value="" selected disabled>{{translate('messages.select')}} {{translate('messages.module')}}</option>
                                    @foreach(\App\Models\Module::parcel()->get() as $module)
                                        <option value="{{$module->id}}" {{$parcel_category->module_id==$module->id?'selected':''}}>{{$module->module_name}}</option>
                                    @endforeach
                                </select>
                            </div> --}}
                            @endif
                        </div>
                        <div class="col-lg-6">
                            <div class="h-100 d-flex flex-column">
                                <label class="mb-0 mt-auto d-block text-center">
                                    {{translate('messages.image')}}
                                    <small class="text-danger">* ( {{translate('messages.ratio')}} 200x200 )</small>
                                </label>
                                <center class="py-3 my-auto">
                                    <img class="img--130" id="viewer" src="{{asset('storage/app/public/parcel_category')}}/{{$parcel_category['image']}}" alt="" onerror='this.src="{{asset('/public/assets/admin/img/400x400/img2.jpg')}}"' />
                                </center>
                                <div class="custom-file">
                                    <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                            accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                    <label class="custom-file-label" for="customFileEg1">{{translate('messages.choose')}} {{translate('messages.file')}}</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label  class="input-label text-capitalize">{{translate('messages.per_km_shipping_charge')}}</label>
                                <input type="number" step=".01" min="0" placeholder="{{translate('messages.per_km_shipping_charge')}}" class="form-control" name="parcel_per_km_shipping_charge"
                                    value="{{ $parcel_category->parcel_per_km_shipping_charge }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-label text-capitalize">{{translate('messages.minimum_shipping_charge')}}</label>
                                <input type="number" step=".01" min="0" placeholder="{{translate('messages.minimum_shipping_charge')}}" class="form-control" name="parcel_minimum_shipping_charge"
                                    value="{{ $parcel_category->parcel_minimum_shipping_charge }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="btn--container justify-content-end">
                                <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('messages.reset')}}</button>
                                <button type="submit" class="btn btn--primary">{{translate('messages.update')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- End Table -->
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
                $(".from_part_2").removeClass('d-none');
            }
            else
            {
                $(".from_part_2").addClass('d-none');
            }
        });
    </script>
        <script>
            $('#reset_btn').click(function(){
                $('#module_id').val("{{$parcel_category->module_id}}").trigger('change');
                $('#viewer').attr('src', "{{asset('storage/app/public/parcel_category')}}/{{$parcel_category['image']}}");
            })
        </script>
@endpush
