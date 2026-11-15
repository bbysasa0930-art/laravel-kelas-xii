@extends('admin.layouts.app')

@section('title', 'Edit Cast: ' . $cast->name)

@section('content')
<div class="page-header-box">
  <div class="row">
    <div class="col-sm-8">
      <h2><i class="fa fa-pencil-square" style="color: #FF8D1B; margin-right: 8px;"></i>Edit Data Cast</h2>
      <p>Mengubah informasi cast: <strong>{{ $cast->name }}</strong></p>
    </div>
    <div class="col-sm-4 text-right" style="padding-top: 10px;">
      <a href="{{ route('admin.casts.index') }}" class="btn btn-default">
        <i class="fa fa-arrow-left"></i> Kembali ke Daftar Cast
      </a>
    </div>
  </div>
</div>

<div class="admin-card" style="max-width: 750px;">
  <form action="{{ route('admin.casts.update', $cast->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="form-group @error('name') has-error @enderror">
      <label for="name" style="font-weight: 600;">Nama Lengkap Cast <span class="text-danger">*</span></label>
      <input 
        type="text" 
        name="name" 
        id="name" 
        class="form-control" 
        value="{{ old('name', $cast->name) }}" 
        required
      >
      @error('name')
        <span class="help-block">{{ $message }}</span>
      @enderror
    </div>

    <div class="form-group @error('age') has-error @enderror">
      <label for="age" style="font-weight: 600;">Umur <span class="text-danger">*</span></label>
      <input 
        type="number" 
        name="age" 
        id="age" 
        class="form-control" 
        min="1" 
        max="130" 
        value="{{ old('age', $cast->age) }}" 
        required
      >
      @error('age')
        <span class="help-block">{{ $message }}</span>
      @enderror
    </div>

    <div class="form-group @error('bio') has-error @enderror">
      <label for="bio" style="font-weight: 600;">Biografi Singkat <span class="text-danger">*</span></label>
      <textarea 
        name="bio" 
        id="bio" 
        class="form-control" 
        rows="4" 
        required
      >{{ old('bio', $cast->bio) }}</textarea>
      @error('bio')
        <span class="help-block">{{ $message }}</span>
      @enderror
    </div>

    <div class="form-group @error('foto') has-error @enderror">
      <label for="foto" style="font-weight: 600;">Ganti Foto Profil <small class="text-muted">(Biarkan kosong jika tidak ingin mengubah)</small></label>
      
      @if($cast->foto)
        <div style="margin-bottom: 12px; display: flex; align-items: center; gap: 15px;">
          <img src="{{ asset($cast->foto) }}" alt="{{ $cast->name }}" style="width: 70px; height: 70px; object-fit: cover; border-radius: 50%; border: 2px solid #cbd5e1;">
          <span class="text-muted" style="font-size: 13px;">Foto saat ini aktif. Unggah file baru untuk menggantinya.</span>
        </div>
      @endif

      <input 
        type="file" 
        name="foto" 
        id="foto" 
        class="form-control" 
        accept="image/jpeg,image/png,image/jpg,image/webp"
      >
      <small class="text-muted" style="display: block; margin-top: 5px;">
        <i class="fa fa-info-circle"></i> Format yang diizinkan: JPG, JPEG, PNG, WEBP. Maksimal: 2 MB.
      </small>
      @error('foto')
        <span class="help-block">{{ $message }}</span>
      @enderror
    </div>

    <hr style="margin-top: 25px; margin-bottom: 20px;">

    <div>
      <button type="submit" class="btn btn-orange" style="padding: 10px 25px; font-size: 15px;">
        <i class="fa fa-save"></i> Perbarui Cast
      </button>
      <a href="{{ route('admin.casts.index') }}" class="btn btn-default" style="padding: 10px 20px; font-size: 15px; margin-left: 10px;">
        Batal
      </a>
    </div>
  </form>
</div>
@endsection

