@extends('admin.layouts.app')

@section('title', 'Kelola Film')

@section('content')
<div class="page-header-box">
  <div class="row">
    <div class="col-sm-8">
      <h2><i class="fa fa-film" style="color: #FF8D1B; margin-right: 8px;"></i>Kelola Data Film</h2>
      <p>Daftar seluruh film yang ada dalam database. Anda dapat mencari, menambah, mengedit, atau menghapus film.</p>
    </div>
    <div class="col-sm-4 text-right" style="padding-top: 10px;">
      <a href="{{ route('admin.films.create') }}" class="btn btn-orange">
        <i class="fa fa-plus-circle"></i> Tambah Film Baru
      </a>
    </div>
  </div>
</div>

<div class="admin-card">
  <!-- Bar Pencarian & Tombol Aksi -->
  <div class="row" style="margin-bottom: 20px;">
    <div class="col-md-6 col-sm-8">
      <form action="{{ route('admin.films.index') }}" method="GET" class="form-inline">
        <div class="input-group" style="width: 100%;">
          <input 
            type="text" 
            name="search" 
            class="form-control" 
            placeholder="Cari berdasarkan judul film..." 
            value="{{ $search ?? '' }}"
          >
          <span class="input-group-btn">
            <button class="btn btn-orange" type="submit">
              <i class="fa fa-search"></i> Cari
            </button>
            @if(!empty($search))
              <a href="{{ route('admin.films.index') }}" class="btn btn-default" title="Reset Pencarian">
                <i class="fa fa-refresh"></i> Reset
              </a>
            @endif
          </span>
        </div>
      </form>
    </div>
    <div class="col-md-6 col-sm-4 text-right" style="padding-top: 6px;">
      <span class="text-muted">Total: <strong>{{ $films->total() }}</strong> Film</span>
    </div>
  </div>

  <!-- Tabel Daftar Film -->
  <div class="table-responsive">
    <table class="table table-admin">
      <thead>
        <tr>
          <th style="width: 70px; text-align: center;">Poster</th>
          <th>Judul Film</th>
          <th style="width: 100px; text-align: center;">Tahun</th>
          <th style="width: 140px;">Genre</th>
          <th style="width: 160px; text-align: center;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($films as $film)
          <tr>
            <td style="text-align: center;">
              @if($film->poster)
                <img src="{{ asset($film->poster) }}" alt="{{ $film->title }}" style="width: 45px; height: 60px; object-fit: cover; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.2);">
              @else
                <div style="width: 45px; height: 60px; background: #e2e8f0; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center;">
                  <i class="fa fa-picture-o text-muted"></i>
                </div>
              @endif
            </td>
            <td>
              <strong style="font-size: 15px; color: #1e293b;">{{ $film->title }}</strong>
              <div style="font-size: 12px; color: #64748b; margin-top: 4px;">
                {{ Str::limit($film->sinopsis, 90) }}
              </div>
            </td>
            <td style="text-align: center;">
              <span class="badge" style="background: #e2e8f0; color: #334155; font-size: 13px;">{{ $film->year }}</span>
            </td>
            <td>
              <span class="label label-default" style="font-size: 12px; background: #3b82f6;">
                {{ $film->genre->name ?? 'Tanpa Genre' }}
              </span>
            </td>
            <td style="text-align: center;">
              <a href="{{ route('admin.films.edit', $film->id) }}" class="btn btn-sm btn-info" style="margin-right: 5px;" title="Edit Film">
                <i class="fa fa-pencil"></i> Edit
              </a>
              
              <!-- Form Hapus dengan Konfirmasi -->
              <form action="{{ route('admin.films.destroy', $film->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Yakin ingin menghapus film &quot;{{ addslashes($film->title) }}&quot;? Data relasi cast dan komentar terkait juga akan ikut dihapus.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger" title="Hapus Film">
                  <i class="fa fa-trash"></i> Hapus
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center" style="padding: 30px; color: #94a3b8;">
              <i class="fa fa-film fa-3x" style="margin-bottom: 10px; display: block;"></i>
              Tidak ada data film yang ditemukan.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
    <div class="text-muted" style="font-size: 13px;">
      Menampilkan {{ $films->firstItem() ?? 0 }} sampai {{ $films->lastItem() ?? 0 }} dari {{ $films->total() }} film
    </div>
    <div>
      {{ $films->links() }}
    </div>
  </div>
</div>
@endsection

