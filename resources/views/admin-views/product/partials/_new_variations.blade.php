
<div class="card view_new_option mb-2">
    <div class="card-header">
        <label for=""
            id="new_option_name_{{ $key }}">{{ isset($item['name']) ? $item['name'] : "translate('add new variation')" }}</label>
    </div>
    <div class="card-body">
        <div class="row g-2">
            <div class="col-lg-3 col-md-6">
                <label for="">{{ translate('name') }}</label>
                <input required name="options[{{ $key }}][name]" required class="form-control"
                    type="text" onkeyup="new_option_name(this.value,{{ $key }})"
                    value="{{ $item['name'] }}">
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label class="input-label text-capitalize d-flex alig-items-center"><span
                            class="line--limit-1">{{ translate('messages.selcetion_type') }} </span>
                    </label>
                    <div class="resturant-type-group border">
                        <label class="form-check form--check mr-2 mr-md-4">
                            <input class="form-check-input" type="radio" value="multi"
                                name="options[{{ $key }}][type]" id="type{{ $key }}"
                                {{ $item['type'] == 'multi' ? 'checked' : '' }}
                                onchange="show_min_max({{ $key }})">
                            <span class="form-check-label">
                                {{ translate('Multiple') }}
                            </span>
                        </label>

                        <label class="form-check form--check mr-2 mr-md-4">
                            <input class="form-check-input" type="radio" value="single"
                                {{ $item['type'] == 'single' ? 'checked' : '' }} name="options[{{ $key }}][type]"
                                id="type{{ $key }}" onchange="hide_min_max({{ $key }})">
                            <span class="form-check-label">
                                {{ translate('Single') }}
                            </span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="row g-2">
                    <div class="col-sm-6 col-md-4">
                        <label for="">{{ translate('Min') }}</label>
                        <input id="min_max1_{{ $key }}" {{ $item['type'] == 'single' ? 'readonly ' : 'required' }}
                            value="{{ ($item['min'] != 0) ? $item['min']:''  }}" name="options[{{ $key }}][min]"
                            class="form-control" type="number" min="1">
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <label for="">{{ translate('Max') }}</label>
                        <input id="min_max2_{{ $key }}" {{ $item['type'] == 'single' ? 'readonly ' : 'required' }}
                            value="{{ ($item['max'] != 0) ? $item['max']:''  }}" name="options[{{ $key }}][max]"
                            class="form-control" type="number" min="2">
                    </div>

                    <div class="col-md-4">
                        <label class="d-md-block d-none">&nbsp;</label>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <input name="options[{{ $key }}][required]" type="checkbox"
                                    {{ isset($item['required']) ? ($item['required'] == 'on' ? 'checked	' : '') : '' }}>
                                <label for="options[{{ $key }}][required]"
                                    class="m-0">{{ translate('Required') }}</label>
                            </div>
                            <div>
                                <button type="button" class="btn btn-danger btn-sm delete_input_button"
                                    onclick="removeOption(this)" title="{{ translate('Delete') }}">
                                    <i class="tio-add-to-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="option_price_{{ $key }}">
            <div class="border rounded p-3 pb-0 mt-3" >
                    <div id="option_price_view_{{ $key }}">


                @if (isset($item['values']))
                    @foreach ($item['values'] as $key_value => $value)
                        <div class="row add_new_view_row_class mb-3 position-relative pt-3 pt-md-0">
                            <div class="col-md-4 col-sm-6">
                                <label for="">{{ translate('Option_name') }}</label>
                                <input class="form-control" required type="text"
                                    name="options[{{ $key }}][values][{{ $key_value }}][label]"
                                    value="{{ $value['label'] }}">
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <label for="">{{ translate('Additional_price') }}</label>
                                <input class="form-control" required type="number" min="0" step="0.01"
                                    name="options[{{ $key }}][values][{{ $key_value }}][optionPrice]"
                                    value="{{ $value['optionPrice'] }}">
                            </div>
                            <div class="col-sm-2 max-sm-absolute">
                                <label class="d-none d-md-block">&nbsp;</label>
                                <div class="mt-1">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this)"
                                        title="{{translate('Delete')}}">
                                        <i class="tio-add-to-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                    @endforeach
                @endif
            </div>
                <div class="row mt-3 p-3 mr-1 d-flex" id="add_new_button_{{ $key }}">
                    <button type="button"
                        class="btn btn-outline-primary"onclick="add_new_row_button({{ $key }})">{{ translate('Add_New_Option') }}</button>
                </div>

            </div>




        </div>
    </div>
</div>
