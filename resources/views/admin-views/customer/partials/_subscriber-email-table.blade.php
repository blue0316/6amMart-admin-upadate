@foreach ($customers as $key => $customer)
<tr>
    <td>
        {{ ++$key }}
    </td>
    <td>
        {{ $customer->email }}
    </td>
    <td>{{ date('Y-m-d', strtotime($customer->created_at)) }}</td>
</tr>
@endforeach
