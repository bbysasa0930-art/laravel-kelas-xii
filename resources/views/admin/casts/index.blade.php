@extends('admin.layouts.app')

@section('title', 'Kelola Cast')

@section('content')
<div class="page-header-box">
  <div class="row">
    <div class="col-sm-8">
      <h2><i class="fa fa-users" style="color: #FF8D1B; margin-right: 8px;"></i>Kelola Data Cast / Aktor</h2>
      <p>Daftar seluruh aktor dan aktris. Anda dapat mencari, menambah, mengedit, atau menghapus data cast.</p>
    </div>
    <div class="col-sm-4 text-right" style="padding-top: 10px;">
      <a href="{{ route('admin.casts.create') }}" class="btn btn-orange">
        <i class="fa fa-plus-circle"></i> Tambah Cast Baru
      </a>
    </div>
  </div>
</div>

<div class="admin-card">
  <!-- Bar Pencarian -->
  <div class="row" style="margin-bottom: 20px;">
    <div class="col-md-6 col-sm-8">
      <form action="{{ route('admin.casts.index') }}" method="GET" class="form-inline">
        <div class="input-group" style="width: 100%;">
          <input 
            type="text" 
            name="search" 
            class="form-control" 
            placeholder="Cari berdasarkan nama cast..." 
            value="{{ $search ?? '' }}"
          >
          <span class="input-group-btn">
            <button class="btn btn-orange" type="submit">
              <i class="fa fa-search"></i> Cari
            </button>
            @if(!empty($search))
              <a href="{{ route('admin.casts.index') }}" class="btn btn-default" title="Reset">
                <i class="fa fa-refresh"></i> Reset
              </a>
            @endif
          </span>
        </div>
      </form>
    </div>
    <div class="col-md-6 col-sm-4 text-right" style="padding-top: 6px;">
      <span class="text-muted">Total: <strong>{{ $casts->total() }}</strong> Cast</span>
    </div>
  </div>

  <!-- Tabel Daftar Cast -->
  <div class="table-responsive">
    <table class="table table-admin">
      <thead>
        <tr>
          <th style="width: 70px; text-align: center;">Foto</th>
          <th>Nama Cast</th>
          <th style="width: 80px; text-align: center;">Umur</th>
          <th>Biografi</th>
          <th style="width: 110px; text-align: center;">Total Film</th>
          <th style="width: 150px; text-align: center;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($casts as $cast)
          <tr>
            <td style="text-align: center;">
              @if($cast->foto)
                <img src="{{ asset($cast->foto) }}" alt="{{ $cast->name }}" style="width: 45px; height: 45px; object-fit: cover; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.2);">
              @else
                <div style="width: 45px; height: 45px; background: #e2e8f0; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: #64748b;">
                  <i class="fa fa-user fa-lg"></i>
                </div>
              @endif
            </td>
            <td>
              <strong style="font-size: 15px; color: #1e293b;">{{ $cast->name }}</strong>
            </td>
            <td style="text-align: center;">
              {{ $cast->age ? $cast->age . ' thn' : '-' }}
            </td>
            <td>
              <span class="text-muted" style="font-size: 13px;">{{ Str::limit($cast->bio, 80) }}</span>
            </td>
            <td style="text-align: center;">
              <span class="badge" style="background: #3b82f6;">{{ $cast->films_count ?? 0 }} film</span>
            </td>
            <td style="text-align: center;">
              <a href="{{ route('admin.casts.edit', $cast->id) }}" class="btn btn-sm btn-info" style="margin-right: 5px;" title="Edit Cast">
                <i class="fa fa-pencil"></i> Edit
              </a>

              <form action="{{ route('admin.casts.destroy', $cast->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Yakin ingin menghapus cast &quot;{{ addslashes($cast->name) }}&quot;? Relasi perannya pada film juga akan dihapus.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger" title="Hapus Cast">
                  <i class="fa fa-trash"></i> Hapus
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center" style="padding: 30px; color: #94a3b8;">
              <i class="fa fa-users fa-3x" style="margin-bottom: 10px; display: block;"></i>
              Tidak ada data cast yang ditemukan.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
    <div class="text-muted" style="font-size: 13px;">
      Menampilkan {{ $casts->firstItem() ?? 0 }} sampai {{ $casts->lastItem() ?? 0 }} dari {{ $casts->total() }} cast
    </div>
    <div>
      {{ $casts->links() }}
    </div>
  </div>
</div>
@endsection

