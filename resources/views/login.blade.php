<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Sistem Uji Kompetensi</title>
  <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap-icons/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
</head>

<body class="login-page">
  <main class="login-shell">
    <section class="login-brand" aria-labelledby="login-title">
      <div class="brand-mark"><img src="{{ asset('assets/images/logo-pkp-khaki.png') }}" alt="Logo Kementerian PKP"></div>
      <h1 id="login-title">Sistem Uji Kompetensi</h1>
      <p>Jabatan Fungsional Penata Kelola Perumahan</p>
      <span class="brand-year">PPSDM <b>&bull;</b> 2026 </span>
    </section>

    <section class="login-card" aria-label="Form masuk ke sistem">
      <header class="login-card-header">
        <h2>LOGIN APLIKASI</h2>
      </header>

      @if (session('error'))
        <div class="alert-custom alert-custom-danger">
          <i class="bi bi-exclamation-triangle-fill alert-custom-icon"></i>
          <div class="alert-custom-content">{{ session('error') }}</div>
        </div>
      @endif

      <form class="login-form" action="{{ url('/login') }}" method="POST">
        @csrf
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
            <input id="password" name="password" type="password" placeholder="Masukkan kata sandi" autocomplete="current-password" required>
            <button class="password-toggle" type="button" aria-label="Tampilkan kata sandi" data-password-toggle>
              <i class="bi bi-eye" aria-hidden="true"></i>
            </button>
          </div>
        </div>

        <button class="login-submit" type="submit">Masuk</button>
      </form>

      <div class="demo-divider"><span>&mdash; Demo Akun &mdash;</span></div>
      <div class="demo-accounts">
        <button class="demo-account demo-admin" type="button" data-demo-email="admin@pkp.go.id" data-demo-password="admin123">
          <strong>ADMIN</strong>
          <span>admin@pkp.go.id</span>
        </button>
        <button class="demo-account demo-participant" type="button" data-demo-email="puteri@pkp.go.id" data-demo-password="peserta2026">
          <strong>PESERTA</strong>
          <span>puteri@pkp.go.id</span>
        </button>
      </div>
    </section>
  </main>

  <script>
    const passwordInput = document.querySelector('#password');
    const passwordToggle = document.querySelector('[data-password-toggle]');

    passwordToggle.addEventListener('click', () => {
      const isPassword = passwordInput.type === 'password';
      passwordInput.type = isPassword ? 'text' : 'password';
      passwordToggle.setAttribute('aria-label', isPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
      passwordToggle.querySelector('i').className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
    });

    document.querySelectorAll('[data-demo-email]').forEach((button) => {
      button.addEventListener('click', () => {
        document.querySelector('#email').value = button.dataset.demoEmail;
        document.querySelector('#password').value = button.dataset.demoPassword;
        document.querySelector('#password').focus();
      });
    });
  </script>
</body>

</html>