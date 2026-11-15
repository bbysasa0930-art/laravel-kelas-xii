@extends('admin.layouts.app')

@section('title', 'Tambah Cast Baru')

@section('content')
<div class="page-header-box">
  <div class="row">
    <div class="col-sm-8">
      <h2><i class="fa fa-plus-circle" style="color: #FF8D1B; margin-right: 8px;"></i>Tambah Cast Baru</h2>
      <p>Tambahkan aktor atau aktris baru ke sistem OneMovies.</p>
    </div>
    <div class="col-sm-4 text-right" style="padding-top: 10px;">
      <a href="{{ route('admin.casts.index') }}" class="btn btn-default">
        <i class="fa fa-arrow-left"></i> Kembali ke Daftar Cast
      </a>
    </div>
  </div>
</div>

<div class="admin-card" style="max-width: 750px;">
  <form action="{{ route('admin.casts.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="form-group @error('name') has-error @enderror">
      <label for="name" style="font-weight: 600;">Nama Lengkap Cast <span class="text-danger">*</span></label>
      <input 
        type="text" 
        name="name" 
        id="name" 
        class="form-control" 
        placeholder="Contoh: Christian Bale" 
        value="{{ old('name') }}" 
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
        placeholder="Contoh: 45" 
        min="1" 
        max="130" 
        value="{{ old('age') }}" 
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
        placeholder="Tuliskan latar belakang singkat cast..." 
        required
      >{{ old('bio') }}</textarea>
      @error('bio')
        <span class="help-block">{{ $message }}</span>
      @enderror
    </div>

    <div class="form-group @error('foto') has-error @enderror">
      <label for="foto" style="font-weight: 600;">Foto Profil <small class="text-muted">(Opsional)</small></label>
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
        <i class="fa fa-save"></i> Simpan Cast
      </button>
      <a href="{{ route('admin.casts.index') }}" class="btn btn-default" style="padding: 10px 20px; font-size: 15px; margin-left: 10px;">
        Batal
      </a>
    </div>
  </form>
</div>
@endsection

