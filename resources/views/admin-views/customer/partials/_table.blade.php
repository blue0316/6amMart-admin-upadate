@foreach ($customers as $key => $customer)
<tr class="">
    <td class="">
        {{ $key + 1 }}
    </td>
    <td class="table-column-pl-0">
        <a href="{{ route('admin.users.customer.view', [$customer['id']]) }}" class="text--hover">
            {{ $customer['f_name'] . ' ' . $customer['l_name'] }}
        </a>
    </td>
    <td>
        <div>
            {{ $customer['email'] }}
        </div>
        <div>
            {{ $customer['phone'] }}
        </div>
    </td>
    <td>
        <label class="badge">
            {{ $customer->order_count }}
        </label>
    </td>
    <td>
        <label class="toggle-switch toggle-switch-sm ml-xl-4" for="stocksCheckbox{{ $customer->id }}">
            <input type="checkbox"
                onclick="status_change_alert('{{ route('admin.users.customer.status', [$customer->id, $customer->status ? 0 : 1]) }}', '{{ $customer->status? translate('messages.you_want_to_block_this_customer'): translate('messages.you_want_to_unblock_this_customer') }}', event)"
                class="toggle-switch-input" id="stocksCheckbox{{ $customer->id }}"
                {{ $customer->status ? 'checked' : '' }}>
            <span class="toggle-switch-label">
                <span class="toggle-switch-indicator"></span>
            </span>
        </label>
    </td>
    <td>
        <a class="btn action-btn btn--warning btn-outline-warning"
            href="{{ route('admin.users.customer.view', [$customer['id']]) }}"
            title="{{ translate('messages.view') }} {{ translate('messages.customer') }}"><i
                class="tio-visible-outlined"></i>
        </a>
    </td>
</tr>
@endforeach
