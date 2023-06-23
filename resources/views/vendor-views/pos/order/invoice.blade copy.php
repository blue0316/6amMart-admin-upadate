<div class="content container-fluid invoice-page">
    <div class="ff-emoji" id="printableArea">
        <div class="print--invoice">
            @if($order->store)
            <div class="text-center pt-4 mb-3">
                <img class="invoice-logo" src="{{asset('/public/assets/admin/img/invoice-logo.png')}}" alt="">
                <div class="top-info">
                    <h2 class="store-name">{{$order->store->name}}</h2>
                    <div>
                        {{$order->store->address}}
                    </div>
                    <div class="mt-1">
                        {{translate('messages.phone')}} : {{$order->store->phone}}
                    </div>
                </div>
            </div>
            @endif
            <div class="top-info">
                <img src="{{asset('/public/assets/admin/img/invoice-star.png')}}" alt="" class="w-100">
                <div class="text-uppercase text-center">{{translate('messages.cash_receipt')}}</div>
                <img src="{{asset('/public/assets/admin/img/invoice-star.png')}}" alt="" class="w-100">
            </div>
            <div class="order-info-id text-center">
                <h5>{{translate('order_id')}} : {{$order['id']}}</h5>
                <div>
                    {{date('d/M/Y '.config('timeformat'),strtotime($order['created_at']))}}
                </div>
            </div>
            <div class="order-info-details">
                <div class="row mt-3">
                    @if ($order->order_type=='parcel')
                    <div class="col-12">
                        @php($address=json_decode($order->delivery_address,true))
                        <h5><u>{{translate('messages.sender')}} {{translate('messages.info')}}</u></h5>
                        <div>
                            {{translate('messages.sender')}} {{translate('messages.name')}} : {{isset($address)?$address['contact_person_name']:$order->address['f_name'].' '.$order->customer['l_name']}}
                        </div>
                        <div>
                            {{translate('messages.phone')}} : {{isset($address)?$address['contact_person_number']:$order->customer['phone']}}
                        </div>
                        <div class="text-break">
                            {{translate('messages.address')}} : {{isset($address)?$address['address']:''}}
                        </div>
                        @php($address=$order->receiver_details)
                        <h5><u>{{translate('messages.receiver')}} {{translate('messages.info')}}</u></h5>
                        <div>
                            {{translate('messages.receiver')}} {{translate('messages.name')}} : {{isset($address)?$address['contact_person_name']:$order->address['f_name'].' '.$order->customer['l_name']}}
                        </div>
                        <div>
                            {{translate('messages.phone')}} : {{isset($address)?$address['contact_person_number']:$order->customer['phone']}}
                        </div>
                        <div class="text-break">
                            {{translate('messages.address')}} : {{isset($address)?$address['address']:''}}
                        </div>
                    </div>
                    @else
                    <div class="col-12">
                        @php($address = json_decode($order->delivery_address, true))
                        @if(!empty($address))
                        <h5>
                            <span>{{translate('messages.contact_name')}} :</span> {{isset($address['contact_person_name'])?$address['contact_person_name']:''}}
                        </h5>
                        <h5>
                            <span>{{translate('messages.phone')}} :</span> {{isset($address['contact_person_number'])? $address['contact_person_number'] : ''}}
                        </h5>
                        {{--<h5>
                            <span>{{translate('messages.Floor')}} :</span> {{isset($address['floor'])? $address['floor'] : ''}}
                        </h5>
                        <h5>
                            <span>{{translate('messages.Road')}} :</span> {{isset($address['road'])? $address['road'] : ''}}
                        </h5>
                        <h5>
                            <span>{{translate('messages.House')}} :</span> {{isset($address['house'])? $address['house'] : ''}}
                        </h5>--}}
                        @endif
                        <h5 class="text-break">
                            <span>{{translate('messages.address')}} :</span> {{isset($order->delivery_address)?json_decode($order->delivery_address, true)['address']:''}}
                        </h5>
                    </div>
                    @endif
                </div>
                <table class="table invoice--table text-black mt-3">
                    <thead class="border-0">
                    <tr class="border-0">
                        <th>{{translate('messages.desc')}}</th>
                        <th class="w-10p"></th>
                        <th>{{translate('messages.price')}}</th>
                    </tr>
                    </thead>

                    <tbody>
                    @if ($order->order_type == 'parcel')
                        <tr>
                            <td>{{translate('messages.delivery_charge')}}</td>
                            <td class="text-center">1</td>
                            <td>{{\App\CentralLogics\Helpers::format_currency($order->delivery_charge)}}</td>
                        </tr>
                    @else
                        @php($sub_total=0)
                        @php($total_tax=0)
                        @php($total_dis_on_pro=0)
                        @php($add_ons_cost=0)
                        @foreach($order->details as $detail)

                            @php($item=json_decode($detail->item_details, true))
                                <tr>
                                    <td class="text-break">
                                        {{$item['name']}} <br>
                                        @if(count(json_decode($detail['variation'],true))>0)
                                            <strong><u>Variation : </u></strong>
                                            @foreach(json_decode($detail['variation'],true)[0] as $key1 =>$variation)
                                                @if ($key1 != 'stock')
                                                    <div class="font-size-sm text-body">
                                                        <span>{{$key1}} :  </span>
                                                        <span class="font-weight-bold">{{$key1=='price'?\App\CentralLogics\Helpers::format_currency($variation):$variation}}</span>
                                                    </div>
                                                @endif

                                            @endforeach
                                        @endif
                                    <div class="addons">
                                        @foreach(json_decode($detail['add_ons'],true) as $key2 =>$addon)
                                            @if($key2==0)<strong><u>{{translate('messages.addons')}} : </u></strong>@endif
                                            <div>
                                                <span class="text-break">{{$addon['name']}} :  </span>
                                                <span class="font-weight-bold">
                                                    {{$addon['quantity']}} x {{\App\CentralLogics\Helpers::format_currency($addon['price'])}}
                                                </span>
                                            </div>
                                            @php($add_ons_cost+=$addon['price']*$addon['quantity'])
                                        @endforeach
                                    </div>
                                        @if(count(json_decode($detail['variation'],true))<=0)
                                        <div class="price">
                                            {{\App\CentralLogics\Helpers::format_currency($detail->price)}}
                                        </div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{$detail['quantity']}}
                                    </td>
                                    <td class="w-28p">
                                        @php($amount=($detail['price'])*$detail['quantity'])
                                        {{\App\CentralLogics\Helpers::format_currency($amount)}}
                                    </td>
                                </tr>
                                @php($sub_total+=$amount)
                                @php($total_tax+=$detail['tax_amount']*$detail['quantity'])

                            {{--@elseif($detail->campaign)
                                <tr>
                                    <td class="">
                                        {{$detail['quantity']}}
                                    </td>
                                    <td class="text-break">
                                        {{$detail->campaign['title']}} <br>
                                        @if(count(json_decode($detail['variation'],true))>0)
                                            <strong><u>Variation : </u></strong>
                                            @foreach(json_decode($detail['variation'],true)[0] as $key1 =>$variation)
                                                <div class="font-size-sm text-body">
                                                    <span>{{$key1}} :  </span>
                                                    <span class="font-weight-bold">{{$key1=='price'?\App\CentralLogics\Helpers::format_currency($variation):$variation}}</span>
                                                </div>
                                            @endforeach
                                        @else
                                        <div class="font-size-sm text-body">
                                            <span>{{translate('messages.price')}} :  </span>
                                            <span class="font-weight-bold">{{\App\CentralLogics\Helpers::format_currency($detail->price)}}</span>
                                        </div>
                                        @endif

                                        @foreach(json_decode($detail['add_ons'],true) as $key2 =>$addon)
                                            @if($key2==0)<strong><u>{{translate('messages.price')}} : </u></strong>@endif
                                            <div class="font-size-sm text-body">
                                                <span class="text-break">{{$addon['name']}} :  </span>
                                                <span class="font-weight-bold">
                                                    {{$addon['quantity']}} x {{\App\CentralLogics\Helpers::format_currency($addon['price'])}}
                                                </span>
                                            </div>
                                            @php($add_ons_cost+=$addon['price']*$addon['quantity'])
                                        @endforeach
                                    </td>
                                    <td class="w-28p">
                                        @php($amount=($detail['price'])*$detail['quantity'])
                                        {{\App\CentralLogics\Helpers::format_currency($amount)}}
                                    </td>
                                </tr>
                                @php($sub_total+=$amount)
                                @php($total_tax+=$detail['tax_amount']*$detail['quantity'])
                            @endif--}}
                        @endforeach
                    @endif

                    </tbody>
                </table>
                <div class="checkout--info">
                    <dl class="row text-right">
                        @if ($order->order_type !='parcel')
                            <dt class="col-6">{{translate('messages.subtotal')}}:</dt>
                            <dd class="col-6">
                                {{\App\CentralLogics\Helpers::format_currency($sub_total+$add_ons_cost)}}</dd>
                            <dt class="col-6">{{translate('messages.discount')}}:</dt>
                            <dd class="col-6">
                                - {{\App\CentralLogics\Helpers::format_currency($order['store_discount_amount'])}}</dd>
                            <dt class="col-6">{{translate('messages.coupon_discount')}}:</dt>
                            <dd class="col-6">
                                - {{\App\CentralLogics\Helpers::format_currency($order['coupon_discount_amount'])}}</dd>
                            <dt class="col-6">{{translate('messages.vat/tax')}}:</dt>
                            <dd class="col-6">+ {{\App\CentralLogics\Helpers::format_currency($order['total_tax_amount'])}}</dd>
                            <dt class="col-6">{{ translate('messages.delivery_man_tips') }}:</dt>
                            <dd class="col-6">
                                @php($delivery_man_tips = $order['dm_tips'])
                                + {{ \App\CentralLogics\Helpers::format_currency($delivery_man_tips) }}
                            </dd>
                            <dt class="col-6">{{translate('messages.delivery_charge')}}:</dt>
                            <dd class="col-6">
                                @php($del_c=$order['delivery_charge'])
                                {{\App\CentralLogics\Helpers::format_currency($del_c)}}
                            </dd>
                        @else
                        <dt class="col-6">{{ translate('messages.delivery_man_tips') }}:</dt>
                        <dd class="col-6">
                            @php($delivery_man_tips = $order['dm_tips'])
                            + {{ \App\CentralLogics\Helpers::format_currency($delivery_man_tips) }}
                        </dd>
                        @endif
                        <dt class="col-6 total">{{translate('messages.total')}}:</dt>
                        <dd class="col-6 total">{{\App\CentralLogics\Helpers::format_currency($order->order_amount)}}</dd>
                    </dl>
                    @if ($order->payment_method == 'cash')
                        <div class="d-flex flex-row justify-content-between border-top">
                            <span>{{translate('messages.Paid by')}}: {{translate('messages.'.$order->payment_method)}}</span>	<span>{{translate('messages.amount')}}: {{$order->adjusment + $order->order_amount}}</span>	<span>{{translate('messages.change')}}: {{$order->adjusment}}</span>
                        </div>
                    @endif
                </div>
            </div>
            <div class="top-info mt-2">
                <img src="{{asset('/public/assets/admin/img/invoice-star.png')}}" alt="" class="w-100">
                <div class="text-uppercase text-center">{{translate('THANK YOU')}}</div>
                <img src="{{asset('/public/assets/admin/img/invoice-star.png')}}" alt="" class="w-100">
                <div class="copyright">
                    &copy; {{\App\Models\BusinessSetting::where(['key'=>'business_name'])->first()->value}}. <span
                    class="d-none d-sm-inline-block">{{\App\Models\BusinessSetting::where(['key'=>'footer_text'])->first()->value}}</span>
                </div>
            </div>
        </div>
    </div>
</div>
