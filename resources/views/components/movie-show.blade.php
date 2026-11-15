@extends('templates.index')

@push('style')
  <link rel="stylesheet" type="text/css" href="{{ asset('template/list-css/table-style.css') }}" />
  <link rel="stylesheet" type="text/css" href="{{ asset('template/list-css/basictable.css') }}" />
  <style>
    /* Styling Kartu Cast Kecil */
    .cast-card-item {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 6px;
      padding: 10px;
      display: flex;
      align-items: center;
      gap: 12px;
      height: 100%;
      box-shadow: 0 1px 3px rgba(0,0,0,0.06);
      transition: all 0.2s ease;
    }
    .cast-card-item:hover {
      border-color: #FF8D1B;
      box-shadow: 0 3px 8px rgba(255,141,27,0.15);
      transform: translateY(-2px);
    }
    .cast-avatar-img {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      object-fit: cover;
      flex-shrink: 0;
      border: 2px solid #FF8D1B;
    }
    .cast-avatar-placeholder {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      background: #f1f5f9;
      color: #64748b;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      font-size: 20px;
      border: 2px solid #e2e8f0;
    }
    .cast-info-box {
      overflow: hidden;
      line-height: 1.3;
    }
    .cast-info-name {
      font-size: 14px;
      font-weight: 700;
      color: #1e293b;
      margin: 0;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .cast-info-role {
      font-size: 12px;
      color: #64748b;
      margin: 3px 0 0;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
  </style>
@endpush

@push('scripts')
  <script type="text/javascript" src="{{ asset('template/list-js/jquery.basictable.min.js') }}"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $('#table-breakpoint').basictable({
        breakpoint: 768
      });
    });
  </script>
@endpush

