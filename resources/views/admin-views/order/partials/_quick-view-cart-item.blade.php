<div class="modal-header p-0">
    <h4 class="modal-title product-title">
    </h4>
    <button class="close call-when-done" type="button" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="d-flex flex-row">
        <!-- Product gallery-->
        <div class="d-flex align-items-center justify-content-center active">
            <img class="img-responsive initial--20"
                src="{{ asset($item_type == 'item' ? 'storage/app/public/product' : 'storage/app/public/campaign') }}/{{ $product['image'] }}"
                onerror="this.src='{{ asset('public/assets/admin/img/160x160/img2.jpg') }}'" alt="Product image"
                width="">
            <div class="cz-image-zoom-pane"></div>
        </div>
        <!-- Product details-->
        <div class="details pl-2 w-0 flex-grow">
            @if ($item_type == 'item')
                <a href="{{ route('vendor.item.view', $product->id) }}"
                    class="h3 mb-2 product-title">{{ $product->name }}</a>
            @else
                <div class="h3 mb-2 product-title">{{ $product->title }}</div>
            @endif
            @if (isset($product->module_id) && $product->module->module_type == 'food')
                <div class="mb-3 text-dark">
                    <span class="h3 font-weight-normal text-accent mr-1">
                        {{ \App\CentralLogics\Helpers::get_food_price_range($product, true) }}
                    </span>
                    @if ($product->discount > 0)
                        <strike class="initial--18">
                            {{ \App\CentralLogics\Helpers::get_food_price_range($product) }}
                        </strike>
                    @endif
                </div>
            @else
                <div class="mb-3 text-dark">
                    <span class="h3 font-weight-normal text-accent mr-1">
                        {{ \App\CentralLogics\Helpers::get_price_range($product, true) }}
                    </span>
                    @if ($product->discount > 0)
                        <strike class="initial--18">
                            {{ \App\CentralLogics\Helpers::get_price_range($product) }}
                        </strike>
                    @endif
                </div>
            @endif

            @if ($product->discount > 0)
                <div class="mb-3 text-dark">
                    <strong>{{ translate('messages.discount') }} : </strong>
                    <strong
                        id="set-discount-amount">{{ \App\CentralLogics\Helpers::get_product_discount($product) }}</strong>
                </div>
            @endif
            <!-- Product panels-->
            {{-- <div class="sharethis-inline-share-buttons"></div> --}}
        </div>
    </div>
    <div class="row pt-2">
        <div class="col-12">
            <h2>{{ translate('messages.description') }}</h2>
            <span class="d-block text-dark">
                {!! $product->description !!}
            </span>
            <form id="add-to-cart-form" class="mb-2">
                @csrf
                <input type="hidden" name="id" value="{{ $product->id }}">
                <input type="hidden" name="cart_item_key" value="{{ $item_key }}">
                <input type="hidden" name="item_type" value="{{ $item_type }}">
                <input type="hidden" name="order_details_id" value="{{ $cart_item['id'] }}">
                <input type="hidden" name="order_id" value="{{ $order_id }}">

                @php($temp = json_decode($cart_item->variation, true))

                @if (isset($product->module_id) && $product->module->module_type == 'food')
                    @if ($product->food_variations)
                        @php($singleArray = [])
                        @php($singleArray_name = [])
                        @php($values = [])

                        @php($selected_variations = json_decode($cart_item['variation'], true))
                        @if (is_array($selected_variations))

                            @php($singleArray = array_column($selected_variations, 'values'))
                            @php($singleArray_name = array_column($selected_variations, 'name'))

                            @php($names = [])
                            @php($values = [])
                            @foreach ($selected_variations as $key => $var)
                                @if (isset($var['values']))
                                    @php($names[$key] = $var['name'])
                                    @php($items = [])
                                    @foreach ($var['values'] as $k => $item)

                                        @php($items[$k] = $item['label'])
                                    @endforeach
                                    @php($values[$key] = $items)
                                @endif
                            @endforeach
                        @endif



                        @foreach (json_decode($product->food_variations) as $key => $choice)
                            @if (isset($choice->name) && isset($choice->values))
                                <div class="h3 p-0 pt-2">{{ $choice->name }} <small style="font-size: 12px"
                                        class="text-muted">
                                        ({{ $choice->required == 'on' ? translate('messages.Required') : translate('messages.optional') }})
                                    </small>
                                </div>
                                @if ($choice->min != 0 && $choice->max != 0)
                                    <small class="d-block mb-3">
                                        {{ translate('You_need_to_select_minimum_ ') }} {{ $choice->min }}
                                        {{ translate('to_maximum_ ') }} {{ $choice->max }}
                                        {{ translate('options') }}
                                    </small>
                                @endif

                                <input type="hidden" name="variations[{{ $key }}][min]"
                                    value="{{ $choice->min }}">
                                <input type="hidden" name="variations[{{ $key }}][max]"
                                    value="{{ $choice->max }}">
                                <input type="hidden" name="variations[{{ $key }}][required]"
                                    value="{{ $choice->required }}">
                                <input type="hidden" name="variations[{{ $key }}][name]"
                                    value="{{ $choice->name }}">

                                @foreach ($choice->values as $k => $option)
                                    <div class="form-check form--check d-flex pr-5 mr-5">
                                        <input class="form-check-input"
                                            type="{{ $choice->type == 'multi' ? 'checkbox' : 'radio' }}"
                                            id="choice-option-{{ $key }}-{{ $k }}"
                                            name="variations[{{ $key }}][values][label][]"
                                            value="{{ $option->label }}"
                                            @if (isset($values[$key]))
                                                
                                            {{ in_array($option->label, $values[$key]) ? 'checked' : '' }}
                                            @endif
                                            autocomplete="off">
                                        <label class="form-check-label"
                                            for="choice-option-{{ $key }}-{{ $k }}">{{ Str::limit($option->label, 20, '...') }}</label>
                                        <span
                                            class="ml-auto">{{ \App\CentralLogics\Helpers::format_currency($option->optionPrice) }}</span>
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                @else
                    @php($variations = count($temp) > 0 ? explode('-', $temp[0]['type']) : [])
                    @foreach (json_decode($product->choice_options) as $key => $choice)
                        <div class="h3 p-0 pt-2">{{ $choice->title }}
                        </div>

                        <div class="d-flex justify-content-left flex-wrap">
                            @foreach ($choice->options as $option)
                                <input class="btn-check" type="radio" id="{{ $choice->name }}-{{ $option }}"
                                    name="{{ $choice->name }}" value="{{ $option }}"
                                    {{ count($temp) > 0 && str_replace(' ', '', $option) == $variations[$key] ? 'checked' : '' }}
                                    autocomplete="off">
                                <label class="btn btn-sm check-label mx-1 choice-input"
                                    for="{{ $choice->name }}-{{ $option }}">{{ Str::limit($option, 20, '...') }}</label>
                            @endforeach
                        </div>
                    @endforeach
                @endif

                <!-- Quantity + Add to cart -->
                <div class="d-flex justify-content-between">
                    <div class="product-description-label mt-2 text-dark h3">{{ translate('messages.quantity') }}:
                    </div>
                    <div class="product-quantity d-flex align-items-center">
                        <div class="input-group input-group--style-2 pr-3 initial--19">
                            <span class="input-group-btn">
                                <button class="btn btn-number text-dark pl-2" type="button" data-type="minus"
                                    data-field="quantity"
                                    {{ $cart_item['quantity'] <= 1 ? 'disabled="disabled"' : '' }}>
                                    <i class="tio-remove  font-weight-bold"></i>
                                </button>
                            </span>
                            <input type="text" name="quantity"
                                class="form-control input-number text-center cart-qty-field" placeholder="1"
                                value="{{ $cart_item['quantity'] }}" min="1" max="100">
                            <span class="input-group-btn">
                                <button class="btn btn-number text-dark p-2" type="button" data-type="plus"
                                    data-field="quantity">
                                    <i class="tio-add  font-weight-bold"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
                @php($add_ons = json_decode($product->add_ons))
                @if (count($add_ons) > 0 && $add_ons[0])
                    <div class="h3 p-0 pt-2">{{ translate('messages.addon') }}
                    </div>

                    <div class="d-flex justify-content-left flex-wrap">
                        @php($addons = array_column(json_decode($cart_item['add_ons'], true), 'quantity', 'id'))
                        @foreach (\App\Models\AddOn::withoutGlobalScope(\App\Scopes\StoreScope::class)->whereIn('id', $add_ons)->active()->get() as $key => $add_on)
                            @php($checked = array_key_exists($add_on->id, $addons))
                            <div class="flex-column pb-2">
                                <input type="hidden" name="addon-price{{ $add_on->id }}"
                                    value="{{ $add_on->price }}">
                                <input class="btn-check addon-chek" type="checkbox" id="addon{{ $key }}"
                                    onchange="addon_quantity_input_toggle(event)" name="addon_id[]"
                                    value="{{ $add_on->id }}" {{ $checked ? 'checked' : '' }} autocomplete="off">
                                <label class="d-flex align-items-center btn btn-sm check-label mx-1 addon-input"
                                    for="addon{{ $key }}">{{ Str::limit($add_on->name, 20, '...') }} <br>
                                    {{ \App\CentralLogics\Helpers::format_currency($add_on->price) }}</label>
                                <label
                                    class="input-group addon-quantity-input mx-1 shadow bg-white rounded px-1 @if ($checked) visiblity-visible @endif"
                                    for="addon{{ $key }}">
                                    <button class="btn btn-sm h-100 text-dark px-0" type="button"
                                        onclick="this.parentNode.querySelector('input[type=number]').stepDown(), getVariantPrice()"><i
                                            class="tio-remove  font-weight-bold"></i></button>
                                    <input type="number" name="addon-quantity{{ $add_on->id }}"
                                        class="form-control text-center border-0 h-100" placeholder="1"
                                        value="{{ $checked ? $addons[$add_on->id] : 1 }}" min="1"
                                        max="100" readonly>
                                    <button class="btn btn-sm h-100 text-dark px-0" type="button"
                                        onclick="this.parentNode.querySelector('input[type=number]').stepUp(), getVariantPrice()"><i
                                            class="tio-add  font-weight-bold"></i></button>
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endif
                <div class="row no-gutters d-none mt-2 text-dark" id="chosen_price_div">
                    <div class="col-2">
                        <div class="product-description-label">{{ translate('Total Price') }}:</div>
                    </div>
                    <div class="col-10">
                        <div class="product-price">
                            <strong id="chosen_price"></strong>
                        </div>
                    </div>
                </div>

                <div class="btn--container justify-content-end mt-2">
                    <button class="btn btn--danger" onclick="removeFromCart({{ $item_key }})" type="button">
                        <i class="tio-delete"></i>
                        {{ translate('messages.delete') }}
                    </button>
                    <button class="btn btn--primary" onclick="update_order_item()" type="button">
                        <i class="tio-edit"></i>
                        {{ translate('messages.update') }}
                    </button>

                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    cartQuantityInitialize();
    getVariantPrice();
    $('#add-to-cart-form input').on('change', function() {
        getVariantPrice();
    });
</script>
