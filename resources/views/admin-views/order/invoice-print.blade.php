@extends('layouts.admin.print')

@section('title','')

@push('css_or_js')
    
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style type="text/css" media="print">
        @page {
            size: auto;   /* auto is the initial value */
            margin: 0;  /* this affects the margin in the printer settings */
        }

    </style>
@endpush

@section('content')

@include('admin-views.order.partials._invoice')

@endsection

