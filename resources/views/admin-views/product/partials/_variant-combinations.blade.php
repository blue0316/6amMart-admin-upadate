@if(count($combinations[0]) > 0)
    <table class="table table-bordered">
        <thead class="thead-light">
            <tr>
                <th class="text-center border-0">
                    <span class="control-label">{{translate('messages.Variant')}}</span>
                </th>
                <th class="text-center border-0">
                    <span class="control-label">{{translate('messages.Variant Price')}}</span>
                </th>
                @if($stock)
                <th class="text-center border-0">
                    <span class="control-label text-capitalize">{{translate('messages.stock')}}</span>
                </th>
                @endif
            </tr>
        </thead>
        <tbody>

        @foreach ($combinations as $key => $combination)
            @php
                $str = '';
                foreach ($combination as $key => $item){
                    if($key > 0 ){
                        $str .= '-'.str_replace(' ', '', $item);
                    }
                    else{
                        $str .= str_replace(' ', '', $item);
                    }
                }
            @endphp
            @if(strlen($str) > 0)
                <tr>
                    <td>
                        <label for="" class="control-label">{{ $str }}</label>
                    </td>
                    <td>
                        <input type="number" name="price_{{ $str }}" value="{{ $price }}" min="0" step="0.01"
                               class="form-control" required>
                    </td>
                    @if ($stock)
                        <td><input type="number" name="stock_{{ $str }}" value="1" min="0" step="0.01"
                               class="form-control" required></td>
                    @endif

                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
    <script>
        update_qty();
        function update_qty()
        {
            var total_qty = 0;
            var qty_elements = $('input[name^="stock_"]');
            for(var i=0; i<qty_elements.length; i++)
            {
                total_qty += parseInt(qty_elements.eq(i).val());
            }
            if(qty_elements.length > 0)
            {

                $('input[name="current_stock"]').attr("readonly", true);
                $('input[name="current_stock"]').val('wrfrwf');
            }
            else{
                $('input[name="current_stock"]').attr("readonly", false);
            }
        }
        $('input[name^="stock_"]').on('keyup', function () {
            var total_qty = 0;
            var qty_elements = $('input[name^="stock_"]');
            for(var i=0; i<qty_elements.length; i++)
            {
                total_qty += parseInt(qty_elements.eq(i).val());
            }
            $('input[name="current_stock"]').val(total_qty);
        });

    </script>
@endif
