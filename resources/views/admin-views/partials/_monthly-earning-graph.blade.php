<div class="card-body">
    <div class="row mb-4">
        <div class="col-sm mb-2 mb-sm-0">
            @php($params=session('dash_params'))
            @if($params['zone_id']!='all')
                @php($zone_name=\App\Models\Zone::where('id',$params['zone_id'])->first()->name)
            @else
                @php($zone_name = translate('messages.all'))
            @endif

            <div class="d-flex flex-wrap justify-content-center align-items-center">
                <span class="h5 m-0 mr-3 fz--11 d-flex align-items-center mb-2 mb-md-0">
                    <span class="legend-indicator chart-bg-2"></span>
                    {{translate('messages.total_sell')}} : {{\App\CentralLogics\Helpers::format_currency(array_sum($total_sell))}}
                </span>
                <span class="h5 m-0 mr-3 fz--11 d-flex align-items-center mb-2 mb-md-0">
                    <span class="legend-indicator chart-bg-3"></span>
                    {{translate('messages.admin_commission')}} : {{\App\CentralLogics\Helpers::format_currency(array_sum($commission))}}
                </span>
                <span class="h5 m-0 fz--11 d-flex align-items-center mb-2 mb-md-0">
                    <span class="legend-indicator chart-bg-1"></span>
                    {{translate('messages.delivery_commission')}} : {{\App\CentralLogics\Helpers::format_currency(array_sum($delivery_commission))}}
                </span>
            </div>
        </div>
        {{--<div class="col-12 text-right mt--xl--10">
          <span class="badge badge-soft-primary">{{translate('messages.zone')}} : {{$zone_name}}</span>
        </div>--}}
    </div>
    <!-- End Row -->

    <!-- Bar Chart -->
    <div class="d-flex align-items-center">
      <div class="chart--extension">
        {{ \App\CentralLogics\Helpers::currency_symbol() }}({{translate('messages.currency')}})
      </div>
      <div class="chartjs-custom w-75 flex-grow-1">
          <canvas id="updatingData" class="initial--26"
              data-hs-chartjs-options='{
                "type": "bar",
                "data": {
                  "labels": ["Jan","Feb","Mar","April","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
                  "datasets": [{
                    "data": [{{$total_sell[1]}},{{$total_sell[2]}},{{$total_sell[3]}},{{$total_sell[4]}},{{$total_sell[5]}},{{$total_sell[6]}},{{$total_sell[7]}},{{$total_sell[8]}},{{$total_sell[9]}},{{$total_sell[10]}},{{$total_sell[11]}},{{$total_sell[12]}}],
                    "backgroundColor": "#00AA96",
                    "hoverBackgroundColor": "#00AA96",
                    "borderColor": "#00AA96"
                  },
                  {
                    "data": [{{$commission[1]}},{{$commission[2]}},{{$commission[3]}},{{$commission[4]}},{{$commission[5]}},{{$commission[6]}},{{$commission[7]}},{{$commission[8]}},{{$commission[9]}},{{$commission[10]}},{{$commission[11]}},{{$commission[12]}}],
                    "backgroundColor": "#b9e0e0",
                    "hoverBackgroundColor": "#b9e0e0",
                    "borderColor": "#b9e0e0"
                  },
                  {
                    "data": [{{$delivery_commission[1]}},{{$delivery_commission[2]}},{{$delivery_commission[3]}},{{$delivery_commission[4]}},{{$delivery_commission[5]}},{{$delivery_commission[6]}},{{$delivery_commission[7]}},{{$delivery_commission[8]}},{{$delivery_commission[9]}},{{$delivery_commission[10]}},{{$delivery_commission[11]}},{{$delivery_commission[12]}}],
                    "backgroundColor": "#005555",
                    "hoverBackgroundColor": "#005555",
                    "borderColor": "#005555"
                  }
                  ]
                },
                "options": {
                  "scales": {
                    "yAxes": [{
                      "gridLines": {
                        "color": "#e7eaf3",
                        "drawBorder": false,
                        "zeroLineColor": "#e7eaf3"
                      },
                      "ticks": {
                        "beginAtZero": true,
                        "stepSize": {{ceil((array_sum($total_sell)/10000))*2000}},
                        "fontSize": 12,
                        "fontColor": "#97a4af",
                        "fontFamily": "Open Sans, sans-serif",
                        "padding": 5,
                        "postfix": " {{\App\CentralLogics\Helpers::currency_symbol()}}"
                      }
                    }],
                    "xAxes": [{
                      "gridLines": {
                        "display": false,
                        "drawBorder": false
                      },
                      "ticks": {
                        "fontSize": 12,
                        "fontColor": "#97a4af",
                        "fontFamily": "Open Sans, sans-serif",
                        "padding": 5
                      },
                      "categoryPercentage": 0.3,
                      "maxBarThickness": "10"
                    }]
                  },
                  "cornerRadius": 5,
                  "tooltips": {
                    "prefix": " ",
                    "hasIndicator": true,
                    "mode": "index",
                    "intersect": false
                  },
                  "hover": {
                    "mode": "nearest",
                    "intersect": true
                  }
                }
              }'>
          </canvas>
      </div>
    </div>
    <!-- End Bar Chart -->
</div>

<script>
    // INITIALIZATION OF CHARTJS
    // =======================================================
    Chart.plugins.unregister(ChartDataLabels);

    $('.js-chart').each(function () {
        $.HSCore.components.HSChartJS.init($(this));
    });

    var updatingChart = $.HSCore.components.HSChartJS.init($('#updatingData'));
</script>
