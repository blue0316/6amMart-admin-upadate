<div class="d-flex flex-row cart--table-scroll">
    <table class="table table-bordered">
        <thead class="text-muted thead-light">
            <tr class="text-center">
                <th class="border-bottom-0" scope="col">{{ translate('messages.item') }}</th>
                <th class="border-bottom-0" scope="col">{{ translate('messages.qty') }}</th>
                <th class="border-bottom-0" scope="col">{{ translate('messages.price') }}</th>
                <th class="border-bottom-0" scope="col">{{ translate('messages.delete') }}</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $subtotal = 0;
            $addon_price = 0;
            $tax = \App\CentralLogics\Helpers::get_store_data()->tax;
            $discount = 0;
            $discount_type = 'amount';
            $discount_on_product = 0;
            $variation_price = 0;
            ?>
            @if (session()->has('cart') && count(session()->get('cart')) > 0)
                <?php
                $cart = session()->get('cart');
                if (isset($cart['tax'])) {
                    $tax = $cart['tax'];
                }
                if (isset($cart['discount'])) {
                    $discount = $cart['discount'];
                    $discount_type = $cart['discount_type'];
                }
                ?>
                @foreach (session()->get('cart') as $key => $cartItem)
                    @if (is_array($cartItem))
                        <?php
                        $variation_price += isset($cartItem['variation_price']) ? $cartItem['variation_price'] : 0;
                        $product_subtotal = $cartItem['price'] * $cartItem['quantity'];
                        $discount_on_product += $cartItem['discount'] * $cartItem['quantity'];
                        $subtotal += $product_subtotal;
                        $addon_price += $cartItem['addon_price'];
                        ?>
                        <tr>
                            <td class="media align-items-center cursor-pointer"
                                onclick="quickViewCartItem({{ $cartItem['id'] }}, {{ $key }})">
                                <img class="avatar avatar-sm mr-1"
                                    src="{{ asset('storage/app/public/product') }}/{{ $cartItem['image'] }}"
                                    onerror="this.src='{{ asset('public/assets/admin/img/100x100/2.png') }}'"
                                    alt="{{ $cartItem['name'] }} image">
                                <div class="media-body">
                                    <h5 class="text-hover-primary mb-0">{{ Str::limit($cartItem['name'], 10) }}</h5>
                                    <small>{{ Str::limit($cartItem['variant'], 20) }}</small>
                                </div>
                            </td>
                            <td class="text-center middle-align">
                                <input type="number" data-key="{{ $key }}"
                                    class="amount--input form-control text-center" value="{{ $cartItem['quantity'] }}"
                                    min="1" onkeyup="updateQuantity(event)">
                            </td>
                            <td class="text-center px-0 py-1">
                                <div class="btn">
                                    {{ \App\CentralLogics\Helpers::format_currency($product_subtotal) }}
                                </div> <!-- price-wrap .// -->
                            </td>
                            <td class="align-items-center text-center">
                                <a href="javascript:removeFromCart({{ $key }})"
                                    class="btn btn-sm btn-outline-danger"> <i class="tio-delete-outlined"></i></a>
                            </td>
                        </tr>
                    @endif
                @endforeach
            @endif
        </tbody>
    </table>
</div>

<?php
$add = false;
if (session()->has('address') && count(session()->get('address')) > 0) {
    $add = true;
    $delivery_fee = session()->get('address')['delivery_fee'];
} else {
    $delivery_fee = 0;
}
$total = $subtotal + $addon_price;
$discount_amount = $discount_type == 'percent' && $discount > 0 ? (($total - $discount_on_product) * $discount) / 100 : $discount;
$total -= $discount_amount + $discount_on_product;
$tax_included = \App\Models\BusinessSetting::where(['key'=>'tax_included'])->first() ?  \App\Models\BusinessSetting::where(['key'=>'tax_included'])->first()->value : 0;
$total_tax_amount = $tax > 0 ? ($total * $tax) / 100 : 0;

