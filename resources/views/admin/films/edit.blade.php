@extends('admin.layouts.app')

@section('title', 'Edit Film: ' . $film->title)

@section('content')
<div class="page-header-box">
  <div class="row">
    <div class="col-sm-8">
      <h2><i class="fa fa-pencil-square" style="color: #FF8D1B; margin-right: 8px;"></i>Edit Data Film</h2>
      <p>Mengubah data film: <strong>{{ $film->title }}</strong></p>
    </div>
    <div class="col-sm-4 text-right" style="padding-top: 10px;">
      <a href="{{ route('admin.films.index') }}" class="btn btn-default">
        <i class="fa fa-arrow-left"></i> Kembali ke Daftar Film
      </a>
    </div>
  </div>
</div>

<div class="admin-card">
  <form action="{{ route('admin.films.update', $film->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">
      <!-- Kolom Kiri: Informasi Film -->
      <div class="col-md-7">
        <div class="form-group @error('title') has-error @enderror">
          <label for="title" style="font-weight: 600;">Judul Film <span class="text-danger">*</span></label>
          <input 
            type="text" 
            name="title" 
            id="title" 
            class="form-control" 
            value="{{ old('title', $film->title) }}" 
            required
          >
          @error('title')
            <span class="help-block">{{ $message }}</span>
          @enderror
        </div>

        <div class="row">
          <div class="col-sm-6">
            <div class="form-group @error('year') has-error @enderror">
              <label for="year" style="font-weight: 600;">Tahun Rilis <span class="text-danger">*</span></label>
              <input 
                type="number" 
                name="year" 
                id="year" 
                class="form-control" 
                min="1900" 
                max="{{ date('Y') + 5 }}" 
                value="{{ old('year', $film->year) }}" 
                required
              >
              @error('year')
                <span class="help-block">{{ $message }}</span>
              @enderror
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group @error('genre_id') has-error @enderror">
              <label for="genre_id" style="font-weight: 600;">Genre Film <span class="text-danger">*</span></label>
              <select name="genre_id" id="genre_id" class="form-control" required>
                <option value="">-- Pilih Genre --</option>
                @foreach($genres as $genre)
                  <option value="{{ $genre->id }}" {{ old('genre_id', $film->genre_id) == $genre->id ? 'selected' : '' }}>
                    {{ $genre->name }}
                  </option>
                @endforeach
              </select>
              @error('genre_id')
                <span class="help-block">{{ $message }}</span>
              @enderror
            </div>
          </div>
        </div>

        <div class="form-group @error('sinopsis') has-error @enderror">
          <label for="sinopsis" style="font-weight: 600;">Sinopsis / Deskripsi <span class="text-danger">*</span></label>
          <textarea 
            name="sinopsis" 
            id="sinopsis" 
            class="form-control" 
            rows="6" 
            required
          >{{ old('sinopsis', $film->sinopsis) }}</textarea>
          @error('sinopsis')
            <span class="help-block">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group @error('poster') has-error @enderror">
          <label for="poster" style="font-weight: 600;">Ganti Poster Film <small class="text-muted">(Biarkan kosong jika tidak ingin mengubah)</small></label>
          
          @if($film->poster)
            <div style="margin-bottom: 12px; display: flex; align-items: center; gap: 15px;">
              <img src="{{ asset($film->poster) }}" alt="{{ $film->title }}" style="width: 80px; height: 110px; object-fit: cover; border-radius: 4px; border: 1px solid #cbd5e1;">
              <span class="text-muted" style="font-size: 13px;">Poster saat ini aktif. Unggah file baru di bawah ini untuk menggantinya.</span>
            </div>
          @endif

          <input 
            type="file" 
            name="poster" 
            id="poster" 
            class="form-control" 
            accept="image/jpeg,image/png,image/jpg,image/webp"
          >
          <small class="text-muted" style="display: block; margin-top: 5px;">
            <i class="fa fa-info-circle"></i> Format yang diizinkan: JPG, JPEG, PNG, WEBP. Maksimal: 2 MB. File poster lama otomatis dihapus saat diganti.
          </small>
          @error('poster')
            <span class="help-block">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <!-- Kolom Kanan: Pilihan Cast / Aktor (Many-to-Many) -->
      <div class="col-md-5">
        <div class="form-group">
          <label style="font-weight: 600; display: block;">
            Pilih Cast / Pemeran Film <small class="text-muted">(Multi-pilih)</small>
          </label>
          <p class="text-muted" style="font-size: 13px;">Centang aktor/aktris yang berperan dalam film ini:</p>
          
          <div style="max-height: 420px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 4px; padding: 12px; background: #f8fafc;">
            @php 
              $currentCastIds = old('cast_ids', $selectedCastIds ?? []); 
            @endphp
            @forelse($casts as $cast)
              <div class="checkbox" style="margin-top: 5px; margin-bottom: 5px;">
                <label style="font-size: 14px;">
                  <input 
                    type="checkbox" 
                    name="cast_ids[]" 
                    value="{{ $cast->id }}"
                    {{ in_array($cast->id, $currentCastIds) ? 'checked' : '' }}
                  >
                  <strong>{{ $cast->name }}</strong> 
                  @if($cast->age) <span class="text-muted">({{ $cast->age }} thn)</span> @endif
                </label>
              </div>
            @empty
              <p class="text-muted">Belum ada data cast.</p>
            @endforelse
          </div>
          @error('cast_ids')
            <span class="text-danger" style="font-size: 13px;">{{ $message }}</span>
          @enderror
        </div>
      </div>
    </div>

    <hr style="margin-top: 25px; margin-bottom: 20px;">

    <div>
      <button type="submit" class="btn btn-orange" style="padding: 10px 25px; font-size: 15px;">
        <i class="fa fa-save"></i> Perbarui Film
      </button>
      <a href="{{ route('admin.films.index') }}" class="btn btn-default" style="padding: 10px 20px; font-size: 15px; margin-left: 10px;">
        Batal
      </a>
    </div>
  </form>
</div>
@endsection

