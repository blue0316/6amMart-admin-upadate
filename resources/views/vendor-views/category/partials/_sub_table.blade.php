@foreach($categories as $key=>$category)
<tr>
    <td>{{$key+1}}</td>
    <td>{{$category->id}}</td>
    <td>
        <span class="d-block font-size-sm text-body">
            {{Str::limit($category->parent?$category->parent['name']:translate('messages.category_deleted'),20,'...')}}
        </span>
    </td>
    <td>
        <span class="d-block font-size-sm text-body">
            {{Str::limit($category->name,20,'...')}}
        </span>
    </td>
</tr>
@endforeach
