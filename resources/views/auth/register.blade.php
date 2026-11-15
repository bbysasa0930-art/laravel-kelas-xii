@extends('templates.index')

@section('content')
<div class="faq">
  <div class="container">
    <div class="agileits-news-top">
      <ol class="breadcrumb">
        <li><a href="{{ route('home') }}">Home</a></li>
        <li class="active">Register</li>
      </ol>
    </div>

    <div class="w3layouts-register" style="max-width: 520px; margin: 40px auto; padding: 30px; background: #ffffff; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-top: 4px solid #FF8D1B; border-radius: 4px;">
      <h3 style="margin-top: 0; margin-bottom: 25px; text-align: center; color: #212121; font-weight: 600;">
        <i class="fa fa-user-plus" style="color: #FF8D1B; margin-right: 8px;"></i>Daftar Akun Baru
      </h3>

      {{-- Tampilkan ringkasan error jika ada kesalahan validasi --}}
      @if ($errors->any())
        <div class="alert alert-danger" style="border-radius: 3px;">
          <strong style="display: block; margin-bottom: 5px;"><i class="fa fa-exclamation-circle"></i> Terdapat kesalahan pada data yang diisi:</strong>
          <ul style="margin: 0; padding-left: 20px;">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('register.post') }}" method="POST">
        {{-- Token Proteksi CSRF --}}
        @csrf

        {{-- Field Nama / Username --}}
        <div class="form-group" style="margin-bottom: 20px;">
          <label for="name" style="font-weight: 600; color: #333;">Nama / Username <span style="color: #e74c3c;">*</span></label>
          <div class="input-group">
            <span class="input-group-addon" style="background: #f7f7f7;"><i class="fa fa-user"></i></span>
            <input 
              type="text" 
              name="name" 
              id="name" 
              class="form-control @error('name') is-invalid @enderror" 
              placeholder="Masukkan nama atau username" 
              value="{{ old('name') }}" 
              required
              autofocus
              style="height: 42px; font-size: 14px;"
            >
          </div>
          @error('name')
            <span class="text-danger" style="font-size: 13px; font-weight: 500;">{{ $message }}</span>
          @enderror
        </div>

        {{-- Field Email --}}
        <div class="form-group" style="margin-bottom: 20px;">
          <label for="email" style="font-weight: 600; color: #333;">Alamat Email <span style="color: #e74c3c;">*</span></label>
          <div class="input-group">
            <span class="input-group-addon" style="background: #f7f7f7;"><i class="fa fa-envelope"></i></span>
            <input 
              type="email" 
              name="email" 
              id="email" 
              class="form-control @error('email') is-invalid @enderror" 
              placeholder="contoh@domain.com" 
              value="{{ old('email') }}" 
              required
              style="height: 42px; font-size: 14px;"
            >
          </div>
          @error('email')
            <span class="text-danger" style="font-size: 13px; font-weight: 500;">{{ $message }}</span>
          @enderror
        </div>

        {{-- Field Password --}}
        <div class="form-group" style="margin-bottom: 20px;">
          <label for="password" style="font-weight: 600; color: #333;">Password <span style="color: #e74c3c;">*</span></label>
          <div class="input-group">
            <span class="input-group-addon" style="background: #f7f7f7;"><i class="fa fa-key"></i></span>
            <input 
              type="password" 
              name="password" 
              id="password" 
              class="form-control @error('password') is-invalid @enderror" 
              placeholder="Minimal 8 karakter" 
              required
              style="height: 42px; font-size: 14px;"
            >
          </div>
          @error('password')
            <span class="text-danger" style="font-size: 13px; font-weight: 500;">{{ $message }}</span>
          @enderror
        </div>

        {{-- Field Konfirmasi Password --}}
        <div class="form-group" style="margin-bottom: 25px;">
          <label for="password_confirmation" style="font-weight: 600; color: #333;">Konfirmasi Password <span style="color: #e74c3c;">*</span></label>
          <div class="input-group">
            <span class="input-group-addon" style="background: #f7f7f7;"><i class="fa fa-check-square-o"></i></span>
            <input 
              type="password" 
              name="password_confirmation" 
              id="password_confirmation" 
              class="form-control @error('password_confirmation') is-invalid @enderror" 
              placeholder="Ulangi password di atas" 
              required
              style="height: 42px; font-size: 14px;"
            >
          </div>
          @error('password_confirmation')
            <span class="text-danger" style="font-size: 13px; font-weight: 500;">{{ $message }}</span>
          @enderror
        </div>

        {{-- Tombol Submit Register --}}
        <button 
          type="submit" 
          class="btn btn-block" 
          style="background: #FF8D1B; color: #fff; font-size: 16px; font-weight: 600; padding: 12px; border: none; border-radius: 4px; transition: background 0.3s;"
          onmouseover="this.style.background='#e0760d'"
          onmouseout="this.style.background='#FF8D1B'"
        >
          <i class="fa fa-user-plus" style="margin-right: 6px;"></i> Daftar Sekarang
        </button>
      </form>

      <hr style="margin: 25px 0;">

      <div style="text-align: center; font-size: 14px; color: #666;">
        Sudah memiliki akun? 
        <a href="{{ route('login') }}" style="color: #FF8D1B; font-weight: 600; text-decoration: underline;">
          Masuk di sini
        </a>
      </div>
    </div>
  </div>
</div>
@endsection

