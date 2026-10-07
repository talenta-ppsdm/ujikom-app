<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar | Sistem Uji Kompetensi</title>
  <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap-icons/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/register.css') }}">
</head>

<body class="login-page register-page">
  <main class="login-shell">
    <section class="login-brand" aria-labelledby="login-title">
      <div class="brand-mark"><img src="{{ asset('assets/images/logo-pkp-khaki.png') }}" alt="Logo Kementerian PKP"></div>
      <h1 id="login-title">Sistem Uji Kompetensi</h1>
      <p>Jabatan Fungsional Penata Kelola Perumahan</p>
      <span class="brand-year">PPSDM <b>&bull;</b> 2026 </span>
    </section>
    
    <section class="login-card register-card" aria-label="Form pendaftaran akun">
      <header class="login-card-header">
        <h2>DAFTAR AKUN</h2>
      </header>

      @if ($errors->any())
        <div class="alert-custom alert-custom-danger">
          <i class="bi bi-exclamation-triangle-fill alert-custom-icon"></i>
          <div class="alert-custom-content">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
              @endforeach
            </ul>
          </div>
        </div>
      @endif

      <form class="login-form register-form" action="{{ url('/registrasi') }}" method="POST">
        @csrf
        <div class="form-field">
          <label for="nip">NIP</label>
          <div class="input-wrap">
            <i class="bi bi-fingerprint" aria-hidden="true"></i>
            <input id="nip" name="nip" type="text" inputmode="numeric" placeholder="Masukkan NIP (18 digit)" autocomplete="username" required>
          </div>
        </div>

        <div class="form-field">
          <label for="name">NAMA</label>
          <div class="input-wrap">
            <i class="bi bi-person" aria-hidden="true"></i>
            <input id="name" name="name" type="text" placeholder="Masukkan nama lengkap" autocomplete="name" required>
          </div>
        </div>

        <div class="form-field">
          <label for="email">ALAMAT EMAIL</label>
          <div class="input-wrap">
            <i class="bi bi-envelope" aria-hidden="true"></i>
            <input id="email" name="email" type="email" placeholder="nama@instansi.go.id" autocomplete="email" required>
          </div>
        </div>

        <div class="form-field">
          <label for="password">KATA SANDI</label>
          <div class="input-wrap">
            <i class="bi bi-lock" aria-hidden="true"></i>
            <input id="password" name="password" type="password" placeholder="Masukkan kata sandi" autocomplete="new-password" required>
            <button class="password-toggle" type="button" aria-label="Tampilkan kata sandi" data-password-toggle>
              <i class="bi bi-eye" aria-hidden="true"></i>
            </button>
          </div>
        </div>

        <div class="form-field">
          <label for="password_confirmation">KONFIRMASI KATA SANDI</label>
          <div class="input-wrap">
            <i class="bi bi-lock" aria-hidden="true"></i>
            <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Ulangi kata sandi" autocomplete="new-password" required>
            <button class="password-toggle" type="button" aria-label="Tampilkan konfirmasi kata sandi" data-password-toggle>
              <i class="bi bi-eye" aria-hidden="true"></i>
            </button>
          </div>
        </div>

        <button class="login-submit" type="submit">Daftar</button>
      </form>

      <div class="login-divider"><span>atau</span></div>
      <button class="google-login" type="button">
        <span class="google-login-mark" aria-hidden="true">G</span>
        <span>Daftar dengan Google</span>
      </button>

      <p class="login-register-prompt">
        Sudah punya akun? <a class="login-link" href="{{ route('login') }}">Kembali ke Login</a>
      </p>
    </section>
  </main>

  <script>
    document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
      toggle.addEventListener('click', () => {
        const input = toggle.closest('.input-wrap').querySelector('input');
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        toggle.setAttribute('aria-label', isPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        toggle.querySelector('i').className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
      });
    });
  </script>
</body>

</html>
