<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Admin Panel') - OneMovies Dashboard</title>

  <!-- Bootstrap & FontAwesome dari template -->
  <link href="{{ asset('template/css/bootstrap.css') }}" rel="stylesheet" type="text/css" media="all" />
  <link rel="stylesheet" href="{{ asset('template/css/font-awesome.min.css') }}" />
  <link href="//fonts.googleapis.com/css?family=Roboto+Condensed:100,200,300,400,500,600,700,800,900" rel="stylesheet">

  <style>
    body {
      font-family: 'Roboto Condensed', sans-serif;
      background-color: #f4f6f9;
      color: #333;
      margin: 0;
      padding: 0;
    }
    /* Top Header */
    .admin-header {
      background: #1f242d;
      color: #fff;
      padding: 12px 20px;
      border-bottom: 3px solid #FF8D1B;
      position: sticky;
      top: 0;
      z-index: 1000;
    }
    .admin-header .brand {
      font-size: 20px;
      font-weight: 700;
      color: #fff;
      text-decoration: none;
      letter-spacing: 1px;
    }
    .admin-header .brand span {
      color: #FF8D1B;
    }
    .admin-header .user-info {
      float: right;
      padding-top: 4px;
    }
    .admin-header .user-info a {
      color: #ccc;
      margin-left: 15px;
      text-decoration: none;
      transition: color 0.2s;
    }
    .admin-header .user-info a:hover {
      color: #FF8D1B;
    }
    
    /* Layout Container */
    .admin-wrapper {
      display: flex;
      min-height: calc(100vh - 55px);
    }
    
    /* Sidebar */
    .admin-sidebar {
      width: 250px;
      background: #252b36;
      color: #a4b0be;
      flex-shrink: 0;
      box-shadow: 2px 0 8px rgba(0,0,0,0.05);
    }
    .admin-sidebar .sidebar-heading {
      padding: 18px 20px 10px;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      color: #747d8c;
      font-weight: 700;
    }
    .admin-sidebar ul.nav-menu {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    .admin-sidebar ul.nav-menu li a {
      display: block;
      padding: 14px 20px;
      color: #ced6e0;
      text-decoration: none;
      font-size: 15px;
      border-left: 4px solid transparent;
      transition: all 0.2s;
    }
    .admin-sidebar ul.nav-menu li a i {
      margin-right: 12px;
      width: 18px;
      text-align: center;
      font-size: 16px;
    }
    .admin-sidebar ul.nav-menu li a:hover {
      background: #2f3542;
      color: #fff;
      border-left-color: #FF8D1B;
    }
    .admin-sidebar ul.nav-menu li.active a {
      background: #2f3542;
      color: #fff;
      border-left-color: #FF8D1B;
      font-weight: 600;
    }
    
    /* Main Content */
    .admin-main {
      flex: 1;
      padding: 25px 30px;
      background-color: #f4f6f9;
      overflow-x: hidden;
    }
    .page-header-box {
      background: #fff;
      padding: 20px 25px;
      border-radius: 6px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.04);
      margin-bottom: 25px;
      border-left: 5px solid #FF8D1B;
    }
    .page-header-box h2 {
      margin: 0 0 5px 0;
      font-size: 24px;
      font-weight: 700;
      color: #2f3542;
    }
    .page-header-box p {
      margin: 0;
      color: #747d8c;
      font-size: 14px;
    }
    
    /* Content Card */
    .admin-card {
      background: #fff;
      border-radius: 6px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      padding: 25px;
      margin-bottom: 25px;
    }
    .admin-card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      padding-bottom: 12px;
      border-bottom: 1px solid #edf2f7;
    }
    .admin-card-header h3 {
      margin: 0;
      font-size: 18px;
      font-weight: 600;
      color: #2f3542;
    }
    
    /* Table styling */
    .table-admin {
      width: 100%;
      margin-bottom: 0;
    }
    .table-admin thead th {
      background: #f8fafc;
      color: #475569;
      font-weight: 600;
      border-bottom: 2px solid #e2e8f0;
      font-size: 14px;
      padding: 12px 14px;
    }
    .table-admin tbody td {
      vertical-align: middle;
      padding: 12px 14px;
      border-top: 1px solid #e2e8f0;
      font-size: 14px;
    }
    .table-admin tbody tr:hover {
      background-color: #f8fafc;
    }

    /* Buttons */
    .btn-orange {
      background-color: #FF8D1B;
      border-color: #FF8D1B;
      color: #fff;
      font-weight: 600;
      transition: background 0.2s;
    }
    .btn-orange:hover, .btn-orange:focus {
      background-color: #e0760d;
      border-color: #e0760d;
      color: #fff;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .admin-wrapper {
        flex-direction: column;
      }
      .admin-sidebar {
        width: 100%;
      }
      .admin-main {
        padding: 15px;
      }
    }
  </style>
  @stack('styles')
