<div class="modal-header p-0">
    <h4 class="modal-title product-title">
    </h4>
    <button class="close call-when-done" type="button" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="d-flex flex-row align-items-center">
        @if (config('toggle_veg_non_veg'))
            <span
                class="badge badge-{{ $product->veg ? 'success' : 'danger' }} position-absolute">{{ $product->veg ? translate('messages.veg') : translate('messages.non_veg') }}</span>
        @endif
        <!-- Product gallery-->
        <div class="d-flex align-items-center justify-content-center active h-9rem">
            <img class="img-responsive img-responsive-1"src="{{ asset('storage/app/public/product') }}/{{ $product['image'] }}"
                onerror="this.src='{{ asset('public/assets/admin/img/160x160/img2.jpg') }}'"
                data-zoom="{{ asset('storage/app/public/product') }}/{{ $product['image'] }}" alt="Product image"
                width="">
            <div class="cz-image-zoom-pane"></div>
        </div>
        <!-- Product details-->
        <div class="details pl-2">
            <a href="{{ route('vendor.item.view', $product->id) }}"
                class="h3 mb-2 product-title text-break">{{ $product->name }}</a>

                @if (isset($product->module_id) && $product->module->module_type == 'food')
                <div class="mb-3 text-dark">
                    <span class="h3 font-weight-normal text-accent mr-1">
                        {{ \App\CentralLogics\Helpers::get_food_price_range($product, true) }}
                    </span>
                    @if ($product->discount > 0 || \App\CentralLogics\Helpers::get_store_discount($product->store))
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
                    @if ($product->discount > 0 || \App\CentralLogics\Helpers::get_store_discount($product->store))
                        <strike class="initial--18">
                            {{ \App\CentralLogics\Helpers::get_price_range($product) }}
                        </strike>
                    @endif
                </div>
                @endif

            @if ($product->discount > 0 || \App\CentralLogics\Helpers::get_store_discount($product->store))
                <div class="mb-3 text-dark">
                    <strong>{{ translate('messages.discount') }} : </strong>
                    <strong
                        id="set-discount-amount">{{ \App\CentralLogics\Helpers::get_product_discount($product) }}</strong>
                </div>
            @endif
            <!-- Product panels-->
        </div>
    </div>
    <div class="row pt-2">
        <div class="col-12">
            <h2>{{ translate('messages.description') }}</h2>
            <span class="d-block text-dark text-break">
                {!! $product->description !!}
            </span>
            <form id="add-to-cart-form" class="mb-2">
                @csrf
                <input type="hidden" name="id" value="{{ $product->id }}">
                <input type="hidden" name="cart_item_key" value="{{ $item_key }}">
                @if ($product->module->module_type == 'food')
                    @if ($product->food_variations)

                        @php($values = [])
                        @php($selected_variations = $cart_item['variations'])
                        @php($names = [])
                        @php($values = [])
                        @foreach ($selected_variations as $key => $var)
                            @if (isset($var['values']))
                                @php($names[$key] = $var['name'])
                                @foreach ($var['values'] as $k => $item)
                                    @php($values[$key] = $item)
                                @endforeach
                            @endif
                        @endforeach

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
                                            @if (isset($values[$key])) {{ in_array($option->label, $values[$key]) ? 'checked' : '' }} @endif
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
                    @foreach (json_decode($product->choice_options) as $key => $choice)
                        <div class="h3 p-0 pt-2">{{ $choice->title }}
                        </div>

                        <div class="d-flex justify-content-left flex-wrap">
                            @foreach ($choice->options as $key => $option)
                                <input class="btn-check" type="radio" id="{{ $choice->name }}-{{ $option }}"
                                    name="{{ $choice->name }}" value="{{ $option }}"
                                    {{ trim($option) == $cart_item[$choice->name] ? 'checked' : '' }}
                                    autocomplete="off">
                                <label class="btn btn-sm check-label mx-1 choice-input  text-break"
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
                        <div class="input-group input-group--style-2 pr-3 w-160px">
                            <span class="input-group-btn">
                                <button class="btn btn-number text-dark p-10px" type="button" data-type="minus"
                                    data-field="quantity"
                                    {{ $cart_item['quantity'] <= 1 ? 'disabled="disabled"' : '' }}>
                                    <i class="tio-remove  font-weight-bold"></i>
                                </button>
                            </span>
                            <input type="text" name="quantity"
                                class="form-control input-number text-center cart-qty-field" placeholder="1"
                                value="{{ $cart_item['quantity'] }}" min="1" max="100">
                            <span class="input-group-btn">
                                <button class="btn btn-number text-dark p-10px" type="button" data-type="plus"
                                    data-field="quantity"><i class="tio-add  font-weight-bold"></i>
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
                        @foreach (\App\Models\AddOn::whereIn('id', $add_ons)->active()->get() as $key => $add_on)
                            <div class="flex-column pb-2">
                                <input type="hidden" name="addon-price{{ $add_on->id }}"
                                    value="{{ $add_on->price }}">
                                <input class="btn-check addon-chek" type="checkbox" id="addon{{ $key }}"
                                    onchange="addon_quantity_input_toggle(event)" name="addon_id[]"
                                    value="{{ $add_on->id }}"
                                    {{ in_array($add_on->id, $cart_item['add_ons']) ? 'checked' : '' }}
                                    autocomplete="off">
                                <label
                                    class="d-flex align-items-center btn btn-sm check-label mx-1 addon-input text-break"
                                    for="addon{{ $key }}">{{ Str::limit($add_on->name, 20, '...') }} <br>
                                    {{ \App\CentralLogics\Helpers::format_currency($add_on->price) }}</label>
                                <label class="input-group addon-quantity-input mx-1 shadow bg-white rounded px-1"
                                    for="addon{{ $key }}"
                                    @if (in_array($add_on->id, $cart_item['add_ons'])) style="visibility:visible;" @endif>
                                    <button class="btn btn-sm h-100 text-dark px-0" type="button"
                                        onclick="this.parentNode.querySelector('input[type=number]').stepDown(), getVariantPrice()"><i
                                            class="tio-remove  font-weight-bold"></i></button>
                                    <input type="number" name="addon-quantity{{ $add_on->id }}"
                                        class="form-control text-center border-0 h-100" placeholder="1"
                                        value="{{ array_search($add_on->id, $cart_item['add_ons']) ? $cart_item['add_on_qtys'][array_search($add_on->id, $cart_item['add_ons'])] : 1 }}"
                                        min="1" max="100" readonly>
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
                        <div class="product-description-label">{{ translate('messages.Total Price') }}:</div>
                    </div>
                    <div class="col-10">
                        <div class="product-price">
                            <strong id="chosen_price"></strong>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center mt-2">
                    <button class="btn btn--primary h--45px w-40p" onclick="addToCart()" type="button">
                        <i class="tio-shopping-cart"></i>
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
