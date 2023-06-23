<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required Meta Tags Always Come First -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Title -->
    <title>{{translate('messages.error')}} 404 | {{\App\Models\BusinessSetting::where(['key'=>'business_name'])->first()->value??'Stack Food'}}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon.ico">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&amp;display=swap" rel="stylesheet">

    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/vendor/icon-set/style.css">

    <!-- CSS Front Template -->
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/theme.minc619.css?v=1.0')}}">
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/style.css')}}">
</head>

<body>

<!-- Content -->
<div class="container">
    <div class="footer-height-offset d-flex justify-content-center align-items-center flex-column">
        <div class="row align-items-sm-center w-100">
            <div class="col-sm-6">
                <div class="text-center text-sm-right mr-sm-4 mb-5 mb-sm-0">
                    <img class="w-60 w-sm-100 mx-auto mw-15rem"
                         src="{{asset('public/assets/admin')}}/svg/illustrations/think.svg" alt="Image Description">
                </div>
            </div>

            <div class="col-sm-6 col-md-4 text-center text-sm-left">
                <h1 class="display-1 mb-0">404</h1>
                <p class="lead">{{translate('messages.404_warning_message')}}.</p>
                @if(auth('vendor')->check())
                    <a class="btn btn-primary" href="{{route('vendor.dashboard')}}">{{translate('messages.dashboard')}}</a>
                @else
                    <a class="btn btn-primary" href="{{route('admin.dashboard')}}">{{translate('messages.dashboard')}}</a>
                @endif
            </div>
        </div>
        <!-- End Row -->
    </div>
</div>
<!-- End Content -->

<!-- Footer -->
<div class="footer text-center">
    <ul class="list-inline list-separator">
        <li class="list-inline-item">
            <a class="list-separator-link" target="_blank" href="{{route('contact-us')}}">{{\App\Models\BusinessSetting::where(['key'=>'business_name'])->first()->value??'Stack Food'}} {{translate('messages.support')}}</a>
        </li>
    </ul>
</div>
<!-- End Footer -->


<!-- JS Front -->
<script src="{{asset('public/assets/admin')}}/js/theme.min.js"></script>
</body>

</html>
