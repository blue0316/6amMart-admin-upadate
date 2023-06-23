@if(count($combinations) > 0)
    <table class="table table-bordered">
        <thead class="thead-light">
        <tr>
            <th class="text-center">
                <span class="control-label">{{translate('messages.Variant')}}</span>
            </th>
            <th class="text-center">
                <span class="control-label">{{translate('messages.Variant Price')}}</span>
            </th>
            @if($stock)
            <th class="text-center">
                <span class="control-label text-capitalize">{{translate('messages.stock')}}</span>
            </th>
            @endif
        </tr>
        </thead>
        <tbody>

        @foreach ($combinations as $key => $combination)
            <tr>
                <td>
                    <span class="control-label">{{ $combination['type'] }}</span>
                    <input value="{{ $combination['type'] }}" name="type[]" type="hidden">
                </td>
                <td>
                    <input type="number" name="price_{{ $combination['type'] }}"
                           value="{{$combination['price']}}" min="0"
                           step="0.01"
                           class="form-control" required>
                </td>
                @if ($stock)
                <td>
                    <input type="number" onkeyup="update_qty()" name="stock_{{ $combination['type'] }}" value="{{$combination['stock']??0}}" min="0" step="0.01"
                            class="form-control" required>
                </td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