if ($tax_included ==  1){
    $total_tax_amount=0;
}
$total = $total + $delivery_fee;
if (isset($cart['paid'])) {
    $paid = $cart['paid'];
    $change = $total + $total_tax_amount - $paid;
} else {
    $paid = $total + $total_tax_amount;
    $change = 0;
}
?>
<form action="{{ route('vendor.pos.order') }}" id='order_place' method="post">
    @csrf
    <input type="hidden" name="user_id" id="customer_id">
    <div class="box p-3">
        <dl class="row">

            <dt class="col-6 font-regular">{{ translate('messages.addon') }}:</dt>
            <dd class="col-6 text-right">{{ \App\CentralLogics\Helpers::format_currency($addon_price) }}</dd>

            <dt class="col-6 font-regular">{{ translate('messages.subtotal') }}
                @if ($tax_included ==  1)
                ({{ translate('messages.TAX_Included') }})
                @endif
                :</dt>
            <dd class="col-6 text-right">{{ \App\CentralLogics\Helpers::format_currency($subtotal + $addon_price) }}</dd>


            <dt class="col-6 font-regular">{{ translate('messages.discount') }} :</dt>
            <dd class="col-6 text-right">-
                {{ \App\CentralLogics\Helpers::format_currency(round($discount_on_product, 2)) }}</dd>
            <dt class="col-6 font-regular">{{ translate('messages.delivery_fee') }} :</dt>
            <dd class="col-6 text-right" id="delivery_price">
                {{ \App\CentralLogics\Helpers::format_currency($delivery_fee, 2) }}</dd>

            <dt class="col-6 font-regular">{{ translate('messages.extra_discount') }} :</dt>
            <dd class="col-6 text-right"><button class="btn btn-sm" type="button" data-toggle="modal"
                    data-target="#add-discount"><i class="tio-edit"></i></button>-
                {{ \App\CentralLogics\Helpers::format_currency(round($discount_amount, 2)) }}</dd>
                @if ($tax_included !=  1)
            <dt class="col-6 font-regular">{{ translate('messages.tax') }} : </dt>
            <dd class="col-6 text-right"><button class="btn btn-sm" type="button" data-toggle="modal"
                    data-target="#add-tax"><i
                        class="tio-edit"></i></button>{{ \App\CentralLogics\Helpers::format_currency(round($total_tax_amount, 2)) }}
            </dd>
            @endif
            <dd class="col-12">
                <hr class="m-0">
            </dd>
            <dt class="col-6 font-regular">{{ translate('Total') }}: </dt>
            <dd class="col-6 text-right h4 b">
                {{ \App\CentralLogics\Helpers::format_currency(round($total + $total_tax_amount, 2)) }} </dd>
        </dl>
        <div class="pos--payment-options mt-3 mb-3">
            <h5 class="mb-3">{{ translate($add ? 'messages.Payment Method' : 'Paid by') }}</h5>
            <ul>
                @if ($add)
                    @php($cod = \App\CentralLogics\Helpers::get_business_settings('cash_on_delivery'))
                    @if ($cod['status'])
                        <li>
                            <label>
                                <input type="radio" name="type" value="cash_on_delivery" hidden checked>
                                <span>{{ translate('Cash On Delivery') }}</span>
                            </label>
                        </li>
                    @endif
                @else
                    <li id="payment_cash">
                        <label>
                            <input type="radio" name="type" value="cash" hidden="" checked>
                            <span>{{ translate('messages.Cash') }}</span>
                        </label>
                    </li>
                    <li id="payment_card">
                        <label>
                            <input type="radio" name="type" value="card" hidden="">
                            <span>{{ translate('messages.Card') }}</span>
                        </label>
                    </li>
                @endif

            </ul>
        </div>
        @if (!$add)
            <div id="paid_section">
                <div class="mt-4 d-flex justify-content-between pos--payable-amount">
                    <label class="m-0">{{ translate('Paid Amount') }} :</label>
                    <div>
                        <span data-toggle="modal" data-target="#insertPayableAmount" class="text-body"><i
                                class="tio-edit"></i></span>
                        <span>{{ \App\CentralLogics\Helpers::format_currency($paid) }}</span>
                        <input type="hidden" name="amount" value="{{ $paid }}">
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-between pos--payable-amount">
                    <label class="m-0">{{ translate('Change Amount') }} :</label>
                    <div>
                        <span>{{ \App\CentralLogics\Helpers::format_currency($change) }}</span>
                        <input type="hidden" value="{{ $change }}">
                    </div>
                </div>
            </div>
        @endif
        <div class="row button--bottom-fixed g-1 bg-white">
            <div class="col-sm-6">
                <button type="submit" class="btn  btn--primary btn-sm btn-block">{{ translate('place_order') }}
                </button>
            </div>
            <div class="col-sm-6">
                <a href="#" class="btn btn--reset btn-sm btn-block"
                    onclick="emptyCart()">{{ translate('Clear Cart') }}</a>
            </div>
        </div>
        {{-- <div class="row pt-2 button--bottom-fixed g-1 bg-white">
            <div class="col-md-6">
                <a href="#" class="btn btn--danger btn-sm btn-block" onclick="emptyCart()"><i
                        class="fa fa-times-circle "></i> {{translate('messages.cancel')}} </a>
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn  btn--primary btn-sm btn-block"><i class="fa fa-shopping-bag"></i>
                    {{translate('messages.order')}} </button>
            </div>
        </div> --}}
    </div>
