@extends('layouts.admin.app')
@section('title', translate('messages.landing_page_settings'))
@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{ asset('public/assets/admin/css/croppie.css') }}" rel="stylesheet">
@endpush
@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header pb-0">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/landing.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{ translate('messages.landing_page_settings') }}
                </span>
            </h1>
        </div>
        <div class="mb-5">
            <!-- Nav Scroller -->
            <div class="js-nav-scroller hs-nav-scroller-horizontal">
                <!-- Nav -->
                @include('admin-views.business-settings.landing-page-settings.top-menu-links.top-menu-links')
                <!-- End Nav -->
            </div>
            <!-- End Nav Scroller -->
        </div>
        <!-- End Page Header -->
        <div class="card mb-3">
            <div class="card-body">
                <form action="{{ route('admin.business-settings.landing-page-settings', 'feature') }}" method="POST"
                    enctype="multipart/form-data">
                    @php($feature = \App\Models\BusinessSetting::where(['key' => 'feature'])->first())
                    @php($feature = isset($feature->value) ? json_decode($feature->value, true) : null)
                    @csrf
                    <div class="row gy-3">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="input-label" for="feature_title">{{ translate('messages.feature_title') }}</label>
                                <input type="text" id="feature_title" name="feature_title" class="form-control"
                                    placeholder="{{ translate('Feature title') }}">
                            </div>
                            <div class="form-group mb-0">
                                <label class="input-label"
                                    for="feature_description">{{ translate('messages.feature_description') }}</label>
                                <textarea placeholder="{{ translate('Feature description') }}" name="feature_description" class="form-control" cols="30"
                                    rows="10"></textarea>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group h-100 d-flex flex-column mb-0">
                                <label class="input-label text-center d-block mt-auto mb-lg-0">{{ translate('messages.feature_img') }}<small class="text-danger">* (
                                        {{ translate('messages.size') }}: 100 X 100 px )</small></label>
                                <center id="image-viewer-section" class="pt-2 mt-auto mb-auto">
                                    <img class="img--120" id="viewer"
                                        src="{{ asset('public/assets/admin/img/100x100/2.png') }}" alt="Image" />
                                </center>
                                <div class="custom-file mt-2">
                                    <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                        accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                                    <label class="custom-file-label" for="customFileEg1">{{ translate('messages.choose') }}
                                        {{ translate('messages.file') }}</label>
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
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light">
                            <tr>
                                <th class="border-0" scope="col">{{ translate('sl') }}</th>
                                <th class="border-0" scope="col">{{ translate('messages.image') }}</th>
                                <th class="border-0" scope="col">{{ translate('messages.feature_title') }}</th>
                                <th class="border-0" scope="col">{{ translate('messages.feature_description') }}</th>
                                <th class="border-0 text-center" scope="col">{{ translate('messages.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($feature)
                                @foreach ($feature as $key => $feature_item)
                                    <tr>
                                        <th scope="row">{{ $key + 1 }}</th>
                                        <td>
                                            <div class="media align-items-center">
                                                <img class="avatar avatar-lg mr-3"
                                                    src="{{ asset('public/assets/landing/image') }}/{{ $feature_item['img'] }}"
                                                    onerror="this.src='{{ asset('public/assets/admin/img/160x160/img2.jpg') }}'"
                                                    alt="{{ $feature_item['title'] }}">
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                {{ $feature_item['title'] }}
                                            </div>
                                        </td>

                                        <td>
                                            <div class="max-sm-mw-200 word-break max-sm-w-200">
                                                {{ Str::limit($feature_item['feature_description'], 100) }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn--container justify-content-center">
                                                <a class="btn action-btn btn--danger btn-outline-danger" href="javascript:"
                                                    onclick="form_alert('feature-{{ $key }}','{{ translate('messages.Want_to_delete_this_item') }}')"
                                                    title="{{ translate('messages.delete') }}"><i class="tio-delete-outlined"></i>
                                                </a>
                                            </div>
                                            <form
                                                action="{{ route('admin.business-settings.landing-page-settings-delete', ['tab' => 'feature', 'key' => $key]) }}"
                                                method="post" id="feature-{{ $key }}">
                                                @csrf
                                                @method('delete')
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script_2')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#viewer').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#customFileEg1").change(function() {
            readURL(this);
            $('#image-viewer-section').show(1000);
        });
        $(document).on('ready', function() {});

        $('#reset_btn').click(function(){
            $('#viewer').attr('src','{{asset('public/assets/admin/img/400x400/img2.jpg')}}');
        })
    </script>
@endpush
