@extends('errors::minimal')

@section('title', translate('Too Many Requests'))
@section('code', '429')
@section('message', translate('Too Many Requests'))
