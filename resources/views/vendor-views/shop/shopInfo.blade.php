@extends('layouts.vendor.app')
@section('title',translate('messages.store_view'))
@push('css_or_js')
    <!-- Custom styles for this page -->
@endpush

@section('content')
<div class="content container-fluid"> 
    <div class="page-header">
        <div class="d-flex flex-wrap justify-content-between">
            <h2 class="page-header-title text-capitalize my-2">
                <img class="w--26" src="{{asset('/public/assets/admin/img/store.png')}}" alt="public">
                <span>
                    {{translate('messages.my_store_info')}}
                </span>
            </h2>
            <div class="my-2">
                <a class="btn btn--primary" href="{{route('vendor.shop.edit')}}"><i class="tio-edit"></i>{{translate('messages.edit_store_information')}}</a>
            </div>
        </div>
    </div>
    <div class="card border-0">
        <div class="card-body p-0">
            @if($shop->cover_photo)
            <div>
                <img class="my-restaurant-img" src="{{asset('storage/app/public/store/cover/'.$shop->cover_photo)}}" onerror="this.src='{{asset('public/assets/admin/img/900x400/img1.jpg')}}'">
            </div>
            @endif
            <div class="my-resturant--card">
                
                @if($shop->image=='def.png')
                <div class="my-resturant--avatar">
                    <img class="border"
                    src="{{asset('public/assets/back-end')}}/img/shop.png"
                    onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'" alt="User Pic">
                </div>
                @else
                    <div class="my-resturant--avatar">
                        <img src="{{asset('storage/app/public/store/'.$shop->logo)}}" class="border" onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'" alt="">
                    </div>
                @endif

                <div class="my-resturant--content">
                    <span class="d-block mb-1 pb-1">
                        <strong> {{translate('messages.name')}} :</strong>{{$shop->name}}
                    </span>
                    <span class="d-block mb-1 pb-1">
                        <strong>{{translate('messages.phone')}} :</strong> <a href="tel:{{$shop->phone}}">{{$shop->phone}}</a>
                    </span>
                    <span class="d-block mb-1 pb-1">
                        <strong>{{translate('messages.address')}} : </strong> {{$shop->address}}
                    </span>
                    <span class="d-block mb-1 pb-1">
                        <strong>{{translate('messages.admin_commission')}} : </strong> {{(isset($shop->comission)? $shop->comission:\App\Models\BusinessSetting::where('key','admin_commission')->first()->value)}}%
                    </span>
                    <span class="d-block mb-1 pb-1">
                        <strong>{{translate('messages.vat/tax')}} : </strong> {{$shop->tax}}%</span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