@section('content')
<div class="container">
  <br />
  <!-- Breadcrumb -->
  <div class="agileits-single-top">
    <ol class="breadcrumb">
      <li><a href="{{ route('home') }}">Home</a></li>
      <li><a href="{{ route('movies') }}">Movies</a></li>
      <li class="active">{{ $film->title }}</li>
    </ol>
  </div>

  <div class="single-page-agile-info">
    <div class="show-top-grids-w3lagile">
      <div class="col-sm-8 single-left">
        <div class="song">
          <div class="single-right-grids">
            <!-- Poster Film -->
            <div class="col-md-4 single-right-grid-left">
              <a href="{{ route('movies.show', $film->id) }}">
                <img src="{{ asset($film->poster) }}" alt="{{ $film->title }}" style="width: 100%; border-radius: 4px; box-shadow: 0 4px 10px rgba(0,0,0,0.15);" />
              </a>
            </div>

            <!-- Informasi Film & Daftar Cast -->
            <div class="col-md-8 single-right-grid-right">
              <div class="song-info">
                <h2 style="margin-top: 0;">{{ $film->title }}</h2>	
                <p class="author">
                  <span class="label label-warning" style="background: #FF8D1B; font-size: 13px;">{{ $film->year }}</span>
                  <span class="label label-default" style="font-size: 13px; margin-left: 5px;">{{ $film->genre->name ?? 'Genre' }}</span>
                </p>
                <p class="views" style="text-align: justify; margin-top: 15px; color: #555; line-height: 1.6;">
                  {{ $film->sinopsis }}
                </p>
              </div>

              <!-- Bagian "Cast & Pemeran Film" -->
              <div class="cast-section" style="margin-top: 25px;">
                <h4 style="font-size: 18px; font-weight: 700; color: #FF8D1B; border-bottom: 2px solid #FF8D1B; padding-bottom: 8px; margin-bottom: 15px;">
                  <i class="fa fa-users"></i> Cast & Pemeran Film
                </h4>

                {{-- Jika film belum memiliki cast --}}
                @if($casts->isEmpty())
                  <div class="alert alert-info" style="font-size: 14px; background: #e0f2fe; color: #0369a1; border-color: #bae6fd; border-radius: 4px;">
                    <i class="fa fa-info-circle" style="margin-right: 5px;"></i> Data cast belum tersedia
                  </div>
                @else
                  <!-- Deretan Kartu Cast Kecil Responsif -->
                  <div class="row" style="margin-left: -5px; margin-right: -5px; margin-bottom: 20px;">
                    @foreach ($casts as $cast)
                      <div class="col-xs-12 col-sm-6" style="padding: 5px; margin-bottom: 10px;">
                        <div class="cast-card-item">
                          @if(!empty($cast->cast_foto))
                            <img src="{{ asset($cast->cast_foto) }}" alt="{{ $cast->cast_name }}" class="cast-avatar-img">
                          @else
                            <div class="cast-avatar-placeholder">
                              <i class="fa fa-user"></i>
                            </div>
                          @endif
                          <div class="cast-info-box">
                            <p class="cast-info-name" title="{{ $cast->cast_name }}">{{ $cast->cast_name }}</p>
                            <p class="cast-info-role" title="{{ $cast->role_name ?? 'Pemeran' }}">
                              Sebagai: <strong style="color: #FF8D1B;">{{ $cast->role_name ?? '-' }}</strong>
                            </p>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>

                  <!-- Tabel Detail Cast -->
                  <div class="agile-news-table" style="margin-top: 10px;">
                    <table id="table-breakpoint">
                      <thead>
                        <tr>
                          <th style="width: 50px; text-align: center;">Foto</th>
                          <th>Nama Cast</th>
                          <th>Peran (Karakter)</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($casts as $cast)
                          <tr>
                            <td class="w3-list-info" style="text-align: center;">
                              @if(!empty($cast->cast_foto))
                                <img src="{{ asset($cast->cast_foto) }}" alt="{{ $cast->cast_name }}" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                              @else
                                <i class="fa fa-user-circle text-muted" style="font-size: 20px;"></i>
                              @endif
                            </td>
                            <td class="w3-list-info">
                              <strong>{{ $cast->cast_name }}</strong>
                              @if($cast->cast_age)
                                <small class="text-muted">({{ $cast->cast_age }} thn)</small>
                              @endif
                            </td>
                            <td class="w3-list-info">
                              <span style="color: #FF8D1B; font-weight: 600;">{{ $cast->role_name ?? '-' }}</span>
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                @endif
              </div>

            </div>
            <div class="clearfix"> </div>
          </div>
        </div>

        <div class="song-grid-right">
          <div class="share">
            <h5>Share this</h5>
            <div class="single-agile-shar-buttons">
              <ul>
                <li>
                  <div class="fb-like" data-href="https://www.facebook.com/w3layouts" data-layout="button_count" data-action="like" data-size="small" data-show-faces="false" data-share="false"></div>
                </li>
                <li>
                  <div class="fb-share-button" data-href="https://www.facebook.com/w3layouts" data-layout="button_count" data-size="small" data-mobile-iframe="true"><a class="fb-xfbml-parse-ignore" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fwww.facebook.com%2Fw3layouts&amp;src=sdkpreparse">Share</a></div>
                </li>
                <li class="news-twitter">
                  <a href="https://twitter.com/w3layouts" class="twitter-follow-button" data-show-count="false">Follow @w3layouts</a>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="clearfix"> </div>

        <!-- Komentar Film -->
        <div class="all-comments">
          <div class="all-comments-info">
            <a href="#komentar">Komentar ({{ $comments->count() }})</a>
          </div>

          {{-- Pesan sukses setelah komentar berhasil dikirim --}}
          @if(session('success_comment'))
            <div class="alert alert-success alert-dismissible" role="alert" style="border-radius: 4px; margin-bottom: 15px;">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <i class="fa fa-check-circle" style="margin-right: 5px;"></i> {{ session('success_comment') }}
            </div>
          @endif

          {{-- Form Kirim Komentar (hanya tampil jika sudah login) --}}
          <div id="komentar" style="margin-bottom: 25px; padding: 20px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
            @if(Auth::check() || session()->has('user_id'))
              {{-- ===== USER SUDAH LOGIN: tampilkan form komentar ===== --}}
              <h5 style="margin-top: 0; font-weight: 700; color: #1e293b;">
                <i class="fa fa-comment-o" style="color: #FF8D1B;"></i> Tulis Komentar
              </h5>
              <form action="{{ route('comments.store', $film->id) }}" method="POST">
                @csrf

                {{-- Pesan error validasi --}}
                @if($errors->has('comment'))
                  <div class="alert alert-danger" style="font-size: 13px; padding: 8px 12px; border-radius: 4px; margin-bottom: 10px;">
                    <i class="fa fa-exclamation-circle"></i> {{ $errors->first('comment') }}
                  </div>
                @endif

                {{-- Rating (opsional) --}}
                <div class="form-group" style="margin-bottom: 12px;">
                  <label style="font-size: 13px; font-weight: 600; color: #374151;">
                    Rating (opsional):
                  </label>
                  <select name="rating" class="form-control" style="width: 120px; height: 34px; font-size: 13px; display: inline-block;">
                    <option value="">-- Pilih --</option>
                    @for($i = 1; $i <= 5; $i++)
                      <option value="{{ $i }}" {{ old('rating') == $i ? 'selected' : '' }}>
                        {{ $i }} ★
                      </option>
                    @endfor
                  </select>
                </div>

                {{-- Textarea komentar --}}
                <div class="form-group" style="margin-bottom: 12px;">
                  <textarea
                    name="comment"
                    class="form-control"
                    rows="3"
                    maxlength="500"
                    placeholder="Tulis komentar Anda... (maks. 500 karakter)"
                    style="font-size: 14px; border-radius: 4px; resize: vertical;"
                    required
                  >{{ old('comment') }}</textarea>
                  <small class="text-muted" style="font-size: 12px;">Maks. 500 karakter</small>
                </div>

                <button type="submit" class="btn" style="background: #FF8D1B; color: #fff; font-weight: 600; border: none; padding: 8px 20px; border-radius: 4px; font-size: 14px;">
                  <i class="fa fa-paper-plane-o"></i> Kirim Komentar
                </button>
              </form>
            @else
              {{-- ===== USER BELUM LOGIN: tampilkan pesan ajakan login ===== --}}
              <div style="text-align: center; padding: 10px 0;">
                <i class="fa fa-lock" style="font-size: 32px; color: #94a3b8; margin-bottom: 10px; display: block;"></i>
                <p style="color: #64748b; font-size: 14px; margin-bottom: 15px;">
                  Silakan login untuk memberikan komentar pada film ini.
                </p>
                {{-- Link login menyertakan URL film sebagai "intended" agar setelah login langsung balik ke sini --}}
                <a href="{{ route('login') }}"
                   class="btn"
                   style="background: #FF8D1B; color: #fff; font-weight: 600; border: none; padding: 9px 24px; border-radius: 4px; font-size: 14px; text-decoration: none;">
                  <i class="fa fa-sign-in"></i> Login untuk Berkomentar
                </a>
                <span style="margin: 0 8px; color: #94a3b8;">atau</span>
                <a href="{{ route('register') }}"
                   style="color: #FF8D1B; font-weight: 600; font-size: 14px;">
                  Daftar akun baru
                </a>
              </div>
            @endif
          </div>

          {{-- Daftar komentar yang sudah ada (bisa dilihat semua pengunjung) --}}
          <div class="media-grids">
            @forelse ($comments as $comment)
              @php
                // Format nama user dengan fallback 'Pengguna' jika id user tidak cocok di tabel users
                $userName = !empty($comment->user_name) ? $comment->user_name : (isset($comment->user->name) ? $comment->user->name : 'Pengguna');
                // Format tanggal komentar yang mudah dibaca, contoh: "27 Oktober 2026, 14:30"
                $commentDate = $comment->created_at ? \Carbon\Carbon::parse($comment->created_at)->translatedFormat('d F Y, H:i') : '-';
              @endphp
              <div class="media" style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
                <div class="media-left">
                  <a href="#">
                    <img src="{{ asset('storage/images/user.jpg') }}" title="{{ $userName }}" alt="{{ $userName }}" style="width: 45px; height: 45px; border-radius: 50%;" />
                  </a>
                </div>
                <div class="media-body">
                  <h5 style="margin-top: 0; color: #1e293b; font-weight: 700;">
                    {{ $userName }}
                    @if($comment->rating)
                      <span class="badge" style="background: #f59e0b; margin-left: 8px;"><i class="fa fa-star"></i> {{ $comment->rating }}</span>
                    @endif
                  </h5>
                  <p style="color: #4b5563; font-size: 14px; margin-top: 5px;">{{ $comment->comment }}</p>
                  <small class="text-muted" style="font-size: 12px;">
                    <i class="fa fa-clock-o"></i> {{ $commentDate }}
                  </small>
                </div>
              </div>
            @empty
              <p class="text-muted" style="font-size: 14px;">
                <i class="fa fa-comment-o"></i> Belum ada komentar untuk film ini. Jadilah yang pertama!
              </p>
            @endforelse
          </div>
        </div>
      </div>

      <!-- Sidebar: Similar Film By Genre -->
      <div class="col-md-4 single-right">
        <h3>Film Serupa</h3>
        @foreach ($filmByGenre as $simFilm)
          <div class="single-grid-right">
            <div class="single-right-grids">
              <div class="col-md-4 single-right-grid-left">
                <a href="{{ route('movies.show', $simFilm->id) }}">
                  <img src="{{ asset($simFilm->poster) }}" alt="{{ $simFilm->title }}" style="width: 100%; border-radius: 4px;" />
                </a>
              </div>
              <div class="col-md-8 single-right-grid-right">
                <a href="{{ route('movies.show', $simFilm->id) }}" class="title" style="font-weight: 700;">{{ $simFilm->title }}</a>
                <p class="author"><a href="#" class="author">{{ $simFilm->year }}</a></p>
                <p class="views" style="text-align: justify; margin-right: 10px">{{ Str::limit($simFilm->sinopsis, 90) }}</p>
              </div>
              <div class="clearfix"> </div>
            </div>
          </div>
        @endforeach
      </div>
      <div class="clearfix"> </div>
    </div>
  </div>	
</div>
@endsection
