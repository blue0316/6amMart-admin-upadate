@extends('layouts.admin.app')

@section('title',translate('messages.Refund Policy'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
                <h1 class="d-flex flex-wrap justify-content-between page-header-title">
                    <span class="page-header-icon">
                        <img src="{{asset('public/assets/admin/img/privacy-policy.png')}}" class="w--26" alt="">
                        {{translate('messages.Refund Policy')}}
                    </span>
                    @php($config=\App\CentralLogics\Helpers::get_business_settings('refund'))
                    <form action="{{route('admin.business-settings.refund')}}" method="post" id="tnc-form">
                        @csrf
                    <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                        <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('messages.on') }}</span>
                        <span class="mr-2 switch--custom-label-text off text-uppercase">{{ translate('messages.off') }}</span>
                        <input type="checkbox" name="status" value="1" class="toggle-switch-input" {{$config?($config['status']==1?'checked':''):''}}>
                        <span class="toggle-switch-label text">
                            <span class="toggle-switch-indicator"></span>
                        </span>
                    </label>
                </h1>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                    <div class="form-group">
                        <textarea class="ckeditor form-control" name="refund">{!! $config['value'] !!}</textarea>
                    </div>

                    <div class="btn--container justify-content-end">
                        {{-- <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button> --}}
                        <button type="submit" class="btn btn--primary">{{translate('messages.submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
<script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.ckeditor').ckeditor();
    });

    function checkedFunc() {
        $('.switch--custom-label .toggle-switch-input').each( function() {
            if(this.checked) {
                $(this).closest('.switch--custom-label').addClass('checked')
            }else {
                $(this).closest('.switch--custom-label').removeClass('checked')
            }
        })
    }
    checkedFunc()
    $('.switch--custom-label .toggle-switch-input').on('change', checkedFunc)
</script>
@endpush
