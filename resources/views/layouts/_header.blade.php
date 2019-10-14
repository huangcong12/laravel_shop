<nav class="navbar navbar-expand-lg navbar-light bg-light navbar-static-top">
    <div class="container">
        {{-- Branding Image--}}
        <a href="{{ url('/') }}" class="navbar-brand">
            Laravel Shop
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse"
        data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
        aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                @if(isset($categoryTree))
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                           aria-expanded="false" id="categoryTree">所有类目 <b class="carent"></b></a>
                        <ul class="dropdown-menu" aria-labelledby="categoryTree">
                            @each('layouts._category_item', $categoryTree, 'category')
                        </ul>
                    </li>
                @endif
            </ul>

            <ul class="navbar-nav navbar-right">
                {{-- 登录注册链接开始 --}}
                @guest
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">登录</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">注册</a></li>
                @else
                    <li class="nav-item">
                        <a href="{{ route('cart.index') }}" class="nav-link mt-1">
                            <i class="fa fa-shopping-cart"></i>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img
                                src="https://www.gravatar.com/avatar/a69ee269fbc8a50c6f830bced14aa64a?s=200"
                                class="img-responsive img-circle" width="30px" height="30px">
                            {{ Auth::user()->nam }}
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a href="#" id="logout" class="dropdown-item"
                               onclick="event.preventDefault();document.getElementById('logout-form').submit()">退出登录</a>
                            <form action="{{ route('logout') }}" id="logout-form" method="post" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                            <a class="dropdown-item" href="{{ route('user_addresses.index') }}">收货地址</a>
                            <a class="dropdown-item" href="{{ route('orders.index') }}">我的订单</a>
                            <a class="dropdown-item" href="{{ route('installments.index') }}">分期付款</a>
                            <a class="dropdown-item" href="{{ route('products.favorites') }}">我的收藏</a>
                        </div>
                    </li>
                    @endguest
                {{-- 登录注册链接结束--}}
            </ul>
        </div>
    </div>
</nav>
