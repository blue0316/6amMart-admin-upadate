<ul class="nav nav-tabs page-header-tabs flex-wrap __nav-tabs-menu">
    <li class="nav-item">
        <a class="nav-link  {{ Request::is('admin/business-settings/landing-page-settings/index') ? 'active' : '' }}"
            href="{{ route('admin.business-settings.landing-page-settings', 'index') }}">{{ translate('messages.text') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::is('admin/business-settings/landing-page-settings/links') ? 'active' : '' }}"
            href="{{ route('admin.business-settings.landing-page-settings', 'links') }}"
            aria-disabled="true">{{ translate('messages.button_links') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::is('admin/business-settings/landing-page-settings/speciality') ? 'active' : '' }}"
            href="{{ route('admin.business-settings.landing-page-settings', 'speciality') }}"
            aria-disabled="true">{{ translate('messages.speciality') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::is('admin/business-settings/landing-page-settings/joinas') ? 'active' : '' }}"
            href="{{ route('admin.business-settings.landing-page-settings', 'joinas') }}"
            aria-disabled="true">{{ translate('messages.join as') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::is('admin/business-settings/landing-page-settings/download-section') ? 'active' : '' }}"
            href="{{ route('admin.business-settings.landing-page-settings', 'download-section') }}"
            aria-disabled="true">{{ translate('messages.download App Section') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::is('admin/business-settings/landing-page-settings/promotion-banner') ? 'active' : '' }}"
            href="{{ route('admin.business-settings.landing-page-settings', 'promotion-banner') }}"
            aria-disabled="true">{{ translate('messages.promotion Banner') }}</a>
    </li>
    {{-- <li class="nav-item">
        <a class="nav-link {{ Request::is('admin/business-settings/landing-page-settings/module-section') ? 'active' : '' }}"
            href="{{ route('admin.business-settings.landing-page-settings', 'module-section') }}"
            aria-disabled="true">{{ translate('messages.module Section') }}</a>
    </li> --}}
    <li class="nav-item">
        <a class="nav-link {{ Request::is('admin/business-settings/landing-page-settings/testimonial') ? 'active' : '' }}"
            href="{{ route('admin.business-settings.landing-page-settings', 'testimonial') }}"
            aria-disabled="true">{{ translate('messages.testimonial') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::is('admin/business-settings/landing-page-settings/feature') ? 'active' : '' }}"
            href="{{ route('admin.business-settings.landing-page-settings', 'feature') }}"
            aria-disabled="true">{{ translate('messages.feature') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::is('admin/business-settings/landing-page-settings/image') ? 'active' : '' }}"
            href="{{ route('admin.business-settings.landing-page-settings', 'image') }}"
            aria-disabled="true">{{ translate('messages.image') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::is('admin/business-settings/landing-page-settings/background-change') ? 'active' : '' }}"
            href="{{ route('admin.business-settings.landing-page-settings', 'background-change') }}"
            aria-disabled="true">{{ translate('messages.colors') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::is('admin/business-settings/landing-page-settings/web-app') ? 'active' : '' }}"
            href="{{ route('admin.business-settings.landing-page-settings', 'web-app') }}"
            aria-disabled="true">{{ translate('messages.web_app') }}</a>
    </li>
</ul>
