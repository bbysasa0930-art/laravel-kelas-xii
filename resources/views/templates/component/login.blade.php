<div class="modal video-modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        Sign In & Sign Up
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>						
      </div>
      <section>
        <div class="modal-body">
          <div class="w3_login_module">
            <div class="module form-module">
              <div class="toggle"><i class="fa fa-times fa-pencil"></i>
                <div class="tooltip">Klik Daftar</div>
              </div>

              {{-- Form Login Modal --}}
              <div class="form">
                <h3>Login Akun Anda</h3>

                @if($errors->has('login'))
                  <div class="alert alert-danger" style="padding: 8px 12px; margin-bottom: 15px; font-size: 13px;">
                    {{ $errors->first('login') }}
                  </div>
                @endif

                <form action="{{ route('login.post') }}" method="post">
                  @csrf
                  <input type="text" name="login" placeholder="Email atau Username" value="{{ old('login') }}" required="">
                  <input type="password" name="password" placeholder="Password" required="">
                  <input type="submit" value="Login">
                </form>
              </div>

              {{-- Form Register Modal --}}
              <div class="form">
                <h3>Buat Akun Baru</h3>

                @if($errors->any() && !$errors->has('login'))
                  <div class="alert alert-danger" style="padding: 8px 12px; margin-bottom: 15px; font-size: 13px;">
                    <ul style="margin: 0; padding-left: 15px;">
                      @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                @endif

                <form action="{{ route('register.post') }}" method="post">
                  @csrf
                  <input type="text" name="name" placeholder="Nama / Username" value="{{ old('name') }}" required="">
                  <input type="email" name="email" placeholder="Alamat Email" value="{{ old('email') }}" required="">
                  <input type="password" name="password" placeholder="Password (min. 8 karakter)" required="">
                  <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" required="">
                  <input type="submit" value="Register">
                </form>
              </div>

              <div class="cta"><a href="{{ route('login') }}">Ke Halaman Login Penuh</a></div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>
<script>
  $('.toggle').click(function(){
    // Switches the Icon
    $(this).children('i').toggleClass('fa-pencil');
    // Switches the forms  
    $('.form').animate({
      height: "toggle",
      'padding-top': 'toggle',
      'padding-bottom': 'toggle',
      opacity: "toggle"
    }, "slow");
  });
</script>