</head>
<body>

  <!-- Header Atas Dashboard -->
  <header class="admin-header clearfix">
    <div class="pull-left">
      <a href="{{ route('admin.dashboard') }}" class="brand">
        One<span>Movies</span> <small style="font-size: 13px; color: #a4b0be; font-weight: normal;">| Admin Panel</small>
      </a>
    </div>
    <div class="user-info">
      <span><i class="fa fa-user-circle" style="color: #FF8D1B;"></i> {{ Auth::user()->name ?? session('name') }}</span>
      <span class="label label-danger" style="margin-left: 5px; text-transform: uppercase;">{{ Auth::user()->role ?? session('role') }}</span>
      <a href="{{ route('home') }}" target="_blank" title="Lihat Website">
        <i class="fa fa-globe"></i> Website
      </a>
      <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-admin-form').submit();" title="Logout">
        <i class="fa fa-sign-out"></i> Logout
      </a>
      <form id="logout-admin-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
      </form>
    </div>
  </header>

  <div class="admin-wrapper">
    <!-- Sidebar Menu Navigasi -->
    <aside class="admin-sidebar">
      <div class="sidebar-heading">Menu Utama</div>
      <ul class="nav-menu">
        <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
          <a href="{{ route('admin.dashboard') }}">
            <i class="fa fa-dashboard"></i> Dashboard
          </a>
        </li>
        <li class="{{ request()->routeIs('admin.films.*') ? 'active' : '' }}">
          <a href="{{ route('admin.films.index') }}">
            <i class="fa fa-film"></i> Kelola Film
          </a>
        </li>
        <li class="{{ request()->routeIs('admin.casts.*') ? 'active' : '' }}">
          <a href="{{ route('admin.casts.index') }}">
            <i class="fa fa-users"></i> Kelola Cast
          </a>
        </li>
        <li class="{{ request()->routeIs('admin.comments.*') ? 'active' : '' }}">
          <a href="{{ route('admin.comments.index') }}">
            <i class="fa fa-comments"></i> Kelola Komentar
          </a>
        </li>
      </ul>

      <div class="sidebar-heading">Navigasi Lainnya</div>
      <ul class="nav-menu">
        <li>
          <a href="{{ route('home') }}">
            <i class="fa fa-arrow-left"></i> Kembali ke Website
          </a>
        </li>
        <li>
          <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-admin-form-side').submit();">
            <i class="fa fa-sign-out"></i> Keluar (Logout)
          </a>
          <form id="logout-admin-form-side" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
          </form>
        </li>
      </ul>
    </aside>

    <!-- Konten Utama Halaman -->
    <main class="admin-main">
      {{-- Notifikasi Flash Message Sukses --}}
      @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert" style="border-radius: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <i class="fa fa-check-circle" style="margin-right: 6px;"></i> {{ session('success') }}
        </div>
      @endif

      {{-- Notifikasi Flash Message Error --}}
      @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert" style="border-radius: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <i class="fa fa-exclamation-triangle" style="margin-right: 6px;"></i> {{ session('error') }}
        </div>
      @endif

      {{-- Ringkasan Error Validasi --}}
      @if($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert" style="border-radius: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <strong><i class="fa fa-times-circle" style="margin-right: 5px;"></i> Terjadi kesalahan validasi:</strong>
          <ul style="margin-top: 5px; margin-bottom: 0; padding-left: 20px;">
            @foreach($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @yield('content')
    </main>
  </div>

  <script src="{{ asset('template/js/jquery-2.1.4.min.js') }}"></script>
  <script src="{{ asset('template/js/bootstrap.min.js') }}"></script>
  @stack('scripts')
</body>
</html>

