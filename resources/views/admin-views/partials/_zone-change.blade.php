<h4 class="m-0 mr-1">
    {{-- <i class="tio-chart-bar-4"></i>
    {{ translate('messages.dashboard_order_statistics') }} --}}
    @php($params = session('dash_params'))
    @if ($params['zone_id'] != 'all')
        @php($zone_name = \App\Models\Zone::where('id', $params['zone_id'])->first()->name)
    @else
        @php($zone_name = translate('messages.all'))
    @endif
    {{--<label class="badge badge-soft-primary m-0">{{ translate('messages.zone') }} : {{ $zone_name }}</label>--}}
</h4>
