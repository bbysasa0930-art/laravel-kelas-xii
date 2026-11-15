<div class="movies_nav">
  <div class="container">
    <nav class="navbar navbar-default">
      <div class="navbar-header navbar-right">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
      </div>
      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse navbar-right" id="bs-example-navbar-collapse-1">
        <nav>
          <ul class="nav navbar-nav">
            <li class="w3_home_act {{ (Route::current() && Route::current()->getName() == 'home') ? 'active' : '' }}"><a href="{{ route('home') }}">Home</a></li>
            <li class="dropdown {{ (Route::current() && Route::current()->getName() == 'genre') ? 'active' : '' }}">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Genres <b class="caret"></b></a>
              <ul class="dropdown-menu multi-column columns-3">
                <li>
                  <div class="col-sm-4">
                    <ul class="multi-column-dropdown">
                      @if(isset($genres) && $genres->count() > 0)
                        @for ($genre = 0; $genre < $genres->count(); $genre++)
                          <li><a href="{{ route('genre', $genres[$genre]->id) }}">{{ $genres[$genre]->name }}</a></li>
                          @if(($genre + 1) % 7 == 0 && ($genre + 1) < $genres->count())
                            </ul>
                          </div>
                          <div class="col-sm-4">
                            <ul class="multi-column-dropdown">
                          @endif
                        @endfor
                      @endif
                    </ul>
                  </div>
                  <div class="clearfix"></div>
                </li>
              </ul>
            </li>
            <li class="w3_home_act {{ (Route::current() && Route::current()->getName() == 'movies') ? 'active' : '' }}"><a href="{{ route('movies') }}">Movies</a></li>

            {{-- Fitur Navbar: Autentikasi Pengguna --}}
            @if(Auth::check() || session()->has('user_id'))
              @php
                $currentUser = Auth::user();
                $currentRole = $currentUser->role ?? session('role');
                $currentName = $currentUser->name ?? session('name');
              @endphp

              {{-- Jika role admin, tampilkan link ke Dashboard Admin --}}
              @if($currentRole === 'admin' || (isset($currentUser->role_id) && $currentUser->role_id == 2))
                <li class="{{ request()->is('admin*') ? 'active' : '' }}">
                  <a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard Admin</a>
                </li>
              @endif

              {{-- Tampilkan nama user yang sedang login --}}
              <li>
                <a href="javascript:void(0);" style="cursor: default;"><i class="fa fa-user"></i> {{ $currentName }}</a>
              </li>

              {{-- Tombol Logout --}}
              <li>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('navbar-logout-form').submit();">
                  <i class="fa fa-sign-out"></i> Logout
                </a>
                <form id="navbar-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                  @csrf
                </form>
              </li>
            @else
              {{-- Tombol Login dan Register jika belum login --}}
              <li class="{{ (Route::current() && Route::current()->getName() == 'login') ? 'active' : '' }}">
                <a href="{{ route('login') }}"><i class="fa fa-sign-in"></i> Login</a>
              </li>
              <li class="{{ (Route::current() && Route::current()->getName() == 'register') ? 'active' : '' }}">
                <a href="{{ route('register') }}"><i class="fa fa-user-plus"></i> Register</a>
              </li>
            @endif

          </ul>
        </nav>
      </div>
    </nav>	
  </div>
</div>
