@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-header-box">
  <h2><i class="fa fa-dashboard" style="color: #FF8D1B; margin-right: 8px;"></i>Dashboard Administrator</h2>
  <p>Selamat datang di <strong>Dashboard Utama</strong> OneMovies, <strong>{{ Auth::user()->name ?? session('name') }}</strong>! Berikut adalah ringkasan data sistem.</p>
</div>

<!-- Ringkasan Statistik Kartu -->
<div class="row">
  <!-- Total Film -->
  <div class="col-md-4 col-sm-6" style="margin-bottom: 20px;">
    <div style="background: linear-gradient(135deg, #FF8D1B, #f39c12); color: #fff; padding: 22px 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(255,141,27,0.25);">
      <div class="pull-right" style="font-size: 45px; opacity: 0.85;"><i class="fa fa-film"></i></div>
      <h3 style="margin: 0; font-size: 34px; font-weight: 700;">{{ $totalFilms }}</h3>
      <p style="margin: 5px 0 0; font-size: 15px; opacity: 0.95;">Total Film</p>
      <div style="margin-top: 15px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.25);">
        <a href="{{ route('admin.films.index') }}" style="color: #fff; text-decoration: none; font-size: 13px; font-weight: 600;">
          Kelola Film <i class="fa fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
  </div>

  <!-- Total User -->
  <div class="col-md-4 col-sm-6" style="margin-bottom: 20px;">
    <div style="background: linear-gradient(135deg, #0284c7, #0ea5e9); color: #fff; padding: 22px 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(14,165,233,0.25);">
      <div class="pull-right" style="font-size: 45px; opacity: 0.85;"><i class="fa fa-users"></i></div>
      <h3 style="margin: 0; font-size: 34px; font-weight: 700;">{{ $totalUsers }}</h3>
      <p style="margin: 5px 0 0; font-size: 15px; opacity: 0.95;">Total Pengguna</p>
      <div style="margin-top: 15px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.25);">
        <span style="font-size: 13px; opacity: 0.9;">Pengguna Terdaftar</span>
      </div>
    </div>
  </div>

  <!-- Total Komentar -->
  <div class="col-md-4 col-sm-12" style="margin-bottom: 20px;">
    <div style="background: linear-gradient(135deg, #059669, #10b981); color: #fff; padding: 22px 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(16,185,129,0.25);">
      <div class="pull-right" style="font-size: 45px; opacity: 0.85;"><i class="fa fa-comments"></i></div>
      <h3 style="margin: 0; font-size: 34px; font-weight: 700;">{{ $totalComments }}</h3>
      <p style="margin: 5px 0 0; font-size: 15px; opacity: 0.95;">Total Komentar</p>
      <div style="margin-top: 15px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.25);">
        <a href="{{ route('admin.comments.index') }}" style="color: #fff; text-decoration: none; font-size: 13px; font-weight: 600;">
          Kelola Komentar <i class="fa fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Daftar Film Terbaru -->
  <div class="col-md-7">
    <div class="admin-card">
      <div class="admin-card-header">
        <h3><i class="fa fa-film" style="color: #FF8D1B; margin-right: 6px;"></i> Film Terbaru Ditambahkan</h3>
        <a href="{{ route('admin.films.create') }}" class="btn btn-xs btn-orange">
          <i class="fa fa-plus"></i> Tambah Film
        </a>
      </div>
      <div class="table-responsive">
        <table class="table table-admin">
          <thead>
            <tr>
              <th style="width: 50px;">Poster</th>
              <th>Judul</th>
              <th>Genre</th>
              <th>Tahun</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentFilms as $film)
              <tr>
                <td>
                  @if($film->poster)
                    <img src="{{ asset($film->poster) }}" alt="{{ $film->title }}" style="width: 36px; height: 50px; object-fit: cover; border-radius: 3px;">
                  @else
                    <div style="width: 36px; height: 50px; background: #eee; display: flex; align-items: center; justify-content: center; border-radius: 3px;">
                      <i class="fa fa-image text-muted"></i>
                    </div>
                  @endif
                </td>
                <td>
                  <strong>{{ $film->title }}</strong>
                </td>
                <td>
                  <span class="label label-default">{{ $film->genre->name ?? '-' }}</span>
                </td>
                <td>{{ $film->year }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center text-muted">Belum ada data film.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Komentar Terbaru -->
  <div class="col-md-5">
    <div class="admin-card">
      <div class="admin-card-header">
        <h3><i class="fa fa-comments" style="color: #059669; margin-right: 6px;"></i> Komentar Terbaru</h3>
        <a href="{{ route('admin.comments.index') }}" class="btn btn-xs btn-default">
          Lihat Semua
        </a>
      </div>
      <div class="table-responsive">
        <table class="table table-admin">
          <thead>
            <tr>
              <th>User</th>
              <th>Film</th>
              <th>Isi Komentar</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentComments as $comm)
              <tr>
                <td><strong>{{ $comm->user->name ?? 'Pengguna' }}</strong></td>
                <td><small>{{ Str::limit($comm->film->title ?? '-', 15) }}</small></td>
                <td><small class="text-muted">{{ Str::limit($comm->comment, 30) }}</small></td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center text-muted">Belum ada komentar.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