</form>
<div class="modal fade" id="insertPayableAmount" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light border-bottom py-3">
                <h5 class="modal-title">{{ translate('messages.payment') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id='payable_store_amount'>
                    @csrf
                    <div class="row">
                        <div class="form-group col-12">
                            <label class="input-label"
                                for="">{{ translate('messages.amount') }}({{ \App\CentralLogics\Helpers::currency_symbol() }})</label>
                            <input type="number" class="form-control" name="paid" min="0" step="0.01"
                                value="{{ $paid }}">
                        </div>
                    </div>
                    <div class="form-group col-12 mb-0">
                        <div class="btn--container justify-content-end">
                            <button class="btn btn-sm btn--primary" type="button" onclick="payableAmount()">
                                {{ translate('messages.submit') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="add-discount" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ translate('messages.update_discount') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('vendor.pos.discount') }}" method="post" class="row">
                    @csrf
                    <div class="form-group col-sm-6">
                        <label for="">{{ translate('messages.discount') }}</label>
                        <input type="number" class="form-control" name="discount" min="0"
                            id="discount_input" value="{{ $discount }}"
                            max="{{ $discount_type == 'percent' ? 100 : 1000000000 }}">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="">{{ translate('messages.type') }}</label>
                        <select name="type" class="form-control" id="discount_input_type"
                            onchange="document.getElementById('discount_input').max=(this.value=='percent'?100:1000000000);">
                            <option value="amount" {{ $discount_type == 'amount' ? 'selected' : '' }}>
                                {{ translate('messages.amount') }}({{ \App\CentralLogics\Helpers::currency_symbol() }})
                            </option>
                            <option value="percent" {{ $discount_type == 'percent' ? 'selected' : '' }}>
                                {{ translate('messages.percent') }}(%)</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-12">
                        <button class="btn btn-sm btn--primary"
                            type="submit">{{ translate('messages.submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add-tax" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ translate('messages.update_tax') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('vendor.pos.tax') }}" method="POST" class="row" id="order_submit_form">
                    @csrf
                    <div class="form-group col-12">
                        <label for="">{{ translate('messages.tax') }}(%)</label>
                        <input type="number" class="form-control" name="tax" min="0">
                    </div>

                    <div class="form-group col-sm-12">
                        <button class="btn btn-sm btn--primary"
                            type="submit">{{ translate('messages.submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light border-bottom py-3">
                <h5 class="modal-title flex-grow-1 text-center">{{ translate('Delivery Information') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <?php
                if (session()->has('address')) {
                    $old = session()->get('address');
                } else {
                    $old = null;
                }
                ?>
                <form id='delivery_address_store'>
                    @csrf

                    <div class="row g-2" id="delivery_address">
                        <div class="col-md-6">
                            <label class="input-label"
                                for="">{{ translate('messages.contact_person_name') }}<span
                                    class="input-label-secondary text-danger">*</span></label>
                            <input type="text" class="form-control" name="contact_person_name"
                                value="{{ $old ? $old['contact_person_name'] : '' }}"
                                placeholder="{{ translate('Ex: Jhone') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="input-label" for="">{{ translate('Contact Number') }}<span
                                    class="input-label-secondary text-danger">*</span></label>
                            <input type="tel" class="form-control" name="contact_person_number"
                                value="{{ $old ? $old['contact_person_number'] : '' }}"
                                placeholder="{{ translate('Ex: +3264124565') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="input-label" for="">{{ translate('messages.Road') }}<span
                                    class="input-label-secondary text-danger">*</span></label>
                            <input type="text" class="form-control" name="road"
                                value="{{ $old ? $old['road'] : '' }}" placeholder="{{ translate('Ex: 4th') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="input-label" for="">{{ translate('messages.House') }}<span
                                    class="input-label-secondary text-danger">*</span></label>
                            <input type="text" class="form-control" name="house"
                                value="{{ $old ? $old['house'] : '' }}" placeholder="{{ translate('Ex: 45/C') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="input-label" for="">{{ translate('messages.Floor') }}<span
                                    class="input-label-secondary text-danger">*</span></label>
                            <input type="text" class="form-control" name="floor"
                                value="{{ $old ? $old['floor'] : '' }}" placeholder="{{ translate('Ex: 1A') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="input-label" for="">{{ translate('messages.longitude') }}<span
                                    class="input-label-secondary text-danger">*</span></label>
                            <input type="text" class="form-control" id="longitude" name="longitude"
                                value="{{ $old ? $old['longitude'] : '' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="input-label" for="">{{ translate('messages.latitude') }}<span
                                    class="input-label-secondary text-danger">*</span></label>
                            <input type="text" class="form-control" id="latitude" name="latitude"
                                value="{{ $old ? $old['latitude'] : '' }}" readonly>
                        </div>
                        <div class="col-md-12">
                            <label class="input-label" for="">{{ translate('messages.address') }}</label>
                            <textarea name="address" class="form-control" cols="30" rows="3"
                                placeholder="{{ translate('Ex: address') }}">{{ $old ? $old['address'] : '' }}</textarea>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-primary">
                                    {{ translate('* pin the address in the map to calculate delivery fee') }}
                                </span>
                                <div>
                                    <span>{{ translate('Delivery fee') }} :</span>
                                    <input type="hidden" name="delivery_fee" id="delivery_fee"
                                        value="{{ $old ? $old['delivery_fee'] : '' }}">
                                    <strong>{{ $old ? $old['delivery_fee'] : 0 }}
                                        {{ \App\CentralLogics\Helpers::currency_symbol() }}</strong>
                                </div>
                            </div>
                            <input id="pac-input" class="controls rounded initial-8"
                                title="{{ translate('messages.search_your_location_here') }}" type="text"
                                placeholder="{{ translate('messages.search_here') }}" />
                            <div class="mb-2 h-200px" id="map"></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="btn--container justify-content-end">
                            <button class="btn btn-sm btn--primary w-100" type="button"
                                onclick="deliveryAdressStore()">
                                {{ translate('Update') }} {{ translate('messages.Delivery address') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    var form = document.getElementById('order_place');
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        var customer_id = document.getElementById('customer');
        if (customer_id.value) {
            console.log(customer_id.value);
            document.getElementById('customer_id').value = customer_id.value;
        }
        form.submit();
    })
    // $('#order_place').submit(function(event) {
    // event.preventDefault();
    //     if($('#customer').val())
    //     {
    //         console.log($('#customer').val());
    //         $(this).append('<input type="hidden" name="user_id" value="'+$('#customer').val()+'" /> ');
    //     }
    //     return true;
    // });
    $('#payment_card').on('change', function() {
        $("#paid_section").hide();
    });
    $('#payment_cash').on('change', function() {
        $("#paid_section").show();
    });
</script>
