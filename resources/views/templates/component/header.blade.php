<div class="header">
  <div class="container">
    <div class="w3layouts_logo">
      <a href="{{ route('home') }}"><h1>One<span>Movies</span></h1></a>
    </div>
    <div class="w3_search">
      <form action="#" method="post">
        <input type="text" name="Search" placeholder="Search" required="">
        <input type="submit" value="Go">
      </form>
    </div>
    <div class="w3l_sign_in_register">
      <ul>
        @if(Auth::check() || session()->has('user_id'))
          @php
            $currentName = Auth::user()->name ?? session('name');
          @endphp
          <li><i class="fa fa-user" style="color: #FF8D1B; margin-right: 5px;"></i> <span style="color: #fff;">{{ $currentName }}</span></li>
          <li>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('header-logout-form').submit();">Logout</a>
            <form id="header-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
            </form>
          </li>
        @else
          <li><a href="{{ route('login') }}">Login</a></li>
          <li><a href="{{ route('register') }}">Register</a></li>
        @endif
      </ul>
    </div>
    <div class="clearfix"> </div>
  </div>
</div>
