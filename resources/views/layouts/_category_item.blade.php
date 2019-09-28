@if(isset($category['children']) && count($category['children']) > 0)
    <li class="dropdown-submenu">
        <a href="{{ route('products.index', ['category_id' => $category['id']]) }}"
           class="dropdown-item dropdown-toggle" data-toggle="dropdown">{{ $category['name'] }}</a>
        <ul class="dropdown-menu">
            @each('layouts._category_item', $category['children'], 'category')
        </ul>
    </li>
@else
    <li>
        <a class="dropdown-item"
           href="{{ route('products.index', ['category_id' => $category['id']]) }}">{{ $category['name'] }}</a>
    </li>
@endif
