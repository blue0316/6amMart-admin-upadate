<table id="columnSearchDatatable"
        class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
        data-hs-datatables-options='{
        "order": [],
        "orderCellsTop": true,

        "entries": "#datatableEntries",
        "isResponsive": false,
        "isShowPaging": false,
        "pagination": "datatablePagination"
        }'>
    <thead class="thead-light">
        <tr>
            <th class="border-0">{{translate('sl')}}</th>
            <th class="border-0">{{translate('messages.title')}}</th>
            <th class="border-0">{{translate('messages.code')}}</th>
            <th class="border-0">{{translate('messages.module')}}</th>
            <th class="border-0">{{translate('messages.type')}}</th>
            <th class="border-0">{{translate('messages.total_uses')}}</th>
            <th class="border-0">{{translate('messages.min')}} {{translate('messages.purchase')}}</th>
            <th class="border-0">{{translate('messages.max')}} {{translate('messages.discount')}}</th>
            <th class="border-0">{{translate('messages.discount')}}</th>
            <th class="border-0">{{translate('messages.discount')}} {{translate('messages.type')}}</th>
            <th class="border-0">{{translate('messages.start')}} {{translate('messages.date')}}</th>
            <th class="border-0">{{translate('messages.expire')}} {{translate('messages.date')}}</th>
            <th class="border-0">{{translate('messages.status')}}</th>
            <th class="border-0 text-center">{{translate('messages.action')}}</th>
        </tr>
    </thead>

    <tbody id="set-rows">
    @foreach($coupons as $key=>$coupon)
        <tr>
            <td>{{$key+1}}</td>
            <td>
            <span class="d-block font-size-sm text-body">
            {{Str::limit($coupon['title'],15,'...')}}
            </span>
            </td>
            <td>{{$coupon['code']}}</td>
            <td>{{Str::limit($coupon->module->module_name, 15, '...')}}</td>
            <td>{{translate('messages.'.$coupon->coupon_type)}}</td>
            <td>{{$coupon->total_uses}}</td>
            <td>{{\App\CentralLogics\Helpers::format_currency($coupon['min_purchase'])}}</td>
            <td>{{\App\CentralLogics\Helpers::format_currency($coupon['max_discount'])}}</td>
            <td>{{$coupon['discount']}}</td>
            <td>{{$coupon['discount_type']}}</td>
            <td>{{$coupon['start_date']}}</td>
            <td>{{$coupon['expire_date']}}</td>
            <td>
                <label class="toggle-switch toggle-switch-sm" for="couponCheckbox{{$coupon->id}}">
                    <input type="checkbox" onclick="location.href='{{route('admin.coupon.status',[$coupon['id'],$coupon->status?0:1])}}'" class="toggle-switch-input" id="couponCheckbox{{$coupon->id}}" {{$coupon->status?'checked':''}}>
                    <span class="toggle-switch-label">
                        <span class="toggle-switch-indicator"></span>
                    </span>
                </label>
            </td>
            <td>
                <div class="btn--container justify-content-center">

                    <a class="btn action-btn btn--primary btn-outline-primary" href="{{route('admin.coupon.update',[$coupon['id']])}}"title="{{translate('messages.edit')}} {{translate('messages.coupon')}}"><i class="tio-edit"></i>
                    </a>
                    <a class="btn action-btn btn--danger btn-outline-danger" href="javascript:" onclick="form_alert('coupon-{{$coupon['id']}}','{{ translate('messages.Want to delete this coupon ?') }}')" title="{{translate('messages.delete')}} {{translate('messages.coupon')}}"><i class="tio-delete-outlined"></i>
                    </a>
                    <form action="{{route('admin.coupon.delete',[$coupon['id']])}}"
                    method="post" id="coupon-{{$coupon['id']}}">
                        @csrf @method('delete')
                    </form>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<hr>
<table>
    <tfoot>

    </tfoot>
</table>
