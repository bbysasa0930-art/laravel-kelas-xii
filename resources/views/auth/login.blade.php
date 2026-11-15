@extends('templates.index')

@section('content')
<div class="faq">
  <div class="container">
    <div class="agileits-news-top">
      <ol class="breadcrumb">
        <li><a href="{{ route('home') }}">Home</a></li>
        <li class="active">Login</li>
      </ol>
    </div>

    <div class="w3layouts-login" style="max-width: 480px; margin: 40px auto; padding: 30px; background: #ffffff; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-top: 4px solid #FF8D1B; border-radius: 4px;">
      <h3 style="margin-top: 0; margin-bottom: 25px; text-align: center; color: #212121; font-weight: 600;">
        <i class="fa fa-lock" style="color: #FF8D1B; margin-right: 8px;"></i>Masuk ke Akun Anda
      </h3>

      {{-- Pesan Sukses (misal setelah registrasi atau logout) --}}
      @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert" style="border-radius: 3px;">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <i class="fa fa-check-circle" style="margin-right: 5px;"></i>{{ session('success') }}
        </div>
      @endif

      {{-- Pesan Error Global / Flash --}}
      @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert" style="border-radius: 3px;">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <i class="fa fa-exclamation-triangle" style="margin-right: 5px;"></i>{{ session('error') }}
        </div>
      @endif

      {{-- Pesan Error Validasi Login (Email atau password salah) --}}
      @if($errors->has('login'))
        <div class="alert alert-danger" role="alert" style="border-radius: 3px;">
          <i class="fa fa-times-circle" style="margin-right: 5px;"></i>{{ $errors->first('login') }}
        </div>
      @endif

      <form action="{{ route('login.post') }}" method="POST">
        {{-- Token Proteksi CSRF --}}
        @csrf

        {{-- Input Email atau Username --}}
        <div class="form-group" style="margin-bottom: 20px;">
          <label for="login" style="font-weight: 600; color: #333;">Email atau Username <span style="color: #e74c3c;">*</span></label>
          <div class="input-group">
            <span class="input-group-addon" style="background: #f7f7f7;"><i class="fa fa-user"></i></span>
            <input 
              type="text" 
              name="login" 
              id="login" 
              class="form-control" 
              placeholder="Masukkan email atau username" 
              value="{{ old('login') }}" 
              required 
              autofocus
              style="height: 42px; font-size: 14px;"
            >
          </div>
          @error('login')
            @if($message !== 'Email atau password salah')
              <span class="text-danger" style="font-size: 13px;">{{ $message }}</span>
            @endif
          @enderror
        </div>

        {{-- Input Password --}}
        <div class="form-group" style="margin-bottom: 25px;">
          <label for="password" style="font-weight: 600; color: #333;">Password <span style="color: #e74c3c;">*</span></label>
          <div class="input-group">
            <span class="input-group-addon" style="background: #f7f7f7;"><i class="fa fa-key"></i></span>
            <input 
              type="password" 
              name="password" 
              id="password" 
              class="form-control" 
              placeholder="Masukkan password" 
              required
              style="height: 42px; font-size: 14px;"
            >
          </div>
          @error('password')
            <span class="text-danger" style="font-size: 13px;">{{ $message }}</span>
          @enderror
        </div>

        {{-- Tombol Submit Login --}}
        <button 
          type="submit" 
          class="btn btn-block" 
          style="background: #FF8D1B; color: #fff; font-size: 16px; font-weight: 600; padding: 12px; border: none; border-radius: 4px; transition: background 0.3s;"
          onmouseover="this.style.background='#e0760d'"
          onmouseout="this.style.background='#FF8D1B'"
        >
          <i class="fa fa-sign-in" style="margin-right: 6px;"></i> Masuk
        </button>
      </form>

      <hr style="margin: 25px 0;">

      <div style="text-align: center; font-size: 14px; color: #666;">
        Belum memiliki akun? 
        <a href="{{ route('register') }}" style="color: #FF8D1B; font-weight: 600; text-decoration: underline;">
          Daftar sekarang
        </a>
      </div>
    </div>
  </div>
</div>
@endsection

