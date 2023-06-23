@extends('layouts.admin.app')

@section('title',translate('messages.landing_page_settings'))

@push('css_or_js')
    <link href="{{asset('public/assets/admin/css/croppie.css')}}" rel="stylesheet">
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
    <!-- Nav Scroller -->
    <div class="mb-5">
        <div class="js-nav-scroller hs-nav-scroller-horizontal">
            <!-- Nav -->
            @include('admin-views.business-settings.landing-page-settings.top-menu-links.top-menu-links')
            <!-- End Nav -->
        </div>
    </div>
    <!-- End Nav Scroller -->

    <!-- End Page Header -->
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.business-settings.landing-page-settings', 'text') }}" method="POST">
                @php($landing_page_text = \App\Models\BusinessSetting::where(['key' => 'landing_page_text'])->first())
                @php($landing_page_text = isset($landing_page_text->value) ? json_decode($landing_page_text->value, true) : null)
                @csrf
                <div class="form-group">
                    <label for="header_title_1">{{ translate('Header Title') }}</label>
                    <input type="text" id="header_title_1" name="header_title_1" class="form-control"
                        value="{{ isset($landing_page_text['header_title_1']) ? $landing_page_text['header_title_1'] : '' }}">
                </div>
                <div class="form-group">
                    <label for="header_title_2">{{ translate('Header Sub Title') }}</label>
                    <input type="text" id="header_title_2" name="header_title_2" class="form-control"
                        value="{{ isset($landing_page_text['header_title_2']) ? $landing_page_text['header_title_2'] : '' }}">
                </div>
                {{-- <div class="form-group">
                    <label for="header_title_3">{{ translate('messages.header_title_3') }}</label>
                    <input type="text" id="header_title_3" name="header_title_3" class="form-control"
                        value="{{ isset($landing_page_text['header_title_3']) ? $landing_page_text['header_title_3'] : '' }}">
                </div> --}}
                <div class="form-group">
                    <label for="about_title">{{ translate('messages.about_title') }}</label>
                    <input type="text" id="about_title" name="about_title" class="form-control"
                        value="{{ isset($landing_page_text['about_title']) ? $landing_page_text['about_title'] : '' }}">
                </div>
                {{-- <div class="form-group">
                    <label for="why_choose_us">{{ translate('messages.why_choose_us') }}</label>
                    <input type="text" id="why_choose_us" name="why_choose_us" class="form-control"
                        value="{{ isset($landing_page_text['why_choose_us']) ? $landing_page_text['why_choose_us'] : '' }}">
                </div> --}}
                <div class="form-group">
                    <label for="mobile_app_section_heading">{{ translate('messages.mobile_app_section_heading') }}</label>
                    <input type="text" id="mobile_app_section_heading" name="mobile_app_section_heading" class="form-control"
                    value="{{ isset($landing_page_text['mobile_app_section_heading']) ? $landing_page_text['mobile_app_section_heading'] : '' }}">

                </div>
                <div class="form-group">
                    <label for="mobile_app_section_text">{{ translate('messages.mobile_app_section_text') }}</label>
                    <input type="text" id="mobile_app_section_text" name="mobile_app_section_text" class="form-control"
                    value="{{ isset($landing_page_text['mobile_app_section_text']) ? $landing_page_text['mobile_app_section_text'] : '' }}">
                </div>
                <div class="form-group">
                    <label for="why_choose_us_title">{{ translate('messages.why_choose_us_title') }}</label>
                    <input type="text" id="why_choose_us_title" name="why_choose_us_title" class="form-control"
                        value="{{ isset($landing_page_text['why_choose_us_title']) ? $landing_page_text['why_choose_us_title'] : '' }}">
                </div>
                <div class="form-group">
                    <label for="module_section_title">{{ translate('messages.module_section_title') }}</label>
                    <input type="text" id="module_section_title" name="module_section_title" class="form-control"
                        value="{{ isset($landing_page_text['module_section_title']) ? $landing_page_text['module_section_title'] : '' }}">
                </div>
                <div class="form-group">
                    <label for="module_section_sub_title">{{ translate('messages.module_section_sub_title') }}</label>
                    <input type="text" id="module_section_sub_title" name="module_section_sub_title" class="form-control"
                        value="{{ isset($landing_page_text['module_section_sub_title']) ? $landing_page_text['module_section_sub_title'] : '' }}">
                </div>
                <div class="form-group">
                    <label for="refer_section_title">{{ translate('messages.refer_section_title') }}</label>
                    <input type="text" id="refer_section_title" name="refer_section_title" class="form-control"
                        value="{{ isset($landing_page_text['refer_section_title']) ? $landing_page_text['refer_section_title'] : '' }}">
                </div>
                <div class="form-group">
                    <label for="refer_section_sub_title">{{ translate('messages.refer_section_sub_title') }}</label>
                    <input type="text" id="refer_section_sub_title" name="refer_section_sub_title" class="form-control"
                        value="{{ isset($landing_page_text['refer_section_sub_title']) ? $landing_page_text['refer_section_sub_title'] : '' }}">
                </div>
                <div class="form-group">
                    <label for="refer_section_description">{{ translate('messages.refer_section_description') }}</label>
                    <textarea id="refer_section_description" name="refer_section_description" class="form-control" cols="30" rows="5">
                        {{ isset($landing_page_text['refer_section_description']) ? $landing_page_text['refer_section_description'] : '' }}
                    </textarea>
                </div>
                <div class="form-group">
                    <label for="joinus_section_title">{{ translate('messages.joinus_section_title') }}</label>
                    <input type="text" id="joinus_section_title" name="joinus_section_title" class="form-control"
                        value="{{ isset($landing_page_text['joinus_section_title']) ? $landing_page_text['joinus_section_title'] : '' }}">
                </div>
                <div class="form-group">
                    <label for="joinus_section_sub_title">{{ translate('messages.joinus_section_sub_title') }}</label>
                    <input type="text" id="joinus_section_sub_title" name="joinus_section_sub_title" class="form-control"
                        value="{{ isset($landing_page_text['joinus_section_sub_title']) ? $landing_page_text['joinus_section_sub_title'] : '' }}">
                </div>
                <div class="form-group">
                    <label for="download_app_section_title">{{ translate('messages.download_app_section_title') }}</label>
                    <input type="text" id="download_app_section_title" name="download_app_section_title" class="form-control"
                        value="{{ isset($landing_page_text['download_app_section_title']) ? $landing_page_text['download_app_section_title'] : '' }}">
                </div>
                <div class="form-group">
                    <label for="download_app_section_sub_title">{{ translate('messages.download_app_section_sub_title') }}</label>
                    <input type="text" id="download_app_section_sub_title" name="download_app_section_sub_title" class="form-control"
                        value="{{ isset($landing_page_text['download_app_section_sub_title']) ? $landing_page_text['download_app_section_sub_title'] : '' }}">
                </div>
                <div class="form-group">
                    <label for="testimonial_title">{{ translate('messages.testimonial_title') }}</label>
                    <input type="text" id="testimonial_title" name="testimonial_title" class="form-control"
                        value="{{ isset($landing_page_text['testimonial_title']) ? $landing_page_text['testimonial_title'] : '' }}">
                </div>
                <div class="form-group">
                    <label for="feature_section_title">{{ translate('messages.feature_section_title') }}</label>
                    <input type="text" id="feature_section_title" name="feature_section_title" class="form-control"
                        value="{{ isset($landing_page_text['feature_section_title']) ? $landing_page_text['feature_section_title'] : '' }}">
                </div>
                <div class="form-group">
                    <label for="feature_section_description">{{ translate('messages.feature_section_description') }}</label>
                    <textarea id="feature_section_description" name="feature_section_description" class="form-control" cols="30" rows="5">
                        {{ isset($landing_page_text['feature_section_description']) ? $landing_page_text['feature_section_description'] : '' }}
                    </textarea>
                </div>
                <div class="form-group">
                    <label for="newsletter_title">{{ translate('messages.newsletter_title') }}</label>
                    <textarea type="text" id="newsletter_title" name="newsletter_title"
                        class="form-control">{{ isset($landing_page_text['newsletter_title']) ? $landing_page_text['newsletter_title'] : '' }}</textarea>
                </div>
                <div class="form-group">
                    <label for="newsletter_sub_title">{{ translate('messages.newsletter_sub_title') }}</label>
                    <textarea type="text" id="newsletter_sub_title" name="newsletter_sub_title"
                        class="form-control">{{ isset($landing_page_text['newsletter_sub_title']) ? $landing_page_text['newsletter_sub_title'] : '' }}</textarea>
                </div>
                <div class="form-group">
                    <label for="contact_us_title">{{ translate('messages.contact_us_title') }}</label>
                    <textarea type="text" id="contact_us_title" name="contact_us_title"
                        class="form-control">{{ isset($landing_page_text['contact_us_title']) ? $landing_page_text['contact_us_title'] : '' }}</textarea>
                </div>
                <div class="form-group">
                    <label for="contact_us_sub_title">{{ translate('messages.contact_us_sub_title') }}</label>
                    <textarea type="text" id="contact_us_sub_title" name="contact_us_sub_title"
                        class="form-control">{{ isset($landing_page_text['contact_us_sub_title']) ? $landing_page_text['contact_us_sub_title'] : '' }}</textarea>
                </div>
                <div class="form-group">
                    <label for="footer_article">{{ translate('messages.footer_article') }}</label>
                    <textarea type="text" id="footer_article" name="footer_article"
                        class="form-control">{{ isset($landing_page_text['footer_article']) ? $landing_page_text['footer_article'] : '' }}</textarea>
                </div>
                <div class="btn--container justify-content-end">
                    <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                    <button type="submit" class="btn btn--primary">{{ translate('messages.submit') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('script_2')
<script>
    $('document').ready(function() {
        $('textarea').each(function() {
            $(this).val($(this).val().trim());
        });
    });
</script>
@endpush
