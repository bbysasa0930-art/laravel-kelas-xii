@extends('admin.layouts.app')

@section('title', 'Kelola Komentar')

@section('content')
<div class="page-header-box">
  <div class="row">
    <div class="col-sm-8">
      <h2><i class="fa fa-comments" style="color: #FF8D1B; margin-right: 8px;"></i>Kelola Komentar Pengguna</h2>
      <p>Daftar seluruh ulasan dan komentar film dari pengguna. Anda dapat meninjau dan menghapus komentar yang tidak pantas.</p>
    </div>
    <div class="col-sm-4 text-right" style="padding-top: 10px;">
      <span class="label label-primary" style="font-size: 14px; padding: 6px 12px;">Total: {{ $comments->total() }} Komentar</span>
    </div>
  </div>
</div>

<div class="admin-card">
  <!-- Bar Pencarian Komentar -->
  <div class="row" style="margin-bottom: 20px;">
    <div class="col-md-6 col-sm-8">
      <form action="{{ route('admin.comments.index') }}" method="GET" class="form-inline">
        <div class="input-group" style="width: 100%;">
          <input 
            type="text" 
            name="search" 
            class="form-control" 
            placeholder="Cari kata kunci komentar, nama user, atau judul film..." 
            value="{{ $search ?? '' }}"
          >
          <span class="input-group-btn">
            <button class="btn btn-orange" type="submit">
              <i class="fa fa-search"></i> Cari
            </button>
            @if(!empty($search))
              <a href="{{ route('admin.comments.index') }}" class="btn btn-default" title="Reset">
                <i class="fa fa-refresh"></i> Reset
              </a>
            @endif
          </span>
        </div>
      </form>
    </div>
  </div>

  <!-- Tabel Komentar -->
  <div class="table-responsive">
    <table class="table table-admin">
      <thead>
        <tr>
          <th style="width: 50px; text-align: center;">#</th>
          <th style="width: 160px;">Nama Pengguna</th>
          <th style="width: 180px;">Judul Film</th>
          <th>Isi Komentar</th>
          <th style="width: 90px; text-align: center;">Rating</th>
          <th style="width: 150px; text-align: center;">Tanggal</th>
          <th style="width: 100px; text-align: center;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($comments as $index => $comm)
          <tr>
            <td style="text-align: center; color: #94a3b8;">
              {{ $comments->firstItem() + $index }}
            </td>
            <td>
              <strong><i class="fa fa-user" style="color: #64748b; margin-right: 5px;"></i>{{ $comm->user->name ?? 'Pengguna' }}</strong>
            </td>
            <td>
              <strong style="color: #FF8D1B;">{{ $comm->film->title ?? 'Film Telah Dihapus' }}</strong>
            </td>
            <td>
              <div style="font-size: 14px; color: #334155; line-height: 1.5;">
                {{ $comm->comment }}
              </div>
            </td>
            <td style="text-align: center;">
              @if($comm->rating)
                <span class="badge" style="background: #f59e0b; color: #fff;">
                  <i class="fa fa-star"></i> {{ $comm->rating }}
                </span>
              @else
                <span class="text-muted">-</span>
              @endif
            </td>
            <td style="text-align: center; color: #64748b; font-size: 13px;">
              {{ $comm->created_at ? \Carbon\Carbon::parse($comm->created_at)->translatedFormat('d F Y, H:i') : '-' }}
            </td>
            <td style="text-align: center;">
              <form action="{{ route('admin.comments.destroy', $comm->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Yakin ingin menghapus komentar ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger" title="Hapus Komentar">
                  <i class="fa fa-trash"></i> Hapus
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center" style="padding: 30px; color: #94a3b8;">
              <i class="fa fa-comments-o fa-3x" style="margin-bottom: 10px; display: block;"></i>
              Tidak ada komentar yang ditemukan.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
    <div class="text-muted" style="font-size: 13px;">
      Menampilkan {{ $comments->firstItem() ?? 0 }} sampai {{ $comments->lastItem() ?? 0 }} dari {{ $comments->total() }} komentar
    </div>
    <div>
      {{ $comments->links() }}
    </div>
  </div>
</div>
@endsection

