@extends('admin.layouts.app')

@section('title', 'Tambah Film Baru')

@section('content')
<div class="page-header-box">
  <div class="row">
    <div class="col-sm-8">
      <h2><i class="fa fa-plus-circle" style="color: #FF8D1B; margin-right: 8px;"></i>Tambah Film Baru</h2>
      <p>Isi formulir di bawah ini untuk menambahkan film baru ke katalog OneMovies.</p>
    </div>
    <div class="col-sm-4 text-right" style="padding-top: 10px;">
      <a href="{{ route('admin.films.index') }}" class="btn btn-default">
        <i class="fa fa-arrow-left"></i> Kembali ke Daftar Film
      </a>
    </div>
  </div>
</div>

<div class="admin-card">
  <form action="{{ route('admin.films.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row">
      <!-- Kolom Kiri: Informasi Utama Film -->
      <div class="col-md-7">
        <div class="form-group @error('title') has-error @enderror">
          <label for="title" style="font-weight: 600;">Judul Film <span class="text-danger">*</span></label>
          <input 
            type="text" 
            name="title" 
            id="title" 
            class="form-control" 
            placeholder="Contoh: The Dark Knight" 
            value="{{ old('title') }}" 
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
                placeholder="Contoh: 2024" 
                min="1900" 
                max="{{ date('Y') + 5 }}" 
                value="{{ old('year', date('Y')) }}" 
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
                  <option value="{{ $genre->id }}" {{ old('genre_id') == $genre->id ? 'selected' : '' }}>
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
            placeholder="Tuliskan ringkasan cerita atau sinopsis film..." 
            required
          >{{ old('sinopsis') }}</textarea>
          @error('sinopsis')
            <span class="help-block">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group @error('poster') has-error @enderror">
          <label for="poster" style="font-weight: 600;">File Poster Film <span class="text-danger">*</span></label>
          <input 
            type="file" 
            name="poster" 
            id="poster" 
            class="form-control" 
            accept="image/jpeg,image/png,image/jpg,image/webp" 
            required
          >
          <small class="text-muted" style="display: block; margin-top: 5px;">
            <i class="fa fa-info-circle"></i> Format yang diizinkan: JPG, JPEG, PNG, WEBP. Ukuran maksimal: 2 MB.
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
            Pilih Cast / Pemeran Film <small class="text-muted">(Opsional / Multi-pilih)</small>
          </label>
          <p class="text-muted" style="font-size: 13px;">Centang aktor/aktris yang berperan dalam film ini:</p>
          
          <div style="max-height: 380px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 4px; padding: 12px; background: #f8fafc;">
            @php $oldCasts = old('cast_ids', []); @endphp
            @forelse($casts as $cast)
              <div class="checkbox" style="margin-top: 5px; margin-bottom: 5px;">
                <label style="font-size: 14px;">
                  <input 
                    type="checkbox" 
                    name="cast_ids[]" 
                    value="{{ $cast->id }}"
                    {{ in_array($cast->id, $oldCasts) ? 'checked' : '' }}
                  >
                  <strong>{{ $cast->name }}</strong> 
                  @if($cast->age) <span class="text-muted">({{ $cast->age }} thn)</span> @endif
                </label>
              </div>
            @empty
              <p class="text-muted">Belum ada data cast. Silakan tambahkan cast terlebih dahulu.</p>
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
        <i class="fa fa-save"></i> Simpan Film
      </button>
      <a href="{{ route('admin.films.index') }}" class="btn btn-default" style="padding: 10px 20px; font-size: 15px; margin-left: 10px;">
        Batal
      </a>
    </div>
  </form>
</div>
@endsection

