@extends('errors::minimal')

@section('title', translate('Service Unavailable'))
@section('code', '503')
@section('message', translate($exception->getMessage() ?: 'Service Unavailable'))
